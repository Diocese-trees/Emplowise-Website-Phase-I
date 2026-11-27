<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

final class EmplowiseUxGate extends Command
{
    protected $signature = 'emplowise:ux-gate
        {--base= : Base URL of a served export, e.g. http://127.0.0.1:8124}
        {--shots= : Directory for full-page phone screenshots}';

    protected $description = 'Run the headless UX gate (overflow, touch targets, drawer focus, dock, axe) against a served export';

    public function handle(): int
    {
        $base = (string) $this->option('base');

        if ($base === '') {
            $this->error('Pass --base=<url>. Serve an export first, e.g.:');
            $this->line('  php artisan emplowise:export-static /tmp/ux-export --base=http://127.0.0.1:8124');
            $this->line('  php -S 127.0.0.1:8124 -t /tmp/ux-export');
            $this->line('  php artisan emplowise:ux-gate --base=http://127.0.0.1:8124');

            return self::FAILURE;
        }

        $script = base_path('tools/ux-gate/check.py');
        if (! is_file($script)) {
            $this->error("Missing gate script: {$script}");

            return self::FAILURE;
        }

        $command = ['python3', $script, '--base='.$base];
        if (is_string($this->option('shots')) && $this->option('shots') !== '') {
            $command[] = '--shots='.$this->option('shots');
        }

        $result = Process::forever()->run($command, function (string $type, string $output): void {
            $this->output->write($output);
        });

        if (! $result->successful()) {
            $this->newLine();
            $this->error('UX gate failed. The suite is non-blocking; see the output above.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('UX gate passed.');

        return self::SUCCESS;
    }
}
