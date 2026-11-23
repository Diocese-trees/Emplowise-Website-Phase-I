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

    public function test_every_redirect_is_permanent(): void
    {
        foreach ($this->config['redirects'] as $redirect) {
            $this->assertSame(301, $redirect['statusCode'] ?? null, 'Legacy routes must answer 301.');
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

    public function test_html_is_revalidated_and_static_assets_get_a_short_ttl(): void
    {
        // Asset names are not content-hashed, so a long immutable cache would
        // pin an old stylesheet to a new page.
        $this->assertSame('public, max-age=0, must-revalidate', $this->headersFor('/(.*)')['cache-control']);

        foreach (['/css/(.*)', '/js/(.*)', '/fonts/(.*)', '/img/(.*)', '/downloads/(.*)'] as $source) {
            $cache = $this->headersFor($source)['cache-control'] ?? '';
            $this->assertStringContainsString('max-age=86400', $cache, "{$source} should cache for a day.");
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
