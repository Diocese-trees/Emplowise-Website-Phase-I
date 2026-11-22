<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * The Emplowise assistant. Quirky Gen Z voice, strictly grounded answers.
 * The model is grounded on the confirmed business dataset in config/emplowise.php.
 * When the model is unavailable the assistant answers from a deterministic
 * rule set with the same facts and the same tone.
 */
final class EmplowiseAssistant
{
    public function __construct(private readonly AlibabaCloudClient $client) {}

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array{reply: string, intent: string, model: string, source: string, suggestions: array<int, array{label: string, query: string}>}
     */
    public function reply(string $message, array $history = [], string $previousIntent = ''): array
    {
        $intent = $this->detectIntent($message, $previousIntent);
        $suggestions = $this->suggestions($intent);

        if (! $this->client->configured()) {
            return [
                'reply' => $this->shorten($this->fallbackReply($intent)),
                'intent' => $intent,
                'model' => 'local-rules',
                'source' => 'fallback',
                'suggestions' => $suggestions,
            ];
        }

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ...array_slice($history, -6),
            ['role' => 'user', 'content' => $message],
        ];

        try {
            $result = $this->client->chat($messages, 0.7, 320);

            return [
                'reply' => $this->shorten($this->tidy($result['content'])),
                'intent' => $intent,
                'model' => $result['model'],
                'source' => 'model',
                'suggestions' => $suggestions,
            ];
        } catch (RuntimeException) {
            return [
                'reply' => $this->shorten($this->fallbackReply($intent)),
                'intent' => $intent,
                'model' => 'local-rules',
                'source' => 'fallback',
                'suggestions' => $suggestions,
            ];
        }
    }

    /**
     * Score every intent and take the best match. First-match rules lose
     * mixed messages such as "how much is the free audit", so phrases count
     * double and the previous turn acts as a weak prior for follow-ups.
     */
    public function detectIntent(string $message, string $previousIntent = ''): string
    {
        $text = Str::lower(trim($message));
        $words = preg_split('/\s+/', $text) ?: [];
        $wordCount = count(array_filter($words));

        $rules = [
            'thanks' => ['thanks', 'thank you', 'shukriya', 'bye', 'goodbye'],
            'pricing' => ['price', 'pricing', 'cost', 'pkr', 'package', 'packages', 'starter', 'business', 'enterprise', 'how much', 'budget', 'charge', 'quote', 'fee', 'per month'],
            'pending' => ['pending', 'tbd', 'not disclosed', 'not published', 'unclear', 'not confirmed'],
            'audit' => ['audit', 'free hr', 'free audit', 'book', 'meeting', 'talk to', 'sign up', 'onboard me', 'consultation', 'get started'],
            'student' => ['student', 'student hub', 'career hub', 'emplostart', 'emplo start', 'internship', 'graduate', 'fresh grad', 'prompt library', 'linkedin'],
            'tools' => ['resume', 'cv', 'ats', 'interview', 'cover letter'],
            'payroll' => ['payroll', 'salary', 'tax', 'attendance', 'leave', 'onboarding', 'records', 'document'],
            'performance' => ['performance', 'review', 'appraisal', 'conflict', 'manager', 'kpi', 'okr'],
            'policy' => ['policy', 'policies', 'sop', 'compliance', 'legal', 'contract', 'labour', 'labor'],
            'services' => ['service', 'services', 'what do you do', 'what you do', 'recruit', 'hr support', 'offer'],
            'contact' => ['contact', 'email', 'phone', 'whatsapp', 'reach you', 'number', 'inbox', 'address', 'office'],
            'careers' => ['job', 'career', 'hiring me', 'apply', 'vacancy', 'open role'],
            'why' => ['why', 'different', 'better than', 'vs', 'instead of', 'in-house', 'in house'],
            'who' => ['who are you', 'who is', 'about you', 'about emplowise', 'what is emplowise', 'your team', 'founder', 'ceo', 'company'],
            'where' => ['where', 'location', 'based', 'city', 'onsite', 'on-site', 'pakistan', 'remote', 'international'],
            'when' => ['when', 'timeline', 'how soon', 'start date', 'how long', 'duration', 'notice period'],
        ];

        if ($wordCount <= 5) {
            $rules['greeting'] = ['hi', 'hello', 'hey', 'salam', 'assalam', 'yo', 'sup'];
        }

        $scores = [];

        foreach ($rules as $intent => $phrases) {
            $score = 0.0;

            foreach ($phrases as $phrase) {
                $pattern = str_contains($phrase, ' ')
                    ? '/'.preg_quote($phrase, '/').'/'
                    : '/\b'.preg_quote($phrase, '/').'\b/';

                if (preg_match($pattern, $text) === 1) {
                    $score += str_contains($phrase, ' ') ? 2.0 : 1.0;
                }
            }

            if ($score > 0) {
                $scores[$intent] = $score;
            }
        }

        if ($previousIntent !== '' && isset($rules[$previousIntent])) {
            $scores[$previousIntent] = ($scores[$previousIntent] ?? 0) + ($wordCount <= 8 ? 0.9 : 0.4);
        }

        if ($scores === []) {
            return 'general';
        }

        arsort($scores);
        $intents = array_keys($scores);
        $top = $scores[$intents[0]];
        $second = count($intents) > 1 ? $scores[$intents[1]] : 0.0;

        // A near tie means the message could belong to either intent, so ask
        // rather than guessing. The previous turn breaks the tie when it can.
        if ($second > 0 && ($top - $second) < 0.5) {
            if ($previousIntent !== '' && isset($scores[$previousIntent]) && $scores[$previousIntent] === $top) {
                return $previousIntent;
            }

            return 'general';
        }

        return $intents[0];
    }

    /**
     * @return array<int, array{label: string, query: string}>
     */
    private function suggestions(string $intent): array
    {
        $groups = [
            'pricing' => [
                ['label' => 'What is included', 'query' => 'What is included in the packages?'],
                ['label' => 'Free audit', 'query' => 'How does the free HR audit work?'],
                ['label' => 'Services', 'query' => 'Which HR services do you run?'],
            ],
            'student' => [
                ['label' => 'ATS score', 'query' => 'How does the ATS score work?'],
                ['label' => 'Mock interview', 'query' => 'How does the mock interview tool work?'],
                ['label' => 'Resume builder', 'query' => 'Is the resume builder free?'],
            ],
            'tools' => [
                ['label' => 'EmploStart', 'query' => 'What is in the EmploStart?'],
                ['label' => 'Prompt library', 'query' => 'What is in the prompt library?'],
                ['label' => 'Profile help', 'query' => 'Can you help with my LinkedIn profile?'],
            ],
            'contact' => [
                ['label' => 'Free audit', 'query' => 'How do I book the free HR audit?'],
                ['label' => 'Where you are', 'query' => 'Where is Emplowise based?'],
                ['label' => 'Who you are', 'query' => 'Who is Emplowise?'],
            ],
            'careers' => [
                ['label' => 'EmploStart', 'query' => 'What free help do you offer students?'],
                ['label' => 'Services', 'query' => 'Which HR services do you run?'],
                ['label' => 'Free audit', 'query' => 'How does the free HR audit work?'],
            ],
        ];

        $fallback = [
            ['label' => 'Pricing', 'query' => 'What do the packages cost?'],
            ['label' => 'Services', 'query' => 'Which HR services do you run?'],
            ['label' => 'Free audit', 'query' => 'How does the free HR audit work?'],
        ];

        return $groups[$intent] ?? $fallback;
    }

    private function systemPrompt(): string
    {
        $brand = config('emplowise.brand');
        $packages = collect(config('emplowise.packages'))
            ->map(fn (array $p): string => sprintf('%s: %s %s for %s', $p['name'], $p['price_display'], $p['period'], $p['capacity']))
            ->implode('; ');
        $services = collect(config('emplowise.services'))->pluck('name')->implode(', ');
        $pending = implode('; ', array_slice(config('emplowise.pending_inputs'), 0, 7));

        return <<<PROMPT
You are the Emplowise assistant, the chat voice on the Emplowise HR Services website.

VOICE
You are a Gen Z HR bestie: warm, quick, a little playful. Short sentences. Lowercase openers are fine. Lines like "ok so", "here is the tea", "no cap" are allowed sparingly, at most once per reply. Never more than one emoji, and only when the user is casual. You are never sarcastic about money, legal risk, or someone's job. Clarity beats slang. If the user sounds formal, match them and drop the slang.

HARD RULES
1. Use only the verified facts below. Never invent prices, clients, results, savings, package inclusions, or timelines.
2. Pricing is exact. Starter is PKR 40,000 per month for up to 20 employees. Business is PKR 75,000 per month for up to 50 employees. Enterprise is custom for 50 or more employees.
3. Package inclusions, recruitment fee terms, payroll tax and disbursement scope, legal qualifications, and representative allocation are confirmed in writing after the free HR audit. Say so plainly. Never guess.
4. Never promise legal compliance, guaranteed productivity, conflict elimination, or board authority.
5. Never call Emplowise the cheapest. Never describe the platform as proprietary AI software.
6. If the question needs a real scoping conversation, point to the Free HR Audit at /free-hr-audit.
7. Answer in 40 words or fewer, maximum four short sentences, one idea per line. Hard cap is five lines. Plain text only: no markdown, no headings, no asterisks, no numbered lists.

VERIFIED FACTS
Brand: Emplowise HR Services. Positioning: {$brand['headline']}. Category: {$brand['category']}. Market: {$brand['market']}. Expansion: {$brand['expansion']}. Audience: {$brand['audience']}.
Delivery: remote HR support, consultants with specialist support, a dedicated HR representative, direct access to the client CEO or founder.
Packages: {$packages}.
Confirmed services: {$services}.
Method: Assess, Identify, Improve, Review.
EmploStart: five free career tools. Interview prep, ATS score, resume builder, profile optimizer, prompt library. No account needed.
Confirmed in writing after the free HR audit: {$pending}.
Guardrails: no invented metrics, no client names, no guarantees, no board authority.
PROMPT;
    }

    private function tidy(string $reply): string
    {
        $reply = str_replace(['**', '##', '#'], '', $reply);
        $reply = preg_replace('/[ \t]+/', ' ', $reply) ?? $reply;

        return trim($reply);
    }

    /**
     * Hard cap: four short sentences on their own lines, never more than five
     * rendered lines, under 260 characters. Keeps every answer terse.
     */
    private function shorten(string $reply): string
    {
        $reply = trim(preg_replace('/\s+/', ' ', $reply) ?? $reply);
        $reply = str_replace(['*', '`'], '', $reply);

        if ($reply === '') {
            return 'Ask me about pricing, services, the free audit, or the EmploStart.';
        }

        preg_match_all('/[^.!?]+[.!?]+|[^.!?]+$/', $reply, $matches);
        $sentences = array_values(array_filter(
            array_map('trim', $matches[0] ?? []),
            static fn (string $s): bool => $s !== '',
        ));

        if ($sentences === []) {
            $sentences = [Str::limit($reply, 220)];
        }

        $lines = [];
        $length = 0;

        foreach ($sentences as $sentence) {
            if (count($lines) >= 4) {
                break;
            }
            if ($length + Str::length($sentence) > 260 && $lines !== []) {
                break;
            }
            $lines[] = $sentence;
            $length += Str::length($sentence) + 1;
        }

        if ($lines === []) {
            $lines = [Str::limit($reply, 220)];
        }

        return implode("\n", $lines);
    }

    private function fallbackReply(string $intent): string
    {
        return match ($intent) {
            'pricing' => "Starter is PKR 40,000 a month for up to 20 employees.\nBusiness is PKR 75,000 a month for up to 50.\nEnterprise is custom above 50.\nExact inclusions are confirmed in writing after the free HR audit, so the audit scopes them.",
            'audit' => "The Free HR Audit is the front door.\nFill the form at /free-hr-audit and pick a morning, afternoon, or evening slot.\nWe map payroll, performance, and policy gaps before quoting anything.\nNo cost, no obligation.",
            'student' => "EmploStart is free and no account is needed.\nInside: mock interviews, an ATS score out of 100, resume builder, profile optimiser, and 14 prompts.\nOpen /emplostart and pick a tool.",
            'tools' => "The ATS tool scores your resume against a job description out of 100.\nThe resume builder and profile optimiser rewrite weak bullets.\nThe prompt library holds 14 ready prompts.\nAll free at /emplostart, and nothing you paste is stored.",
            'payroll' => "Payroll, leave and attendance, onboarding, and employee records are confirmed services.\nTax, statutory contributions, and disbursement scope are confirmed in writing after the free HR audit.\nSo we confirm those before work starts.",
            'performance' => "Performance support, conflict guidance, organisational assessments, and monthly HR reporting are confirmed.\nAssessment cadence is confirmed in writing after the free HR audit.\nThe review cycle stays yours, we run the structure.",
            'policy' => "Policies and SOPs, plus legal and compliance support, are confirmed services.\nWe coordinate compliance, we do not promise legal outcomes.\nJurisdiction coverage is confirmed in writing after the free HR audit.",
            'services' => "Three pillars hold fourteen confirmed services: Payroll, Performance Management, and Policy Making.\nScope runs from recruitment and onboarding to payroll, compliance, performance, records, reporting, and a dedicated HR rep.\nAll of it is delivered remote.",
            'pending' => "Some details are confirmed in writing after the free HR audit: inclusions, recruitment fees, payroll tax scope, legal coverage, rep allocation, cadence, contract terms, and direct contact.\nWe list them as open rather than guessing.\nThe audit pins each one down.",
            'contact' => "The Free HR Audit form is the only verified contact channel right now.\nDirect phone and inbox are confirmed in writing after the free HR audit.\nUse /free-hr-audit and we reply from there.",
            'careers' => "Open roles are not published yet, so nothing is listed on this site.\nIf you want help getting hired, the EmploStart tools are free at /emplostart.",
            'why' => "We sit between informal help and a full enterprise HR department.\nConfirmed structure, published prices, and a named HR representative.\nEvery process you get is documented, not improvised.\nWe do not promise legal outcomes or invented savings.",
            'who' => "Emplowise HR Services does remote HR outsourcing for startups and SMEs in Pakistan.\nConsultants backed by specialist support, plus a dedicated HR representative.\nDirect access to your CEO or founder is part of the model.",
            'where' => "We are based in Pakistan and deliver fully remote.\nThere is no walk-in office in this build.\nInternational expansion is planned long term, not live yet.",
            'when' => "The method runs Assess, Identify, Improve, Review.\nReview means monthly reporting with a quarterly assessment.\nStart dates and contract length are confirmed in writing after the free HR audit.",
            'greeting' => "hey. I am the Emplowise assistant.\nAsk about packages, confirmed services, the free audit, or the EmploStart.\nI keep it short and never invent numbers.",
            'thanks' => "Anytime.\nIf you want a real scope, the audit at /free-hr-audit is the next step.",
            default => "Emplowise runs remote HR for growing teams in Pakistan.\nAsk about pricing, services, the free audit, or the EmploStart.\nIf it is not in our verified data, I point you to the free audit.",
        };
    }
}
