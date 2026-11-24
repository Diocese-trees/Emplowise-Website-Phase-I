<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Console\Commands\ExportStatic;
use Tests\TestCase;

/**
 * The Vercel configuration is a second copy of rules that already exist in
 * PHP: the legacy redirects and the response headers. These tests fail when
 * the copy drifts, rather than leaving a stale file to be discovered in
 * production.
 */
final class VercelConfigTest extends TestCase
{
    private array $config;

    protected function setUp(): void
    {
        parent::setUp();

        $raw = (string) file_get_contents(base_path('vercel.json'));
        $decoded = json_decode($raw, true);

        $this->assertIsArray($decoded, 'vercel.json must be valid JSON.');
        $this->config = $decoded;
    }

    public function test_it_keeps_the_folder_layout_the_exporter_writes(): void
    {
        $this->assertTrue($this->config['trailingSlash'] ?? false, 'The exporter writes /page/index.html, so trailing slashes must be kept.');
    }

    public function test_it_redirects_every_legacy_uri_the_exporter_redirects(): void
    {
        $command = new ExportStatic();
        $property = new \ReflectionProperty($command, 'redirects');
        $property->setAccessible(true);
        /** @var array<string, string> $map */
        $map = $property->getValue($command);

        $configured = [];
        foreach ($this->config['redirects'] as $redirect) {
            $configured[rtrim($redirect['source'], '/')] = $redirect['destination'];
        }

        foreach ($map as $uri => $destination) {
            [$path, $fragment] = array_pad(explode('#', $destination, 2), 2, null);
            $expected = rtrim($path, '/').'/'.($fragment === null ? '' : '#'.$fragment);

            $this->assertArrayHasKey($uri, $configured, "vercel.json does not redirect {$uri}.");
            $this->assertSame($expected, $configured[$uri], "vercel.json sends {$uri} to the wrong place.");
        }

        $this->assertCount(count($map), $configured, 'vercel.json has redirects the exporter does not.');
    }

    public function test_every_redirect_is_permanent_and_uses_the_public_property(): void
    {
        foreach ($this->config['redirects'] as $redirect) {
            // permanent: true is the documented switch and answers 308. The
            // statusCode override is marked private in Vercel's own schema, so
            // it is not relied on here.
            $this->assertTrue($redirect['permanent'] ?? false, 'Legacy routes must be permanent redirects.');
            $this->assertArrayNotHasKey('statusCode', $redirect);
            $this->assertStringEndsWith('/', $redirect['source'], 'trailingSlash: true means sources must carry the slash.');
        }
    }

    public function test_it_sends_the_headers_a_meta_tag_cannot(): void
    {
        $headers = $this->headersFor('/(.*)');

        $this->assertStringContainsString("frame-ancestors 'none'", $headers['content-security-policy']);
        $this->assertSame('nosniff', $headers['x-content-type-options']);
        $this->assertSame('DENY', $headers['x-frame-options']);
        $this->assertSame('strict-origin-when-cross-origin', $headers['referrer-policy']);
        $this->assertStringContainsString('camera=()', $headers['permissions-policy']);
        $this->assertStringContainsString('max-age=63072000', $headers['strict-transport-security']);
    }

    public function test_html_cache_control_is_left_to_the_platform(): void
    {
        // An earlier version set Cache-Control on the catch-all rule as well.
        // Two rules then matched every asset, and the merge order decided the
        // result. Vercel already answers HTML with max-age=0, must-revalidate,
        // so the catch-all stays out of it and only assets opt in.
        $this->assertArrayNotHasKey('cache-control', $this->headersFor('/(.*)'));
    }

    public function test_static_assets_get_a_short_ttl_never_immutable(): void
    {
        // Asset names are not content-hashed, so an immutable cache would pin
        // an old stylesheet to a new page.
        foreach (['/css/(.*)', '/js/(.*)', '/fonts/(.*)', '/img/(.*)', '/downloads/(.*)'] as $source) {
            $cache = $this->headersFor($source)['cache-control'] ?? '';
            $this->assertStringContainsString('max-age=86400', $cache, "{$source} should cache for a day.");
            $this->assertStringContainsString('stale-while-revalidate', $cache);
            $this->assertStringNotContainsString('immutable', $cache);
        }
    }

    /**
     * @return array<string, string>
     */
    private function headersFor(string $source): array
    {
        foreach ($this->config['headers'] as $rule) {
            if ($rule['source'] === $source) {
                $headers = [];
                foreach ($rule['headers'] as $header) {
                    $headers[strtolower($header['key'])] = $header['value'];
                }

                return $headers;
            }
        }

        $this->fail("vercel.json has no header rule for {$source}.");
    }
}
