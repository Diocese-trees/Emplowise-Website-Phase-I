<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Deterministic career tools with optional model assistance.
 *
 * Every tool works without an API key. When Alibaba Cloud Model Studio is
 * configured, the model refines the output. The local engines stay the source
 * of the numeric score so results are explainable and repeatable.
 */
final class StudentToolkit
{
    public function __construct(private readonly AlibabaCloudClient $client)
    {
    }

    // -----------------------------------------------------------------
    // Module 1: Interview preparation
    // -----------------------------------------------------------------

    /**
     * @return array{questions: array<int, string>, source: string}
     */
    public function interviewQuestions(string $role, string $level): array
    {
        $fallback = $this->localQuestions($role, $level);

        if (! $this->client->configured()) {
            return ['questions' => $fallback, 'source' => 'local'];
        }

        try {
            $result = $this->client->chat([
                ['role' => 'system', 'content' => 'You write interview questions. Return strict JSON only.'],
                ['role' => 'user', 'content' => sprintf(
                    'Write 3 interview questions for a %s candidate applying for a %s role. Mix one behavioural, one role specific, one motivation or judgement question. Keep each under 24 words. Return JSON: {"questions":["...","...","..."]}',
                    $level,
                    $role,
                )],
            ], 0.6, 300, true);

            $decoded = $this->decodeJson($result['content']);
            $questions = array_values(array_filter(
                array_map(static fn ($q): string => trim((string) $q), (array) ($decoded['questions'] ?? [])),
                static fn (string $q): bool => $q !== '',
            ));

            if (count($questions) >= 3) {
                return ['questions' => array_slice($questions, 0, 3), 'source' => 'model:'.$result['model']];
            }
        } catch (RuntimeException) {
            // Fall through to the local bank.
        }

        return ['questions' => $fallback, 'source' => 'local'];
    }

    /**
     * @return array{score: int, verdict: string, breakdown: array<int, array{label: string, score: int, max: int, detail: string}>, strengths: array<int, string>, fixes: array<int, string>, model_answer: string, next_probe: string, source: string}
     */
    public function interviewFeedback(string $role, string $level, string $question, string $answer): array
    {
        $local = $this->localFeedback($role, $level, $question, $answer);

        if (! $this->client->configured()) {
            return [...$local, 'source' => 'local'];
        }

        try {
            $result = $this->client->chat([
                ['role' => 'system', 'content' => 'You are a fair but demanding interview coach. Return strict JSON only. Never invent facts about the candidate.'],
                ['role' => 'user', 'content' => sprintf(
                    "Role: %s. Level: %s.\nQuestion: %s\nCandidate answer: %s\n\nScore the answer 0 to 100 using structure (STAR), evidence, and clarity. Give 3 specific strengths, 3 concrete fixes, and a model answer of 90 words or fewer that uses placeholders in brackets instead of invented details. Return JSON: {\"score\":number,\"verdict\":\"short label\",\"strengths\":[\"...\"],\"fixes\":[\"...\"],\"model_answer\":\"...\"}",
                    $role,
                    $level,
                    $question,
                    Str::limit($answer, 4000),
                )],
            ], 0.5, 700, true);

            $decoded = $this->decodeJson($result['content']);

            if (! isset($decoded['score'], $decoded['strengths'], $decoded['fixes'], $decoded['model_answer'])) {
                throw new RuntimeException('Incomplete interview JSON.');
            }

            return [
                'score' => max(0, min(100, (int) $decoded['score'])),
                'verdict' => (string) ($decoded['verdict'] ?? $this->verdict((int) $decoded['score'])),
                'breakdown' => $local['breakdown'],
                'strengths' => array_slice(array_values(array_map('strval', (array) $decoded['strengths'])), 0, 4),
                'fixes' => array_slice(array_values(array_map('strval', (array) $decoded['fixes'])), 0, 4),
                'model_answer' => (string) $decoded['model_answer'],
                'next_probe' => $local['next_probe'],
                'source' => 'model:'.$result['model'],
            ];
        } catch (RuntimeException) {
            return [...$local, 'source' => 'local'];
        }
    }

    // -----------------------------------------------------------------
    // Module 2: ATS scoring
    // -----------------------------------------------------------------

