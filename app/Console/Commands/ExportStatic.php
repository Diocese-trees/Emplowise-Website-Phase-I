<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

/**
 * Renders the public pages to plain HTML so they can be served by a static
 * host such as GitHub Pages. The tools keep working because the deterministic
 * engines also ship as browser JavaScript; the audit form can be pointed at
 * Web3Forms when an access key is configured.
 */
final class ExportStatic extends Command
{
    protected $signature = 'emplowise:export-static
        {path=build/static : Directory to write the static site into}
        {--base= : Public base URL, for example https://example.github.io/repo}
        {--audit-key= : Web3Forms access key for the audit form}';

    protected $description = 'Render the public site to static HTML for a free static host';

    /** @var array<string, string> */
    private array $pages = [
        '/' => 'index.html',
        '/what-we-are' => 'what-we-are/index.html',
        '/what-we-do' => 'what-we-do/index.html',
        '/pricing' => 'pricing/index.html',
        '/student-hub' => 'student-hub/index.html',
        '/faq' => 'faq/index.html',
        '/contact' => 'contact/index.html',
        '/team' => 'team/index.html',
        '/ai-automation' => 'ai-automation/index.html',
        '/free-hr-audit' => 'free-hr-audit/index.html',
    ];

    public function handle(): int
    {
        $base = rtrim((string) ($this->option('base') ?: config('static.base_url')), '/');
        $key = trim((string) ($this->option('audit-key') ?: config('static.web3forms_key')));

        if (! preg_match('#^https?://[^/]+#', $base)) {
            $this->error('Set a valid --base URL, for example https://user.github.io/repo');

            return self::FAILURE;
        }

        $target = $this->argument('path');
        $target = str_starts_with($target, '/') ? $target : base_path($target);

        if (in_array(rtrim($target, '/'), [base_path(), public_path(), '/'], true)) {
            $this->error('Refusing to write into '.$target);

            return self::FAILURE;
        }

        // Rendering runs the web middleware, which starts a session. A static
        // export has no session or cache to persist, and a fresh checkout has
        // no migrated database, so keep both in memory.
        config([
            'app.url' => $base,
            'app.asset_url' => $base,
            'session.driver' => 'array',
            'cache.default' => 'array',
        ]);
        URL::forceRootUrl($base);
        URL::forceScheme((string) (parse_url($base, PHP_URL_SCHEME) ?: 'https'));

        File::deleteDirectory($target);
        File::ensureDirectoryExists($target);

        $kernel = app(Kernel::class);
        $written = 0;

        foreach ($this->pages as $uri => $file) {
            $request = Request::create($uri, 'GET');
            $response = $kernel->handle($request);
            $html = (string) $response->getContent();
            $kernel->terminate($request, $response);

            if ($response->getStatusCode() !== 200) {
                $this->error('Route '.$uri.' returned HTTP '.$response->getStatusCode());

                return self::FAILURE;
            }

            $html = $this->decorate($html, $base, $key, $uri);
            $this->write($target.'/'.$file, $html);
            $written++;
        }

        foreach (['css', 'js', 'img', 'fonts'] as $directory) {
            File::copyDirectory(public_path($directory), $target.'/'.$directory);
        }

        foreach (File::files($target.'/css') as $stylesheet) {
            if ($stylesheet->getExtension() !== 'css') {
                continue;
            }

            $css = (string) File::get($stylesheet->getPathname());
            $css = preg_replace('#url\(/(?!/)#', 'url('.$base.'/', $css) ?? $css;
            File::put($stylesheet->getPathname(), $css);
        }

        foreach (['favicon.png', 'favicon.ico'] as $asset) {
            if (File::exists(public_path($asset))) {
                File::copy(public_path($asset), $target.'/'.$asset);
            }
        }

        $this->write($target.'/.nojekyll', '');
        $this->write($target.'/robots.txt', $this->robots($base));
        $this->write($target.'/sitemap.xml', $this->sitemap($base));
        $this->write($target.'/404.html', $this->notFound($base));

        $this->info(sprintf('Exported %d pages to %s', $written, $target));
        $this->line('Base URL: '.$base);

        if ($key === '') {
            $this->warn('No audit key set. The booking form will not send until WEB3FORMS_ACCESS_KEY is configured.');
        } else {
            $this->line('Audit form: Web3Forms is configured.');
        }

        return self::SUCCESS;
    }

    private function decorate(string $html, string $base, string $key, string $uri): string
    {
        $html = preg_replace('/\s*<meta name="csrf-token"[^>]*>/', '', $html, 1) ?? $html;

        // On a static host every page is a directory index, so /faq is really
        // /faq/. Point the canonical and og:url at the URL that is actually
        // served to avoid a redirect conflicting with the canonical.
        $canonical = $uri === '/' ? $base.'/' : $base.$uri.'/';
        $html = preg_replace(
            '/(<link rel="canonical" href=")[^"]*(")/',
            '$1'.$canonical.'$2',
            $html,
            1,
        ) ?? $html;
        $html = preg_replace(
            '/(<meta property="og:url" content=")[^"]*(")/',
            '$1'.$canonical.'$2',
            $html,
            1,
        ) ?? $html;

        // With no server there is no API to call. Drop the endpoint URLs so the
        // Student Hub goes straight to the bundled browser engines instead of
        // firing four requests that can only fail.
        $html = preg_replace(
            '#<div id="student-config"[^>]*>\s*</div>#i',
            '<div id="student-config" hidden></div>',
            $html,
            1,
        ) ?? $html;

        $csp = $this->cspMeta();

        $html = preg_replace('/(<head>)/i', '$1'."\n    ".$csp, $html, 1) ?? $html;

        // The app emits root-relative asset URLs. A GitHub Pages project site
        // lives under /<repo>/, so every href and src needs that prefix.
        $html = preg_replace('#\b(href|src)="/(?!/)#', '$1="'.$base.'/', $html) ?? $html;

        // Directory indexes are served at /page/, so link to that form directly.
        // Keep any #fragment or ?query attached to the link.
        foreach (array_keys($this->pages) as $page) {
            if ($page === '/') {
                continue;
            }
            $html = preg_replace(
                '~href="'.preg_quote($base.$page, '~').'(?=["#?])~',
                'href="'.$base.$page.'/',
                $html,
            ) ?? $html;
        }

        // No chat endpoint on a static host. Dropping the attribute makes the
        // widget answer from its bundled knowledge base without a request.
        $html = preg_replace('/\s+data-endpoint="[^"]*\/api\/chat"/', '', $html) ?? $html;

        $html = str_replace(
            ['data-endpoint="'.$base.'/api/audit"', 'data-endpoint="/api/audit"'],
            'data-endpoint="https://api.web3forms.com/submit" data-audit-mode="web3forms"'
                .($key === '' ? ' data-audit-nokey="1"' : ''),
            $html,
        );

        $hidden = ($key === '' ? '' : '<input type="hidden" name="access_key" value="'.e($key).'">')
            .'<input type="hidden" name="subject" value="New Free HR Audit request from the Emplowise site">'
            .'<input type="hidden" name="from_name" value="Emplowise website">'
            .'<input type="checkbox" name="botcheck" hidden>';

        return preg_replace('/(<form[^>]*id="audit-form"[^>]*>)/', '$1'.$hidden, $html, 1) ?? $html;
    }

    private function robots(string $base): string
    {
        return "User-agent: *\nAllow: /\nDisallow: /api/\n\nSitemap: ".$base."/sitemap.xml\n";
    }

    private function sitemap(string $base): string
    {
        $urls = '';
        foreach ($this->pages as $uri => $file) {
            $loc = $uri === '/' ? $base.'/' : $base.$uri.'/';
            $priority = $uri === '/' ? '1.0' : '0.8';
            $urls .= '<url><loc>'.e($loc).'</loc><changefreq>weekly</changefreq><priority>'.$priority.'</priority></url>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$urls.'</urlset>'."\n";
    }

    private function cspMeta(): string
    {
        return '<meta http-equiv="Content-Security-Policy" content="default-src \'self\'; '
            .'script-src \'self\'; style-src \'self\'; img-src \'self\' data:; font-src \'self\'; '
            .'connect-src \'self\' https://api.web3forms.com; '
            .'form-action \'self\' https://api.web3forms.com; base-uri \'self\'; object-src \'none\'">';
    }

    private function notFound(string $base): string
    {
        return '<!doctype html><html lang="en"><head><meta charset="utf-8">'
            .'<meta name="viewport" content="width=device-width, initial-scale=1">'
            .$this->cspMeta()
            .'<meta name="robots" content="noindex">'
            .'<title>Page not found | Emplowise</title>'
            .'<link rel="stylesheet" href="'.$base.'/css/site.css">'
            .'</head><body>'
            .'<a class="skip-link" href="#main">Skip to content</a>'
            .'<header class="site-header"><div class="shell">'
            .'<a class="brand" href="'.$base.'/">Emplowise</a>'
            .'</div></header>'
            .'<main id="main" tabindex="-1" class="shell not-found">'
            .'<h1>Page not found</h1><p>That page does not exist. '
            .'<a href="'.$base.'/">Go to the Emplowise home page</a>.</p>'
            .'</main>'
            .'<footer class="site-footer"><div class="shell">'
            .'<p class="footer-note">Emplowise HR Services</p>'
            .'</div></footer>'
            .'</body></html>'."\n";
    }

    private function write(string $path, string $contents): void
    {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents);
    }
}
