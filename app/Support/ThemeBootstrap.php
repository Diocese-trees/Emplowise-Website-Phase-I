<?php

declare(strict_types=1);

namespace App\Support;

/**
 * The tiny inline script that puts the visitor's palette on <html> before the
 * first paint. It is inline on purpose: a deferred external file would let the
 * light palette paint first and flash on every dark-mode visit.
 *
 * Because script-src is 'self' with no 'unsafe-inline', the exact bytes below
 * are registered as a SHA-256 hash in the CSP (headers and the static <meta>).
 * Change SCRIPT and the hash follows automatically; never edit the emitted
 * markup without going through this class.
 */
final class ThemeBootstrap
{
    public const SCRIPT = "(function(){var r=document.documentElement,k='emplowise:theme',s=null,t;try{s=window.localStorage.getItem(k)}catch(e){}t=(s==='dark'||s==='light')?s:((window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light');if(t==='dark')r.setAttribute('data-theme','dark');r.classList.add('js-theme');if(t==='dark'){var m=document.querySelector('meta[name=\"theme-color\"]');if(m)m.setAttribute('content','#050F26')}})();";

    /**
     * The script element as it is written into <head>. The hash is computed
     * from SCRIPT, so the tag and the CSP token can never drift apart.
     */
    public static function tag(): string
    {
        return '<script>'.self::SCRIPT.'</script>';
    }

    /** CSP source expression, quotes included: 'sha256-…'. */
    public static function hash(): string
    {
        return "'sha256-".base64_encode(hash('sha256', self::SCRIPT, true))."'";
    }
}
