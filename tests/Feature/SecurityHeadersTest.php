<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_policy_is_emitted_in_the_configured_mode(): void
    {
        config(['security.csp.mode' => 'enforce']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeaderMissing('Content-Security-Policy-Report-Only');

        $policy = $response->headers->get('Content-Security-Policy');

        $this->assertNotNull($policy);
        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString("script-src 'self'", $policy);
        $this->assertStringContainsString("object-src 'none'", $policy);
        $this->assertStringContainsString("base-uri 'self'", $policy);
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringContainsString('report-uri /api/csp-report', $policy);
    }

    public function test_report_only_mode_does_not_enforce(): void
    {
        config(['security.csp.mode' => 'report_only']);

        $response = $this->get('/');

        $response->assertHeaderMissing('Content-Security-Policy');
        $this->assertNotNull($response->headers->get('Content-Security-Policy-Report-Only'));
    }

    public function test_hsts_only_on_secure_requests(): void
    {
        config(['security.hsts.enabled' => true, 'security.hsts.preload' => true]);

        $this->get('/')->assertHeaderMissing('Strict-Transport-Security');

        $secure = $this->get('https://localhost/');
        $secure->assertHeader('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
    }

    public function test_strict_policy_has_no_inline_escapes(): void
    {
        config(['security.csp.mode' => 'enforce']);

        $response = $this->get('/')->assertOk();
        $policy = (string) $response->headers->get('Content-Security-Policy');

        $this->assertStringNotContainsString("'unsafe-inline'", $policy);
        $this->assertStringNotContainsString("'unsafe-eval'", $policy);
        $this->assertStringNotContainsString('style=', (string) $response->getContent());
    }

    public function test_baseline_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
    }
}
