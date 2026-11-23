<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\CssBundles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Regenerates the checked-in stylesheet bundles. Run this after editing any
 * source file in public/css; the layout loads only the bundles.
 */
final class BundleCss extends Command
{
    protected $signature = 'emplowise:bundle-css';

    protected $description = 'Rebuild public/css/site.css from its sources';

    public function handle(): int
    {
        foreach (CssBundles::build() as $name => $contents) {
            $path = public_path('css/'.$name);
            File::put($path, $contents);
            $this->line(sprintf('  %-10s %6.1f KB', $name, strlen($contents) / 1024));
        }

        $this->info('Stylesheet bundles rebuilt.');

        return self::SUCCESS;
    }
}
