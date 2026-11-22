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

        $this->assertFileExists($this->target.'/js/toolkit/interview.js');
        $this->assertFileExists($this->target.'/js/toolkit/ats.js');
        $this->assertFileExists($this->target.'/js/toolkit/profile.js');
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

        // Directory links keep their fragment, so /page#anchor becomes /page/#anchor.
        $this->assertStringContainsString('href="https://example.github.io/demo/what-we-do/#payroll"', $home);
        $this->assertStringContainsString('href="https://example.github.io/demo/emplostart/#prompts"', $home);

        // Self-hosted fonts are rewritten inside the bundle, so woff2 files resolve under the subpath.
        $site = (string) File::get($this->target.'/css/site.css');
        $this->assertStringContainsString('url(https://example.github.io/demo/fonts/', $site);
        $this->assertStringNotContainsString('url(/fonts/', $site);

        $hub = (string) File::get($this->target.'/emplostart/index.html');
        $this->assertStringContainsString('href="https://example.github.io/demo/css/hub.css', $hub);
    }

    public function test_static_pages_drop_the_api_and_use_local_engines(): void
    {
        $this->artisan('emplowise:export-static', [
            'path' => $this->target,
            '--base' => 'https://example.github.io/demo',
            '--audit-key' => 'test-key',
        ])->assertSuccessful();

        $hub = (string) File::get($this->target.'/emplostart/index.html');

        $this->assertStringContainsString('<div id="emplostart-config" hidden></div>', $hub);
        $this->assertStringNotContainsString('data-interview-questions', $hub);
        $this->assertStringContainsString('js/toolkit/interview.js', $hub);
        $this->assertStringContainsString('js/toolkit/ats.js', $hub);
        $this->assertStringContainsString('js/toolkit/profile.js', $hub);
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
            $this->assertDoesNotMatchRegularExpression(
                '/<script(?![^>]*\bsrc=)(?![^>]*application\/ld\+json)[^>]*>\s*\S/',
                $html,
                $page.' has inline script',
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
