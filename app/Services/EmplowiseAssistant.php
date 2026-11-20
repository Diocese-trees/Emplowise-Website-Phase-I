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
    public function __construct(private readonly AlibabaCloudClient $client)
    {
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array{reply: string, intent: string, model: string, source: string}
     */
    public function reply(string $message, array $history = []): array
    {
        $intent = $this->detectIntent($message);

        if (! $this->client->configured()) {
            return [
                'reply' => $this->shorten($this->fallbackReply($intent)),
                'intent' => $intent,
                'model' => 'local-rules',
                'source' => 'fallback',
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
            ];
        } catch (RuntimeException) {
            return [
                'reply' => $this->shorten($this->fallbackReply($intent)),
                'intent' => $intent,
                'model' => 'local-rules',
                'source' => 'fallback',
            ];
        }
    }

    public function detectIntent(string $message): string
    {
        $text = Str::lower(trim($message));

        return match (true) {
            Str::contains($text, ['thanks', 'thank you', 'shukriya', 'bye', 'goodbye']) => 'thanks',
            Str::contains($text, ['price', 'cost', 'pkr', 'package', 'starter', 'business', 'enterprise', 'how much', 'budget', 'charge', 'quote', 'fee']) => 'pricing',
            Str::contains($text, ['foundation', 'growth', 'scale', 'legacy', 'replit', 'old tier']) => 'legacy',
            Str::contains($text, ['pending input', 'pending', 'tbd', 'not disclosed', 'unclear', 'not confirmed']) => 'pending',
            Str::contains($text, ['audit', 'free hr', 'book', 'meeting', 'talk to', 'sign up', 'onboard me', 'consultation', 'get started']) => 'audit',
            Str::contains($text, ['student', 'internship', 'graduate', 'fresh grad', 'prompt library', 'linkedin']) => 'student',
            Str::contains($text, ['resume', 'cv', 'ats', 'interview', 'cover letter']) => 'tools',
            Str::contains($text, ['payroll', 'salary', 'tax', 'attendance', 'leave', 'onboarding', 'records', 'document']) => 'payroll',
            Str::contains($text, ['performance', 'review', 'appraisal', 'conflict', 'manager', 'kpi', 'okr']) => 'performance',
            Str::contains($text, ['policy', 'policies', 'sop', 'compliance', 'legal', 'contract', 'labour', 'labor']) => 'policy',
            Str::contains($text, ['service', 'services', 'what do you do', 'what you do', 'recruit', 'hr support', 'offer']) => 'services',
            Str::contains($text, ['contact', 'email', 'phone', 'whatsapp', 'reach you', 'number', 'inbox', 'address', 'office']) => 'contact',
            Str::contains($text, ['job', 'career', 'hiring me', 'apply', 'vacancy', 'open role']) => 'careers',
            Str::contains($text, ['why', 'different', 'better than', 'vs ', 'instead of', 'in-house', 'in house', 'whey']) => 'why',
            Str::contains($text, ['who are you', 'who is', 'about you', 'about emplowise', 'what is emplowise', 'your team', 'founder', 'ceo', 'company']) => 'who',
            Str::contains($text, ['where', 'location', 'based', 'city', 'onsite', 'on-site', 'pakistan', 'remote', 'international']) => 'where',
            Str::contains($text, ['when', 'timeline', 'how soon', 'start date', 'how long', 'duration', 'notice period']) => 'when',
            preg_match('/\b(hi|hello|hey|salam|assalam|yo|sup)\b/', $text) === 1 && Str::wordCount($text) <= 5 => 'greeting',
            default => 'general',
        };
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
3. Package inclusions, recruitment fee terms, payroll tax and disbursement scope, legal qualifications, and representative allocation are Pending Input. Say so plainly. Never guess.
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
Student hub: five free tools. Interview prep, ATS score, resume builder, profile optimizer, prompt library. No account needed.
Pending Input: {$pending}.
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
            return 'Ask me about pricing, services, the free audit, or the Student Hub.';
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
            'pricing' => "Starter is PKR 40,000 a month for up to 20 employees.\nBusiness is PKR 75,000 a month for up to 50.\nEnterprise is custom above 50.\nExact inclusions are Pending Input, so the audit scopes them.",
            'legacy' => "The 2025 Replit export listed Foundation at PKR 40,000, Growth at PKR 65,000, and Scale at PKR 100,000.\nThose three tiers are not sold on this site.\nCurrent packages are Starter, Business, and Enterprise.",
            'audit' => "The Free HR Audit is the front door.\nFill the form at /free-hr-audit and pick a morning, afternoon, or evening slot.\nWe map payroll, performance, and policy gaps before quoting anything.\nNo cost, no obligation.",
            'student' => "Student Hub is free and no account is needed.\nInside: mock interviews, an ATS score out of 100, resume builder, profile optimiser, and 14 prompts.\nOpen /student-hub and pick a tool.",
            'tools' => "The ATS tool scores your resume against a job description out of 100.\nThe resume builder and profile optimiser rewrite weak bullets.\nThe prompt library holds 14 ready prompts.\nAll free at /student-hub.",
            'payroll' => "Payroll, leave and attendance, onboarding, and employee records are confirmed services.\nTax, statutory contributions, and disbursement scope are Pending Input.\nSo we confirm those in writing before work starts.",
            'performance' => "Performance support, conflict guidance, organisational assessments, and monthly HR reporting are confirmed.\nAssessment cadence is Pending Input.\nThe review cycle stays yours, we run the structure.",
            'policy' => "Policies and SOPs, plus legal and compliance support, are confirmed services.\nWe coordinate compliance, we do not promise legal outcomes.\nJurisdiction coverage is Pending Input.",
            'services' => "Confirmed scope: recruitment, onboarding, records, leave and attendance, policies, payroll, compliance support, performance, employee relations, reporting, consultation, assessments, and a dedicated HR rep.\nAll of it is delivered remote.",
            'pending' => "Pending Input means not confirmed yet: inclusions, recruitment fees, payroll tax scope, legal coverage, rep allocation, cadence, contract terms, and direct contact.\nWe publish them as open rather than guessing.\nThe audit pins each one down in writing.",
            'contact' => "The Free HR Audit form is the only verified contact channel right now.\nDirect phone and inbox are Pending Input.\nUse /free-hr-audit and we reply from there.",
            'careers' => "Open roles are Pending Input and nothing is published on this site yet.\nIf you want help getting hired, the Student Hub tools are free at /student-hub.",
            'why' => "We sit between informal help and a full enterprise HR department.\nConfirmed structure, published prices, and a named HR representative.\nEvery process you get is documented, not improvised.\nWe do not promise legal outcomes or invented savings.",
            'who' => "Emplowise HR Services does remote HR outsourcing for startups and SMEs in Pakistan.\nConsultants backed by specialist support, plus a dedicated HR representative.\nDirect access to your CEO or founder is part of the model.",
            'where' => "We are based in Pakistan and deliver fully remote.\nThere is no walk-in office in this build.\nInternational expansion is planned long term, not live yet.",
            'when' => "The method runs Assess, Identify, Improve, Review.\nReview means monthly reporting with a quarterly assessment.\nStart dates and contract length are Pending Input.",
            'greeting' => "hey. I am the Emplowise assistant.\nAsk about packages, confirmed services, the free audit, or the Student Hub.\nI keep it short and never invent numbers.",
            'thanks' => "Anytime.\nIf you want a real scope, the audit at /free-hr-audit is the next step.",
            default => "Emplowise runs remote HR for growing teams in Pakistan.\nAsk about pricing, services, the free audit, or the Student Hub.\nIf it is not in our verified data, I say Pending Input.",
        };
    }
}
