<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps generated URLs usable behind a proxy, tunnel, or container port map.
 *
 * Without this, a request that reaches the app through a reverse proxy can
 * still emit links and asset paths pointing at the loopback host. On any other
 * device those requests fail, which reads as "the front end is missing".
 */
class ForcePublicUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $forwardedHost = $request->header('X-Forwarded-Host');

        if ($forwardedHost) {
            $scheme = $request->header('X-Forwarded-Proto');

            if (! $scheme) {
                $scheme = $request->header('X-Forwarded-Ssl') === 'on' ? 'https' : $request->getScheme();
            }

            URL::forceRootUrl($scheme.'://'.trim(explode(',', $forwardedHost)[0]));
            URL::forceScheme($scheme);
        }

        $response = $next($request);

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();

        if (! is_string($html) || $html === '') {
            return $response;
        }

        // Belt and braces: collapse absolute same-origin URLs to root-relative
        // ones so the markup works on whatever host actually served it.
        $roots = array_unique(array_filter([
            $request->getSchemeAndHttpHost(),
            rtrim((string) config('app.url'), '/'),
            'http://127.0.0.1:8000',
            'http://localhost:8000',
            'http://localhost',
            'http://127.0.0.1',
        ]));

        // Canonical URLs, OpenGraph URLs, and JSON-LD must stay absolute.
        $kept = [];
        $html = preg_replace_callback(
            '/<script type="application\/ld\+json">.*?<\/script>|<(?:link|meta)[^>]*(?:rel="canonical"|property="og:url")[^>]*>/is',
            function (array $match) use (&$kept): string {
                $kept[] = $match[0];

                return '<!--keep-'.(count($kept) - 1).'-->';
            },
            $html
        ) ?? $html;

        $rewritten = $html;

        foreach ($roots as $root) {
            $rewritten = str_replace($root.'/', '/', $rewritten);
            $rewritten = str_replace($root.'"', '/"', $rewritten);
            $rewritten = str_replace($root."'", "/'", $rewritten);
        }

        $rewritten = preg_replace_callback(
            '/<!--keep-(\d+)-->/',
            fn (array $match): string => $kept[(int) $match[1]],
            $rewritten
        ) ?? $rewritten;

        if ($rewritten !== $response->getContent()) {
            $response->setContent($rewritten);
            $response->headers->remove('Content-Length');
        }

        return $response;
    }
}
