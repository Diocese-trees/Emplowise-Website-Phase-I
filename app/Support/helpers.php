<?php



if (! function_exists('asset_v')) {
    /**
     * Root-relative asset URL stamped with the file's modified time.
     *
     * The stamp stops proxies and phone browsers from serving a stale stylesheet
     * after a deploy. Because the path is root-relative, the site also works
     * behind any proxy, tunnel, or port mapping.
     */
    function asset_v(string $path): string
    {
        $path = ltrim($path, '/');
        $file = public_path($path);
        $version = is_file($file) ? (string) filemtime($file) : '0';

        return '/'.ltrim($path, '/').'?v='.$version;
    }
}
