<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

final class ExportStaticTest extends TestCase
{
    private string $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->target = sys_get_temp_dir().'/emplowise-static-'.bin2hex(random_bytes(6));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->target);

        parent::tearDown();
    }

    public function test_it_renders_every_public_page_for_a_project_site(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
            '--audit-key' => 'test-key',
        ])->assertSuccessful();

        $pages = [
            'index.html',
            'what-we-are/index.html',
            'what-we-do/index.html',
            'why-emplowise/index.html',
            'emplostart/index.html',
            'faq/index.html',
            'contact/index.html',
            'what-we-are-building/index.html',
            'free-hr-audit/index.html',
            'terms/index.html',
            'privacy/index.html',
            'company-profile/index.html',
            'portfolio/index.html',
            'pitch-deck/index.html',
            'docs/index.html',
        ];

        foreach ($pages as $page) {
            $this->assertFileExists($this->target.'/'.$page);
            $this->assertGreaterThan(1000, (int) filesize($this->target.'/'.$page));
        }

        foreach (['.nojekyll', 'robots.txt', 'sitemap.xml', '404.html'] as $asset) {
            $this->assertFileExists($this->target.'/'.$asset);
        }

        foreach (['pricing', 'career-hub', 'student-hub', 'ai-automation', 'team'] as $legacy) {
            $redirect = (string) File::get($this->target.'/'.$legacy.'/index.html');
            $this->assertStringContainsString('http-equiv="refresh"', $redirect, $legacy.' must redirect');
            $this->assertStringContainsString('name="robots" content="noindex, follow"', $redirect);
        }

        $this->assertFalse(is_dir($this->target.'/js/toolkit'), 'The toolkit runtime no longer ships with the site');
    }

    public function test_assets_and_canonicals_use_the_base_path(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
            '--audit-key' => 'test-key',
        ])->assertSuccessful();

        $home = (string) File::get($this->target.'/index.html');
        $faq = (string) File::get($this->target.'/faq/index.html');

        $this->assertStringContainsString('href="https://example.github.io/demo/css/site.css', $home);
        $this->assertStringContainsString('src="https://example.github.io/demo/js/app.js', $home);
        $this->assertStringContainsString('<link rel="canonical" href="https://example.github.io/demo/faq/"', $faq);
        $this->assertStringNotContainsString('href="/css/', $home);
        $this->assertStringNotContainsString('src="/js/', $home);

        // srcset is a comma-separated list, so it needs its own rewrite pass:
        // a root-relative candidate would 404 on a project site.
        $this->assertStringContainsString('srcset="https://example.github.io/demo/img/logo-primary-125.webp', $home);
        $this->assertStringNotContainsString('srcset="/img/', $home);
        $this->assertStringNotContainsString(', /img/', $home);

        $team = (string) File::get($this->target.'/what-we-are/index.html');
        $this->assertSame(3, substr_count($team, 'class="team-avatar"'));
        $this->assertStringNotContainsString('img/team/placeholder-', $team);

        // Directory links keep their fragment, so /page#anchor becomes /page/#anchor.
        $this->assertStringContainsString('href="https://example.github.io/demo/what-we-do/#payroll"', $home);
        $this->assertStringContainsString('href="https://example.github.io/demo/emplostart/#modules"', $home);

        // Self-hosted fonts are rewritten inside the bundle, so woff2 files resolve under the subpath.
        $site = (string) File::get($this->target.'/css/site.css');
        $this->assertStringContainsString('url(https://example.github.io/demo/fonts/', $site);
        $this->assertStringNotContainsString('url(/fonts/', $site);

        $hub = (string) File::get($this->target.'/emplostart/index.html');
        $this->assertStringContainsString('href="https://example.github.io/demo/css/site.css', $hub);
        $this->assertStringContainsString('href="'.e((string) config('emplowise.emplostart_url')).'"', $hub);
    }

    public function test_it_exports_the_showcase_pages_assets_and_downloads(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
            '--audit-key' => 'test-key',
        ])->assertSuccessful();

        $pages = [
            'company-profile' => 'Emplowise-Company-Profile-2026.docx',
            'portfolio' => 'Emplowise-Portfolio-2026.docx',
            'pitch-deck' => 'Emplowise-Pitch-Deck-2026.pptx',
        ];

        foreach ($pages as $page => $download) {
            $html = (string) File::get($this->target.'/'.$page.'/index.html');

            $this->assertStringContainsString('https://example.github.io/demo/css/showcase.css', $html, $page);
            $this->assertStringNotContainsString('[To Verify', $html, $page.' leaks an internal marker');
            $this->assertStringNotContainsString('href="/downloads/', $html, $page.' keeps a root-relative download');
            $this->assertStringContainsString(
                'href="https://example.github.io/demo/downloads/'.$download,
                $html,
                $page.' download must resolve under the base path',
            );
        }

        $deck = (string) File::get($this->target.'/pitch-deck/index.html');
        $portfolio = (string) File::get($this->target.'/portfolio/index.html');

        $this->assertStringContainsString('https://example.github.io/demo/js/deck.js', $deck);
        $this->assertStringNotContainsString('js/deck.js', $portfolio, 'deck.js must stay on the deck page');
        $this->assertStringContainsString('name="robots" content="noindex,follow"', $deck);
        $this->assertFileExists($this->target.'/css/showcase.css');
        $this->assertFileExists($this->target.'/js/deck.js');

        $downloads = [
            'Emplowise-Branding-Guidelines-2026.docx',
            'Emplowise-Brand-Kit-2026.docx',
            'Emplowise-Company-Profile-2026.docx',
            'Emplowise-Pitch-Deck-2026.pptx',
            'Emplowise-Portfolio-2026.docx',
            'Emplowise-Start-Prompt-Library-2026.docx',
            'Emplowise-Tool-Stack-2026.docx',
            'Emplowise-Website-Breakdown-2026.docx',
        ];

        $this->assertCount(count($downloads), File::files($this->target.'/downloads'));

        foreach ($downloads as $download) {
            $path = $this->target.'/downloads/'.$download;
            $this->assertFileExists($path);
            $this->assertGreaterThan(10000, (int) filesize($path), $download.' looks empty');
            $this->assertSame('PK', substr((string) File::get($path), 0, 2), $download.' is not a zip-based Office file');
        }

        $sitemap = (string) File::get($this->target.'/sitemap.xml');
        $this->assertStringContainsString('https://example.github.io/demo/company-profile/', $sitemap);
        $this->assertStringContainsString('https://example.github.io/demo/portfolio/', $sitemap);
        $this->assertStringNotContainsString('/pitch-deck/', $sitemap);
    }

    public function test_static_launcher_links_out_without_api_calls(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
            '--audit-key' => 'test-key',
        ])->assertSuccessful();

        $hub = (string) File::get($this->target.'/emplostart/index.html');

        $this->assertStringContainsString('href="'.e((string) config('emplowise.emplostart_url')).'"', $hub);
        $this->assertStringContainsString('target="_blank"', $hub);
        $this->assertStringNotContainsString('emplostart-config', $hub);
        $this->assertStringNotContainsString('js/toolkit/', $hub);
        $this->assertStringNotContainsString('js/student.js', $hub);
        $this->assertStringNotContainsString('csrf-token', $hub);
    }

    public function test_the_chat_widget_has_no_endpoint_on_a_static_host(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
            '--audit-key' => 'test-key',
        ])->assertSuccessful();

        foreach (['index.html', 'faq/index.html', 'free-hr-audit/index.html'] as $page) {
            $html = (string) File::get($this->target.'/'.$page);
            $this->assertStringNotContainsString('/api/chat', $html, $page.' still points at the chat API');
        }
    }

    public function test_no_exported_page_keeps_a_laravel_api_route(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
            '--audit-key' => 'test-key',
        ])->assertSuccessful();

        $routes = ['/api/chat', '/api/audit', '/api/csp-report'];
        $files = File::allFiles($this->target);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'html') {
                continue;
            }

            $html = (string) File::get($file->getPathname());

            foreach ($routes as $route) {
                $this->assertStringNotContainsString(
                    $route,
                    $html,
                    $file->getRelativePathname().' still points at '.$route,
                );
            }
        }
    }

    public function test_a_keyless_export_offers_an_email_fallback(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
        ])->assertSuccessful();

        $audit = (string) File::get($this->target.'/free-hr-audit/index.html');

        $this->assertStringContainsString('data-fallback-email="hello@emplowise.com"', $audit);
        $this->assertStringContainsString('data-audit-nokey="1"', $audit);
    }

    public function test_it_swaps_the_booking_form_to_web3forms(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
            '--audit-key' => 'test-key',
        ])->assertSuccessful();

        $audit = (string) File::get($this->target.'/free-hr-audit/index.html');

        $this->assertStringContainsString('data-endpoint="https://api.web3forms.com/submit"', $audit);
        $this->assertStringContainsString('data-audit-mode="web3forms"', $audit);
        $this->assertStringContainsString('name="access_key" value="test-key"', $audit);
        $this->assertStringNotContainsString('data-audit-nokey', $audit);
        $this->assertStringNotContainsString('/api/audit', $audit);

        $csp = (string) File::get($this->target.'/index.html');
        $this->assertStringContainsString("connect-src 'self' https://api.web3forms.com", $csp);
        $this->assertStringContainsString("form-action 'self' https://api.web3forms.com", $csp);
    }

    public function test_a_build_without_a_key_is_marked_so_the_form_does_not_post(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
        ])->assertSuccessful();

        $audit = (string) File::get($this->target.'/free-hr-audit/index.html');

        $this->assertStringContainsString('data-audit-nokey="1"', $audit);
        $this->assertStringNotContainsString('name="access_key"', $audit);
    }

    public function test_exported_pages_keep_the_strict_no_inline_policy(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
            '--audit-key' => 'test-key',
        ])->assertSuccessful();

        foreach (['index.html', 'emplostart/index.html', 'what-we-are/index.html', 'what-we-are-building/index.html', 'free-hr-audit/index.html', '404.html'] as $page) {
            $html = (string) File::get($this->target.'/'.$page);

            $this->assertStringNotContainsString('style="', $html, $page.' has an inline style');
            $this->assertStringContainsString(
                \App\Support\ThemeBootstrap::tag(),
                $html,
                $page.' is missing the pre-paint theme bootstrap',
            );
            $this->assertStringContainsString(
                "script-src 'self' ".\App\Support\ThemeBootstrap::hash(),
                $html,
                $page.' CSP meta does not whitelist the bootstrap hash',
            );

            // The hashed bootstrap is the only executable inline script.
            $stripped = str_replace(\App\Support\ThemeBootstrap::tag(), '', $html);
            $this->assertDoesNotMatchRegularExpression(
                '/<script(?![^>]*\bsrc=)(?![^>]*application\/ld\+json)[^>]*>\s*\S/',
                $stripped,
                $page.' has an inline script other than the hashed theme bootstrap',
            );
        }
    }

    public function test_the_export_does_not_need_a_session_store_or_a_database(): void
    {
        config(['session.driver' => 'not-a-real-driver', 'cache.default' => 'not-a-real-store']);

        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
        ])->assertSuccessful();

        $this->assertFileExists($this->target.'/index.html');
    }

    public function test_it_refuses_to_write_into_the_application_root(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => base_path(),
            '--base' => 'https://example.github.io/demo',
        ])->assertExitCode(1);
    }
}