    /**
     * @return array<string, mixed>
     */
    public function atsScore(string $resume, string $jobDescription): array
    {
        $resume = trim($resume);
        $jobDescription = trim($jobDescription);

        $keywords = $this->extractKeywords($jobDescription);
        $title = $this->detectJobTitle($jobDescription);
        $resumeText = $this->normalise($resume);
        $paddedResume = ' '.$resumeText.' ';
        $resumeStems = [];
        foreach (array_filter(explode(' ', $resumeText)) as $token) {
            $resumeStems[$this->stem($token)] = true;
        }

        $matched = [];
        $missing = [];
        $weighted = 0.0;
        $repeated = [];

        foreach ($keywords as $keyword) {
            $match = $this->keywordMatch($keyword, $paddedResume, $resumeStems);

            if ($match === null) {
                $missing[] = $keyword;
                continue;
            }

            $matched[] = $keyword;
            $weighted += $match;

            if (substr_count($paddedResume, ' '.$keyword.' ') > 6) {
                $repeated[] = $keyword;
            }
        }

        $keywordCount = max(1, count($keywords));
        $coverage = $weighted / $keywordCount;
        $keywordScore = (int) round(55 * min(1.0, $coverage * 1.15));

        $structure = $this->structureChecks($resume, $title);
        $structureScore = array_sum(array_map(static fn (array $check): int => $check['ok'] ? $check['weight'] : 0, $structure));

        $format = $this->formatChecks($resume);
        $formatScore = array_sum(array_map(static fn (array $check): int => $check['ok'] ? $check['weight'] : 0, $format));

        $total = max(3, min(98, $keywordScore + $structureScore + $formatScore));

        $fixes = $this->atsFixes($missing, $structure, $format, $resume);

        if ($repeated !== []) {
            array_unshift($fixes, 'Keyword repeated too often: '.implode(', ', array_slice($repeated, 0, 3)).'. Keep each term to once or twice.');
            $fixes = array_slice(array_values(array_unique($fixes)), 0, 6);
        }

        $result = [
            'score' => $total,
            'verdict' => $this->atsVerdict($total),
            'breakdown' => [
                ['label' => 'Keyword coverage', 'score' => $keywordScore, 'max' => 55, 'detail' => sprintf('%d of %d priority keywords found', count($matched), count($keywords))],
                ['label' => 'Resume structure', 'score' => $structureScore, 'max' => 25, 'detail' => 'Standard sections a parser can label'],
                ['label' => 'Formatting hygiene', 'score' => $formatScore, 'max' => 20, 'detail' => 'Length, bullets, and parser safe layout'],
            ],
            'title' => $title,
            'title_match' => $title === '' || $this->titleAppears($title, Str::lower($resume)),
            'matched' => array_slice($matched, 0, 18),
            'missing' => array_slice($missing, 0, 18),
            'structure' => $structure,
            'format' => $format,
            'fixes' => $fixes,
            'source' => 'local',
        ];

        if ($this->client->configured() && count($missing) > 0) {
            try {
                $ai = $this->client->chat([
                    ['role' => 'system', 'content' => 'You audit resumes for ATS compatibility. Return strict JSON only.'],
                    ['role' => 'user', 'content' => sprintf(
                        "The ATS score is %d out of 100. Missing keywords: %s. Give 3 surgical resume edits, each under 25 words, that add these keywords truthfully. Do not invent experience. Return JSON: {\"edits\":[\"...\",\"...\",\"...\"]}",
                        $total,
                        implode(', ', array_slice($missing, 0, 12)),
                    )],
                ], 0.4, 350, true);

                $decoded = $this->decodeJson($ai['content']);
                $edits = array_slice(array_values(array_map('strval', (array) ($decoded['edits'] ?? []))), 0, 3);

                if ($edits !== []) {
                    $result['ai_edits'] = $edits;
                    $result['source'] = 'hybrid:'.$ai['model'];
                }
            } catch (RuntimeException) {
                // Local result stands.
            }
        }

        return $result;
    }

    // -----------------------------------------------------------------
    // Module 4: Profile optimizer
    // -----------------------------------------------------------------

