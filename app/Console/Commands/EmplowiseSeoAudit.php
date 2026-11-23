<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class EmplowiseSeoAudit extends Command
{
    protected $signature = 'emplowise:seo-audit';

    protected $description = 'Check every public route title, description, keyword, and heading in config/emplowise.php';

    private const ROUTES = [
        'home',
        'what-we-are',
        'what-we-do',
        'what-we-are-building',
        'why-emplowise',
        'emplostart',
        'faq',
        'contact',
        'free-hr-audit',
        'company-profile',
        'portfolio',
        'pitch-deck',
        'docs',
    ];

    private const BANNED_WORDS = ['leverage', 'ecosystem', 'landscape', 'holistic', 'seamless'];

    private const FORBIDDEN_PHRASES = ['Pending Input', 'Foundation', 'Growth', 'Scale', 'Replit'];

    public function handle(): int
    {
        $seo = config('emplowise.seo', []);
        $rows = [];
        $failures = [];

        $titles = [];
        $descriptions = [];
        $primaries = [];
        $secondaries = [];

        foreach (self::ROUTES as $route) {
            $entry = $seo[$route] ?? null;
            $problems = [];

            if (! is_array($entry)) {
                $rows[] = [$route, 'FAIL', 'missing seo entry'];
                $failures[] = $route;

                continue;
            }

            foreach (['primary_keyword', 'secondary_keywords', 'title', 'description', 'h1', 'h2', 'intro'] as $key) {
                if (blank($entry[$key] ?? null)) {
                    $problems[] = "missing {$key}";
                }
            }

            $title = (string) ($entry['title'] ?? '');
            $titleLength = Str::length($title);
            if ($titleLength < 45 || $titleLength > 60) {
                $problems[] = "title is {$titleLength} characters, needs 45 to 60";
            }
            if (! Str::endsWith($title, '| Emplowise')) {
                $problems[] = 'title must end with "| Emplowise"';
            }

            $description = (string) ($entry['description'] ?? '');
            $descriptionLength = Str::length($description);
            if ($descriptionLength < 135 || $descriptionLength > 155) {
                $problems[] = "description is {$descriptionLength} characters, needs 135 to 155";
            }

            $primary = Str::lower(trim((string) ($entry['primary_keyword'] ?? '')));
            $secondary = $entry['secondary_keywords'] ?? [];
            if (! is_array($secondary) || count($secondary) !== 2) {
                $problems[] = 'secondary_keywords must hold exactly two phrases';
            }

            foreach (['title', 'h1', 'intro', 'h2'] as $field) {
                if ($primary !== '' && ! Str::contains(Str::lower((string) ($entry[$field] ?? '')), $primary)) {
                    $problems[] = "primary keyword missing from {$field}";
                }
            }

            $blob = implode(' ', [
                $title,
                $description,
                (string) ($entry['h1'] ?? ''),
                (string) ($entry['h2'] ?? ''),
                (string) ($entry['intro'] ?? ''),
                implode(' ', array_map('strval', is_array($secondary) ? $secondary : [])),
            ]);

            if (Str::contains($blob, ['—', '–'])) {
                $problems[] = 'em dash found';
            }

            foreach (self::BANNED_WORDS as $word) {
                if (Str::contains(Str::lower($blob), $word)) {
                    $problems[] = "banned word: {$word}";
                }
            }

            foreach (self::FORBIDDEN_PHRASES as $phrase) {
                if (Str::contains($blob, $phrase)) {
                    $problems[] = "forbidden phrase: {$phrase}";
                }
            }

            if ($title !== '' && isset($titles[$title])) {
                $problems[] = 'duplicate title with '.$titles[$title];
            }
            $titles[$title] = $route;

            if ($description !== '' && isset($descriptions[$description])) {
                $problems[] = 'duplicate description with '.$descriptions[$description];
            }
            $descriptions[$description] = $route;

            if ($primary !== '' && isset($primaries[$primary])) {
                $problems[] = 'duplicate primary keyword with '.$primaries[$primary];
            }
            $primaries[$primary] = $route;

            foreach (is_array($secondary) ? $secondary : [] as $phrase) {
                $key = Str::lower(trim((string) $phrase));
                if ($key === '') {
                    continue;
                }
                if (isset($secondaries[$key])) {
                    $problems[] = "duplicate secondary keyword with {$secondaries[$key]}";
                }
                $secondaries[$key] = $route;
            }

            $rows[] = [$route, $problems === [] ? 'PASS' : 'FAIL', $problems === [] ? 'ok' : implode('; ', $problems)];
            if ($problems !== []) {
                $failures[] = $route;
            }
        }

        $this->table(['Route', 'Result', 'Notes'], $rows);

        $factProblems = $this->factProblems();
        foreach ($factProblems as $problem) {
            $this->error('Fact check: '.$problem);
        }

        if ($failures !== [] || $factProblems !== []) {
            $this->newLine();
            $this->error('SEO audit failed. Fix the notes above, then run the audit again.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('SEO audit passed. All '.count(self::ROUTES).' routes have unique, in-range titles and descriptions, and every primary keyword appears in the title, h1, intro, and a section heading.');

        return self::SUCCESS;
    }

    /**
     * Confirmed business facts that the public copy depends on.
     *
     * @return array<int, string>
     */
    private function factProblems(): array
    {
        $problems = [];
        $packages = collect(config('emplowise.packages', []))->keyBy('slug');
        $services = config('emplowise.services', []);
        $pillars = config('emplowise.pillars', []);
        $method = config('emplowise.methodology', []);
        $prompts = config('emplowise.student.prompts', []);
        $faqs = config('emplowise.faqs', []);

        if ($packages->count() !== 3) {
            $problems[] = 'packages should count 3';
        }
        if ((int) ($packages->get('starter')['price_pkr'] ?? 0) !== 40000
            || ($packages->get('starter')['capacity'] ?? '') !== 'Up to 20 employees') {
            $problems[] = 'Starter should be PKR 40,000 for up to 20 employees';
        }
        if ((int) ($packages->get('business')['price_pkr'] ?? 0) !== 75000
            || ($packages->get('business')['capacity'] ?? '') !== 'Up to 50 employees') {
            $problems[] = 'Business should be PKR 75,000 for up to 50 employees';
        }
        if (($packages->get('enterprise')['price_pkr'] ?? null) !== null) {
            $problems[] = 'Enterprise should be custom priced';
        }
        if (count($services) !== 14) {
            $problems[] = 'services should count 14';
        }
        if (count($pillars) !== 3) {
            $problems[] = 'pillars should count 3';
        }
        if (implode(',', array_column($method, 'name')) !== 'Assess,Identify,Improve,Review') {
            $problems[] = 'methodology should run Assess, Identify, Improve, Review';
        }
        if (count($prompts) !== 14) {
            $problems[] = 'student prompt library should hold 14 prompts';
        }

        $firstCategory = $faqs[0]['category'] ?? '';
        if (config('emplowise.seo.faq.h2') !== $firstCategory) {
            $problems[] = 'faq seo h2 must match the first FAQ category so the keyword renders as a heading';
        }

        return $problems;
    }
}
