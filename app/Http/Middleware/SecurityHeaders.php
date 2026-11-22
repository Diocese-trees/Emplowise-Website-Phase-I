<?php

namespace App\Http\Middleware;

use App\Support\ThemeBootstrap;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline security headers plus a configurable Content Security Policy.
 *
 * CSP ships in report-only mode first. Once the browser console and
 * /storage/logs/csp.log stay silent, set SECURITY_CSP_MODE=enforce.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        $this->applyCsp($response);
        $this->applyHsts($request, $response);

        return $response;
    }

    private function applyCsp(Response $response): void
    {
        $mode = (string) config('security.csp.mode', 'report_only');

        if ($mode === 'off' || $mode === '') {
            return;
        }

        $policy = $this->policy();

        if ($mode === 'enforce') {
            $response->headers->set('Content-Security-Policy', $policy);
        } else {
            $response->headers->set('Content-Security-Policy-Report-Only', $policy);
        }

        $response->headers->set('Reporting-Endpoints', 'csp-endpoint="'.config('security.csp.report_uri').'"');
    }

    private function policy(): string
    {
        $parts = [];

        foreach ((array) config('security.csp.directives', []) as $directive => $values) {
            $values = (array) $values;

            // The pre-paint theme bootstrap is inline; its exact bytes are
            // whitelisted by hash instead of loosening script-src.
            if ($directive === 'script-src') {
                $values[] = ThemeBootstrap::hash();
            }

            $parts[] = trim($directive.' '.implode(' ', $values));
        }

        $reportUri = (string) config('security.csp.report_uri', '');

        if ($reportUri !== '') {
            $parts[] = 'report-uri '.$reportUri;
            $parts[] = 'report-to csp-endpoint';
        }

        return rtrim(implode('; ', $parts), ';');
    }

    private function applyHsts(Request $request, Response $response): void
    {
        if (! (bool) config('security.hsts.enabled', true)) {
            return;
        }

        if (! $request->isSecure()) {
            return;
        }

        $value = 'max-age='.(int) config('security.hsts.max_age', 63072000);

        $preload = (bool) config('security.hsts.preload', false);

        // Browsers ignore the preload token unless includeSubDomains is also
        // present, so preload always wins when the two settings disagree.
        if ((bool) config('security.hsts.include_subdomains', true) || $preload) {
            $value .= '; includeSubDomains';
        }

        if ($preload) {
            $value .= '; preload';
        }

        $response->headers->set('Strict-Transport-Security', $value);
    }
}