    /**
     * @return array{headlines: array<int, string>, summary: string, bullets: array<int, string>, fixes: array<int, string>, source: string}
     */
    public function optimizeProfile(string $headline, string $summary, string $bullets, string $targetRole = ''): array
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $bullets) ?: [])));
        $local = $this->localProfile($headline, $summary, $lines, $targetRole);

        if (! $this->client->configured()) {
            return [...$local, 'source' => 'local'];
        }

        try {
            $result = $this->client->chat([
                ['role' => 'system', 'content' => 'You rewrite professional profiles for early career candidates. Return strict JSON only. Never invent employers, degrees, or metrics. Use [placeholder] brackets when a number is missing.'],
                ['role' => 'user', 'content' => sprintf(
                    "Target role: %s\nHeadline: %s\nSummary: %s\nExperience bullets:\n%s\n\nRewrite for a LinkedIn profile. Give 3 headline options under 120 characters with one searchable keyword each. Give one summary of 80 words or fewer in first person. Rewrite each bullet to start with an action verb, one measurable placeholder allowed. Remove weak words like passionate, hardworking, aspiring. Return JSON: {\"headlines\":[\"...\"],\"summary\":\"...\",\"bullets\":[\"...\"],\"fixes\":[\"...\"]}",
                    trim($targetRole) === '' ? 'derive from the headline' : Str::limit($targetRole, 80),
                    Str::limit($headline, 200),
                    Str::limit($summary, 1200),
                    Str::limit(implode("\n", $lines), 1500),
                )],
            ], 0.6, 700, true);

            $decoded = $this->decodeJson($result['content']);

            $headlines = array_slice(array_values(array_map('strval', (array) ($decoded['headlines'] ?? []))), 0, 3);
            $newBullets = array_slice(array_values(array_map('strval', (array) ($decoded['bullets'] ?? []))), 0, 12);

            if ($headlines === [] || (string) ($decoded['summary'] ?? '') === '') {
                throw new RuntimeException('Incomplete profile JSON.');
            }

            return [
                'headlines' => $headlines,
                'summary' => (string) $decoded['summary'],
                'bullets' => $newBullets !== [] ? $newBullets : $local['bullets'],
                'fixes' => array_slice(array_values(array_map('strval', (array) ($decoded['fixes'] ?? $local['fixes']))), 0, 4),
                'source' => 'model:'.$result['model'],
            ];
        } catch (RuntimeException) {
            return [...$local, 'source' => 'local'];
        }
    }

    // -----------------------------------------------------------------
    // Local engines
    // -----------------------------------------------------------------

    /**
     * @return array<int, string>
     */
    private function localQuestions(string $role, string $level): array
    {
        $family = $this->roleFamily($role);

        $banks = [
            'engineering' => [
                'Walk me through a project you built end to end. What broke, and how did you fix it?',
                'How do you decide when code is good enough to ship?',
                'Tell me about a bug that took you far longer than expected. What was the root cause?',
            ],
            'data' => [
                'Describe a dataset you cleaned. What was messy, and what did you change?',
                'How would you explain a metric that dropped 20 percent to a non technical manager?',
                'Tell me about an analysis that changed a decision. What was the decision?',
            ],
            'product' => [
                'Pick a product you use daily. What would you cut first, and why?',
                'How do you decide what goes into a first release?',
                'Tell me about a time you had to say no to a stakeholder.',
            ],
            'marketing' => [
                'Walk me through a campaign or content piece you ran. What did the numbers say?',
                'How would you find an audience for a product with no budget?',
                'Tell me about a piece of feedback that changed how you write.',
            ],
            'sales' => [
                'Sell me something you actually use. Keep it under 60 seconds.',
                'How do you handle a prospect who goes quiet after the first call?',
                'Tell me about a time you lost a deal. What did you change afterwards?',
            ],
            'finance' => [
                'Walk me through a spreadsheet or model you built. What was it for?',
                'How do you check your own work before it goes to a manager?',
                'Explain a financial concept you know well to someone outside finance.',
            ],
            'hr' => [
                'A manager wants to fire someone without documentation. What do you do?',
                'How would you run a first week onboarding for ten new hires?',
                'Tell me about a time you handled a confidential situation carefully.',
            ],
            'operations' => [
                'Describe a process you improved. What was the before and after?',
                'A supplier misses a delivery the day before launch. Walk me through your response.',
                'How do you keep track of ten moving tasks without dropping one?',
            ],
            'general' => [
                'Tell me about a project you are proud of. What was your specific contribution?',
                'Describe a time you received difficult feedback. What did you do with it?',
                'Why this role, and why now?',
            ],
        ];

        $questions = $banks[$family];

        if ($level === 'Internship') {
            $questions[2] = 'What do you want to learn in your first three months, and how would you measure it?';
        }

        if ($level === 'Career switch') {
            $questions[2] = 'You are changing fields. What from your previous work transfers directly to this role?';
        }

        return $questions;
    }

    /**
     * @return array{score: int, verdict: string, breakdown: array<int, array{label: string, score: int, max: int, detail: string}>, strengths: array<int, string>, fixes: array<int, string>, model_answer: string, next_probe: string}
     */
    private function localFeedback(string $role, string $level, string $question, string $answer): array
    {
        $text = trim($answer);
        $lower = Str::lower($text);
        $words = str_word_count($text);

        if ($words < 8) {
            return [
                'score' => 18,
                'verdict' => 'Too thin to score',
                'breakdown' => [
                    ['label' => 'Structure', 'score' => 6, 'max' => 35, 'detail' => 'No complete STAR sequence found'],
                    ['label' => 'Evidence', 'score' => 3, 'max' => 25, 'detail' => 'No detail or numbers to weigh'],
                    ['label' => 'Clarity', 'score' => 5, 'max' => 25, 'detail' => 'Too short to judge flow'],
                    ['label' => 'Ownership', 'score' => 4, 'max' => 15, 'detail' => 'Your part is not visible yet'],
                ],
                'strengths' => ['You started the answer instead of stalling.'],
                'fixes' => [
                    'Write at least four sentences so an interviewer can follow your reasoning.',
                    'Use STAR: one line of situation, one of task, three of action, one of result.',
                    'Name the tools, people, or numbers involved. Specifics make it credible.',
                ],
                'model_answer' => $this->modelAnswerTemplate($role, $question),
                'next_probe' => 'Give one concrete example: where were you, what were you asked to do, and what did you do?',
            ];
        }

        $hasSituation = Str::contains($lower, ['when i', 'at my', 'during', 'last year', 'in my', 'at university', 'on a project', 'we were']);
        $hasTask = Str::contains($lower, ['my task', 'i was asked', 'i had to', 'my job', 'the goal', 'responsible', 'needed to']);
        $hasAction = Str::contains($lower, ['i built', 'i led', 'i wrote', 'i organised', 'i organized', 'i analysed', 'i analyzed', 'i designed', 'i ran', 'i fixed', 'i created', 'i coordinated', 'i tested', 'i presented', 'first', 'then', 'so i']);
        $hasResult = Str::contains($lower, ['result', 'outcome', 'as a result', 'which led', 'improved', 'reduced', 'increased', 'delivered', 'learned', 'so the']);

        $numbers = preg_match_all('/\b\d+(\.\d+)?%?\b/', $text);
        $filler = preg_match_all('/\b(um|uh|like|basically|honestly|kind of|sort of|stuff)\b/', $lower);
        $firstPerson = substr_count($lower, ' i ');

        $structure = ($hasSituation ? 9 : 3) + ($hasTask ? 8 : 2) + ($hasAction ? 10 : 3) + ($hasResult ? 8 : 2);
        $evidence = min(25, ($numbers > 0 ? 15 : 4) + (strlen($text) > 240 ? 10 : 5));
        $clarity = $words >= 70 && $words <= 230 ? 25 : ($words < 70 ? 14 : 17);
        $clarity -= min(8, $filler * 2);
        $clarity = max(4, $clarity);
        $ownership = min(15, 5 + $firstPerson * 2);

        $score = max(20, min(96, $structure + $evidence + $clarity + $ownership));

        $missingParts = [];
        if (! $hasSituation) {
            $missingParts[] = 'situation';
        }
        if (! $hasTask) {
            $missingParts[] = 'task';
        }
        if (! $hasAction) {
            $missingParts[] = 'action';
        }
        if (! $hasResult) {
            $missingParts[] = 'result';
        }

        $nextProbe = match (true) {
            ! $hasResult => 'What changed after you acted? Name one result, even a small one.',
            ! $hasAction => 'What three actions did you take, in order? Say what you did, not what the team did.',
            ! $hasTask => 'What were you specifically asked to do, and what was the deadline or constraint?',
            ! $hasSituation => 'Where and when did this happen? Give one line of context before the task.',
            $numbers === 0 => 'What number proves the result? Users, hours saved, marks, headcount, or error rate.',
            default => 'Record a 30 second version. Keep the result last and cut the setup.',
        };

        $breakdown = [
            ['label' => 'Structure', 'score' => $structure, 'max' => 35, 'detail' => $missingParts === [] ? 'Situation, task, action, and result all present' : 'Missing '.implode(', ', $missingParts)],
            ['label' => 'Evidence', 'score' => $evidence, 'max' => 25, 'detail' => $numbers > 0 ? $numbers.' number'.($numbers === 1 ? '' : 's').' used' : 'No numbers found'],
            ['label' => 'Clarity', 'score' => $clarity, 'max' => 25, 'detail' => $words.' words'.($filler > 0 ? ', '.$filler.' filler word'.($filler === 1 ? '' : 's') : '')],
            ['label' => 'Ownership', 'score' => $ownership, 'max' => 15, 'detail' => $firstPerson === 0 ? 'No first-person reference' : $firstPerson.' first-person reference'.($firstPerson === 1 ? '' : 's')],
        ];

        $strengths = [];
        $fixes = [];

        if ($hasSituation) {
            $strengths[] = 'You set a concrete scene before explaining the work. Interviewers can follow you.';
        } else {
            $fixes[] = 'Open with one line of context. Where were you, what was the project, and when.';
        }

        if ($hasAction) {
            $strengths[] = 'Your actions are visible. You described what you did, not just what the team did.';
        } else {
            $fixes[] = 'Name three specific actions you took. Verbs like built, tested, rewrote, and presented beat "worked on".';
        }

        if ($hasResult) {
            $strengths[] = 'You closed the loop with an outcome. That is what separates an answer from an anecdote.';
        } else {
            $fixes[] = 'End with the result. If you have no metric, state what changed for the team or the user.';
        }

        if ($numbers > 0) {
            $strengths[] = 'You used numbers. Quantified answers are easier to trust.';
        } else {
            $fixes[] = 'Add one number, even small. Users, days saved, error rate, marks, or headcount.';
        }

        if ($words > 260) {
            $fixes[] = 'Tighten to 90 seconds. Cut setup and keep the decision, the action, and the result.';
        }

        if ($filler > 2) {
            $fixes[] = 'Cut filler words. Say the point, then the evidence.';
        }

        if ($firstPerson === 0) {
            $fixes[] = 'Say "I" at least twice so your contribution is clear inside the team story.';
        }

        if ($strengths === []) {
            $strengths[] = 'You answered directly without dodging the question.';
        }

        if ($fixes === []) {
            $fixes[] = 'Strong answer. Now practise a 30 second version for follow ups.';
        }

        return [
            'score' => $score,
            'verdict' => $this->verdict($score),
            'breakdown' => $breakdown,
            'strengths' => array_slice($strengths, 0, 3),
            'fixes' => array_slice($fixes, 0, 3),
            'model_answer' => $this->modelAnswerTemplate($role, $question),
            'next_probe' => $nextProbe,
        ];
    }

    private function modelAnswerTemplate(string $role, string $question): string
    {
        return sprintf(
            "In my most recent %s project, the situation was [one line of context]. I was asked to [specific task]. I [action one], then [action two], and finally [action three]. As a result, [outcome with one number if you have it]. The part I would repeat is [lesson].\n\nFor \"%s\", keep it to 90 seconds and finish on the result, not the setup.",
            $role,
            Str::limit($question, 90),
        );
    }

    /**
     * @return array<int, string>
     */
    public function extractKeywords(string $jobDescription): array
    {
        $text = $this->normalise($jobDescription);
        $tokens = array_map(static fn (string $token): string => trim($token, '.'), explode(' ', $text));
        $tokens = array_values(array_filter($tokens, static fn (string $token): bool => $token !== ''));

        $stop = $this->stopWords();
        $generic = ['skill', 'skills', 'experience', 'knowledge', 'work', 'works', 'working', 'role', 'team', 'teams', 'environment', 'ability', 'requirements', 'responsibilities'];
        $unigrams = [];
        $bigrams = [];

        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if (strlen($token) < 3 || in_array($token, $stop, true) || ctype_digit($token)) {
                continue;
            }

            $unigrams[$token] = ($unigrams[$token] ?? 0) + 1;

            if ($i + 1 < $count) {
                $next = $tokens[$i + 1];

                if (strlen($next) >= 3 && ! in_array($next, $stop, true) && ! in_array($next, $generic, true) && ! ctype_digit($next)) {
                    $pair = $token.' '.$next;
                    $bigrams[$pair] = ($bigrams[$pair] ?? 0) + 1;
                }
            }
        }

        arsort($unigrams);
        arsort($bigrams);

        $selected = [];

        // A phrase that appears once in the opening lines is usually the job
        // title or the core requirement, so frequency alone would lose it.
        $intro = $this->normalise(implode(' ', array_slice(preg_split('/\R/u', $jobDescription) ?: [], 0, 2)));
        $intro = ' '.$intro.' ';

        foreach ($bigrams as $pair => $freq) {
            if (count($selected) >= 3) {
                break;
            }

            if (str_contains($intro, ' '.$pair.' ') && ! in_array($pair, $selected, true)) {
                $selected[] = $pair;
            }
        }

        foreach ($bigrams as $pair => $freq) {
            if ($freq >= 2 && count($selected) < 8) {
                $selected[] = $pair;
            }
        }

        foreach ($unigrams as $word => $freq) {
            if (count($selected) >= 16) {
                break;
            }

            $alreadyCovered = false;

            foreach ($selected as $chosen) {
                if (str_contains($chosen, $word)) {
                    $alreadyCovered = true;
                    break;
                }
            }

            if (! $alreadyCovered) {
                $selected[] = $word;
            }
        }

        if ($selected === []) {
            $selected = array_slice(array_keys($unigrams), 0, 10);
        }

        return array_values(array_unique($selected));
    }

    /**
     * Exact match scores full credit, a known synonym slightly less, and a
     * simple stem match less again. Returns null when nothing matches.
     *
     * @param  array<string, bool>  $resumeStems
     */
    private function keywordMatch(string $keyword, string $paddedResume, array $resumeStems): ?float
    {
        if (str_contains($paddedResume, ' '.$keyword.' ')) {
            return 1.0;
        }

        foreach ($this->keywordVariants($keyword) as $variant) {
            if (str_contains($paddedResume, ' '.$variant.' ')) {
                return 0.85;
            }
        }

        if (! str_contains($keyword, ' ')) {
            $stem = $this->stem($keyword);

            if ($stem !== '' && isset($resumeStems[$stem])) {
                return 0.7;
            }
        }

        return null;
    }

    /**
     * Narrow, defensible equivalents only. Distinct tools such as MySQL and
     * PostgreSQL are intentionally not treated as synonyms.
     *
     * @return array<int, string>
     */
    private function keywordVariants(string $keyword): array
    {
        $groups = [
            ['javascript', 'js'],
            ['typescript', 'ts'],
            ['power bi', 'powerbi', 'power-bi'],
            ['machine learning', 'ml'],
            ['artificial intelligence', 'ai'],
            ['communication', 'communicate', 'communicating', 'communicative'],
            ['analysis', 'analyse', 'analyze', 'analytical', 'analytics'],
            ['management', 'managing', 'manage', 'managerial'],
            ['development', 'developing', 'develop'],
            ['customer service', 'customer support', 'client support'],
            ['data cleaning', 'data cleansing', 'clean data'],
            ['human resources', 'hr'],
            ['quality assurance', 'qa'],
            ['search engine optimization', 'seo'],
            ['user experience', 'ux'],
            ['user interface', 'ui'],
            ['e-commerce', 'ecommerce'],
            ['onboarding', 'on boarding'],
        ];

        foreach ($groups as $group) {
            if (in_array($keyword, $group, true)) {
                return array_values(array_diff($group, [$keyword]));
            }
        }

        return [];
    }

    private function stem(string $word): string
    {
        $word = Str::lower($word);

        foreach (['ing', 'ed', 'es', 's'] as $suffix) {
            if (strlen($word) > strlen($suffix) + 3 && Str::endsWith($word, $suffix)) {
                return substr($word, 0, -strlen($suffix));
            }
        }

        return $word;
    }

    private function detectJobTitle(string $jobDescription): string
    {
        $lines = preg_split('/\R/u', trim($jobDescription)) ?: [];
        $first = trim((string) ($lines[0] ?? ''));

        if ($first !== '' && str_word_count($first) <= 8 && ! preg_match('/[.!?]$/', $first)) {
            return Str::limit($first, 60, '');
        }

        if (preg_match('/(?:hiring|seeking|looking for|role|position)\s*(?:an?|:)?\s*([a-z][a-z0-9 ,\/-]{2,50})/i', $jobDescription, $matches) === 1) {
            return trim(Str::limit($matches[1], 60, ''));
        }

        return $first === '' ? '' : Str::limit($first, 60, '');
    }

    private function titleAppears(string $title, string $lowerResume): bool
    {
        $clean = preg_replace('/[^a-z0-9 ]/', ' ', Str::lower($title)) ?? '';
        $tokens = array_values(array_filter(
            explode(' ', trim($clean)),
            static fn (string $token): bool => strlen($token) >= 3 && ! in_array($token, ['and', 'the', 'for', 'with', 'job'], true),
        ));

        if ($tokens === []) {
            return true;
        }

        $found = 0;
        foreach ($tokens as $token) {
            if (str_contains($lowerResume, $token)) {
                $found++;
            }
        }

        return ($found / count($tokens)) >= 0.7;
    }

    /**
     * @return array<int, array{label: string, ok: bool, weight: int, hint: string}>
     */
    private function structureChecks(string $resume, string $title = ''): array
    {
        $lower = Str::lower($resume);

        return [
            ['label' => 'Contact email', 'ok' => (bool) preg_match('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i', $resume), 'weight' => 5, 'hint' => 'Add a plain email address in the header, not inside an image.'],
            ['label' => 'Phone or LinkedIn', 'ok' => (bool) preg_match('/(\+?\d[\d\s\-().]{7,})|linkedin\.com/i', $resume), 'weight' => 4, 'hint' => 'Add a phone number or a LinkedIn URL in plain text.'],
            ['label' => 'Experience section', 'ok' => Str::contains($lower, ['experience', 'employment', 'work history']), 'weight' => 5, 'hint' => 'Use the standard heading Experience.'],
            ['label' => 'Education section', 'ok' => Str::contains($lower, ['education', 'qualification', 'academic']), 'weight' => 3, 'hint' => 'Add an Education section with degree and institution.'],
            ['label' => 'Skills section', 'ok' => Str::contains($lower, ['skills', 'tools', 'technologies']), 'weight' => 4, 'hint' => 'Add a Skills block with the exact tool names from the posting.'],
            ['label' => 'Summary or objective', 'ok' => Str::contains($lower, ['summary', 'profile', 'objective', 'about']), 'weight' => 4, 'hint' => 'Open with a three line summary aimed at this role.'],
            ['label' => 'Target title present', 'ok' => $title === '' || $this->titleAppears($title, $lower), 'weight' => 0, 'hint' => 'Put the exact job title from the posting in your header or summary.'],
        ];
    }

    /**
     * @return array<int, array{label: string, ok: bool, weight: int, hint: string}>
     */
    private function formatChecks(string $resume): array
    {
        $words = str_word_count($resume);
        $bulletLines = preg_match_all('/^\s*[-•*·]\s+/m', $resume);
        $tableLines = preg_match_all('/^.*\|.*\|.*$/m', $resume);
        $noise = (bool) preg_match('/\b(photo|date of birth|marital status|religion|cnic|national id)\b/i', $resume);

        return [
            ['label' => 'Readable length', 'ok' => $words >= 220 && $words <= 900, 'weight' => 8, 'hint' => 'Aim for 220 to 900 words, one to two pages.'],
            ['label' => 'Bullet points used', 'ok' => $bulletLines >= 3, 'weight' => 4, 'hint' => 'Use simple dash bullets, at least three.'],
            ['label' => 'No table layout', 'ok' => $tableLines < 2, 'weight' => 4, 'hint' => 'Remove table and column layouts. Parsers read them out of order.'],
            ['label' => 'No personal noise', 'ok' => ! $noise, 'weight' => 4, 'hint' => 'Remove photo, date of birth, marital status, religion, and ID numbers.'],
        ];
    }

    /**
     * @param  array<int, string>  $missing
     * @param  array<int, array{label: string, ok: bool, weight: int, hint: string}>  $structure
     * @param  array<int, array{label: string, ok: bool, weight: int, hint: string}>  $format
     * @return array<int, string>
     */
    private function atsFixes(array $missing, array $structure, array $format, string $resume): array
    {
        $fixes = [];

        if ($missing !== []) {
            $fixes[] = 'Add these keywords where they are true: '.implode(', ', array_slice($missing, 0, 6)).'.';
        }

        foreach ([...$structure, ...$format] as $check) {
            if (! $check['ok']) {
                $fixes[] = $check['hint'];
            }
        }

        if (! preg_match('/\b\d+(\.\d+)?%?\b/', $resume)) {
            $fixes[] = 'Add at least one number per role. Volume, users, marks, time saved, error rate.';
        }

        if ($fixes === []) {
            $fixes[] = 'Score is healthy. Tailor the summary to this posting and re run before applying.';
        }

        return array_slice(array_values(array_unique($fixes)), 0, 6);
    }

    /**
     * @param  array<int, string>  $lines
     * @return array{headlines: array<int, string>, summary: string, bullets: array<int, string>, fixes: array<int, string>}
     */
    private function localProfile(string $headline, string $summary, array $lines, string $targetRole = ''): array
    {
        $role = trim($targetRole) !== ''
            ? Str::ucfirst(Str::limit(trim($targetRole), 60, ''))
            : $this->deriveRole($headline);
        $skills = $this->detectSkills(Str::lower($headline.' '.$summary.' '.implode(' ', $lines)));
        $skillPhrase = $skills === [] ? 'structured delivery' : implode(', ', array_slice($skills, 0, 3));

        $headlines = [
            Str::limit($role.' | '.$skillPhrase, 118, ''),
            Str::limit($role.' building '.($skills === [] ? 'measurable outcomes' : $skills[0].' systems'), 118, ''),
            Str::limit($role.' · '.$skillPhrase.' · open to '.($skills === [] ? 'early career' : 'new roles'), 118, ''),
        ];

        $cleanSummary = $this->cleanSummary($summary, $role, $skills);
        $bullets = [];
        $fixes = [];

        foreach ($lines as $line) {
            $rewritten = $this->rewriteBullet($line);

            if ($rewritten !== '') {
                $bullets[] = $rewritten;
            }
        }

        if (Str::contains(Str::lower($summary), ['passionate', 'hardworking', 'aspiring', 'team player'])) {
            $fixes[] = 'Weak words removed. Replace claims like passionate with one proof point.';
        }

        if (! preg_match('/\b\d/', $summary.implode(' ', $lines))) {
            $fixes[] = 'Add one number somewhere. Without a metric every claim reads the same.';
        }

        if (count($bullets) > 6) {
            $fixes[] = 'Keep six bullets or fewer. Recruiters skim, they do not read.';
        }

        if ($fixes === []) {
            $fixes[] = 'Solid base. Now mirror three exact keywords from the job post you are targeting.';
        }

        return [
            'headlines' => array_values(array_filter($headlines)),
            'summary' => $cleanSummary,
            'bullets' => array_slice($bullets, 0, 10),
            'fixes' => array_slice($fixes, 0, 3),
        ];
    }

    private function cleanSummary(string $summary, string $role, array $skills): string
    {
        $summary = trim(preg_replace('/\s+/', ' ', $summary) ?? '');

        $weak = ['passionate about', 'hardworking', 'aspiring', 'team player', 'go getter', 'results driven', 'detail oriented'];
        foreach ($weak as $phrase) {
            $summary = preg_replace('/\b'.preg_quote($phrase, '/').'\b/i', '', $summary) ?? $summary;
        }

        $summary = trim(preg_replace('/\s+/', ' ', $summary) ?? '');

        if ($summary === '') {
            $skillLine = $skills === [] ? 'structured problem solving' : implode(', ', array_slice($skills, 0, 3));

            return sprintf(
                'Early career %s with hands on work in %s. I like clear briefs, measurable outcomes, and documentation other people can follow. Currently looking for a role where I can own delivery end to end.',
                Str::lower($role),
                $skillLine,
            );
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $summary) ?: [];
        $sentences = array_slice($sentences, 0, 3);

        return Str::limit(implode(' ', $sentences), 480);
    }

    private function rewriteBullet(string $line): string
    {
        $line = trim(preg_replace('/^\s*[-•*·]\s*/u', '', $line) ?? '');

        if ($line === '') {
            return '';
        }

        $replacements = [
            '/^responsible for\s+/i' => 'Owned ',
            '/^worked on\s+/i' => 'Delivered ',
            '/^helped (to\s+)?/i' => 'Drove ',
            '/^assisted (with\s+)?/i' => 'Supported ',
            '/^participated in\s+/i' => 'Contributed to ',
            '/^was involved in\s+/i' => 'Contributed to ',
            '/^duties included\s+/i' => 'Ran ',
            '/^tasked with\s+/i' => 'Led ',
        ];

        foreach ($replacements as $pattern => $replacement) {
            if (preg_match($pattern, $line)) {
                $line = preg_replace($pattern, $replacement, $line) ?? $line;
                break;
            }
        }

        $line = ucfirst($line);

        if (! preg_match('/\b\d/', $line)) {
            $line = rtrim($line, '.').' [add one number]';
        }

        return Str::limit($line, 190);
    }

    private function deriveRole(string $headline): string
    {
        $headline = trim($headline);

        if ($headline === '') {
            return 'Early career professional';
        }

        $parts = preg_split('/[|·•,–—-]/u', $headline) ?: [];
        $role = trim((string) ($parts[0] ?? $headline));

        $lower = Str::lower($role);
        foreach (['passionate about', 'aspiring', 'hardworking', 'looking for', 'seeking', 'interested in', 'enthusiast'] as $phrase) {
            $position = strpos($lower, $phrase);
            if ($position !== false) {
                $role = trim(substr($role, 0, $position));
                break;
            }
        }

        $role = trim(preg_replace('/\s+/', ' ', $role) ?? '');

        return $role === '' ? 'Early career professional' : Str::ucfirst(Str::limit($role, 60, ''));
    }

    /**
     * @return array<int, string>
     */
    private function detectSkills(string $text): array
    {
        $known = [
            'python', 'javascript', 'typescript', 'react', 'laravel', 'php', 'sql', 'excel',
            'figma', 'canva', 'seo', 'copywriting', 'photoshop', 'tableau', 'power bi',
            'communication', 'research', 'project management', 'stakeholder management',
            'payroll', 'recruitment', 'onboarding', 'data analysis', 'customer service',
            'sales', 'accounting', 'bookkeeping', 'supply chain', 'logistics',
        ];

        $found = [];

        foreach ($known as $skill) {
            if (str_contains($text, $skill)) {
                $found[] = Str::title($skill);
            }
        }

        return array_values(array_unique($found));
    }

    private function roleFamily(string $role): string
    {
        $role = Str::lower($role);

        return match (true) {
            Str::contains($role, ['software', 'engineer', 'developer', 'frontend', 'backend', 'full stack']) => 'engineering',
            Str::contains($role, ['data', 'analytic', 'machine learning', 'ai ']) => 'data',
            Str::contains($role, ['product', 'project', 'program']) => 'product',
            Str::contains($role, ['marketing', 'content', 'brand', 'social']) => 'marketing',
            Str::contains($role, ['sales', 'business development', 'account']) => 'sales',
            Str::contains($role, ['finance', 'account', 'audit']) => 'finance',
            Str::contains($role, ['hr', 'people', 'talent', 'recruit']) => 'hr',
            Str::contains($role, ['operations', 'supply', 'logistics']) => 'operations',
            default => 'general',
        };
    }

    private function atsVerdict(int $score): string
    {
        return match (true) {
            $score >= 80 => 'Strong match',
            $score >= 65 => 'Promising, needs tailoring',
            $score >= 45 => 'Partial match',
            default => 'Needs a rewrite',
        };
    }

    private function verdict(int $score): string
    {
        return match (true) {
            $score >= 85 => 'Hire signal',
            $score >= 70 => 'Close, tighten the evidence',
            $score >= 55 => 'Workable, needs structure',
            default => 'Rebuild the answer',
        };
    }

    private function normalise(string $text): string
    {
        $text = Str::lower($text);
        $text = preg_replace('/[^a-z0-9+#.]+/', ' ', $text) ?? '';

        return trim(preg_replace('/\s+/', ' ', $text) ?? '');
    }

    /**
     * @return array<int, string>
     */
    private function stopWords(): array
    {
        return array_merge(explode(' ', 'a about above after again against all am an and any are as at be because been before being below between both but by can could did do does doing down during each few for from further had has have having he her here hers herself him himself his how i if in into is it its itself just me more most my myself no nor not of off on once only or other our ours ourselves out over own same she should so some such than that the their theirs them themselves then there these they this those through to too under until up very was we were what when where which while who whom why will with would you your yours yourself yourselves also across using use used work working role team teams company job jobs candidate candidates ability able strong good great excellent required requirements responsibilities responsible experience years year including etc plus preferred must may well within per new help support ensure based like looking join us our we you will who what where when why how online apply position opportunity benefits salary month time full part'), array_values(array_filter(explode(' ', 'remote hybrid office manager management lead senior junior entry level intern internship graduate degree bachelor master bachelors masters university college school knowledge understanding familiarity proficiency plus bonus nice must have has with and need needs needed'))));
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJson(string $content): array
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?|```$/m', '', $content) ?? $content;

        $start = strpos($content, '{');
        $end = strrpos($content, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $content = substr($content, $start, $end - $start + 1);
        }

        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Model did not return usable JSON.');
        }

        return $decoded;
    }
}
