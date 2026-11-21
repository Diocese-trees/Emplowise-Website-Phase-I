<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuditRequest;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SiteAndApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_seven_pages_render(): void
    {
        $pages = [
            '/' => 'Remote HR services in Pakistan',
            '/what-we-are' => 'HR outsourcing company in Pakistan',
            '/what-we-do' => 'HR operations outsourcing',
            '/pricing' => 'PKR 40,000',
            '/student-hub' => 'Free ATS resume checker',
            '/faq' => 'HR outsourcing questions',
            '/free-hr-audit' => 'free HR audit in Pakistan',
        ];

        foreach ($pages as $path => $needle) {
            $response = $this->get($path);
            $response->assertOk();
            $response->assertSee($needle, false);
        }
    }

    public function test_pricing_page_marks_unresolved_inclusions_as_pending(): void
    {
        $this->get('/pricing')
            ->assertOk()
            ->assertSee('PKR 75,000')
            ->assertSee('Confirmed in writing after your Free HR Audit')
            ->assertDontSee('most popular', false);
    }

    public function test_student_assets_load_only_on_the_hub(): void
    {
        $hub = $this->get('/student-hub')->assertOk();
        $hub->assertSee('css/student.css', false);
        $hub->assertSee('js/student.js', false);

        $home = $this->get('/')->assertOk();
        $home->assertDontSee('css/student.css', false);
        $home->assertDontSee('js/student.js', false);
    }

    public function test_resume_builder_offers_draft_recovery_controls(): void
    {
        $this->get('/student-hub')
            ->assertOk()
            ->assertSee('Load example')
            ->assertSee('Clear draft')
            ->assertSee('The draft saves in this browser only.');
    }

    public function test_prompt_library_shows_counts_and_bulk_actions(): void
    {
        $html = (string) $this->get('/student-hub')->assertOk()->getContent();

        $this->assertStringContainsString('id="prompt-copy-visible"', $html);
        $this->assertStringContainsString('Copy visible prompts', $html);
        $this->assertStringContainsString('id="prompt-clear"', $html);
        $this->assertStringContainsString('class="search-clear"', $html);
        $this->assertMatchesRegularExpression('/data-prompt-filter="all">All <span class="filter-count">14<\/span>/', $html);
        $this->assertMatchesRegularExpression('/data-prompt-filter="Job search">Job search <span class="filter-count">3<\/span>/', $html);
    }

    public function test_print_stylesheet_is_non_blocking_and_scripts_defer(): void
    {
        $hub = (string) $this->get('/student-hub')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/css\/print\.css\?v=\d+" media="print"/', $hub);
        $this->assertMatchesRegularExpression('/<script src="\/js\/student\.js\?v=\d+" defer><\/script>/', $hub);

        $home = (string) $this->get('/')->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/<script src="\/js\/app\.js\?v=\d+" defer><\/script>/', $home);
        $this->assertMatchesRegularExpression('/<script src="\/js\/chat\.js\?v=\d+" defer><\/script>/', $home);
        $this->assertStringNotContainsString('media="print"', $home);
    }

    public function test_pages_render_without_php_diagnostics(): void
    {
        $pages = ['/', '/what-we-are', '/what-we-do', '/pricing', '/student-hub', '/faq', '/free-hr-audit'];
        $markers = ['Warning:', 'Notice:', 'Deprecated:', 'Fatal error', 'Undefined variable', 'Undefined array key'];

        foreach ($pages as $path) {
            $html = (string) $this->get($path)->assertOk()->getContent();
            foreach ($markers as $marker) {
                $this->assertStringNotContainsString($marker, $html, $path.' rendered '.$marker);
            }
        }
    }

    public function test_favicon_is_a_real_icon_not_an_empty_placeholder(): void
    {
        $path = public_path('favicon.ico');

        $this->assertFileExists($path);
        $this->assertGreaterThan(0, (int) filesize($path));
        $this->assertSame("\x00\x00\x01\x00", (string) file_get_contents($path, false, null, 0, 4));
    }

    public function test_seo_metadata_and_structured_data_render(): void
    {
        $home = $this->get('/')->assertOk();
        $home->assertSee('<link rel="canonical" href="http', false);
        $home->assertSee('property="og:title"', false);
        $home->assertSee('name="twitter:card"', false);
        $home->assertSee('application/ld+json', false);
        $home->assertSee('ProfessionalService', false);
        $home->assertSee('PKR', false);

        $this->get('/faq')->assertOk()->assertSee('FAQPage', false);
    }

    public function test_each_route_uses_its_configured_seo_snippet(): void
    {
        $routes = [
            '/' => 'home',
            '/what-we-are' => 'what-we-are',
            '/what-we-do' => 'what-we-do',
            '/pricing' => 'pricing',
            '/student-hub' => 'student-hub',
            '/faq' => 'faq',
            '/free-hr-audit' => 'free-hr-audit',
        ];

        $titles = [];
        $descriptions = [];

        foreach ($routes as $path => $key) {
            $seo = config('emplowise.seo.'.$key);
            $html = (string) $this->get($path)->assertOk()->getContent();

            $this->assertStringContainsString('<title>'.e($seo['title']).'</title>', $html, $path);
            $this->assertStringContainsString('name="description" content="'.e($seo['description']).'"', $html, $path);
            $this->assertStringContainsString('property="og:title" content="'.e($seo['title']).'"', $html, $path);
            $this->assertStringContainsString('property="og:description" content="'.e($seo['description']).'"', $html, $path);
            $this->assertStringContainsString('property="og:url" content="'.url($path).'"', $html, $path);
            $this->assertStringContainsString('name="twitter:title" content="'.e($seo['title']).'"', $html, $path);
            $this->assertStringContainsString('rel="canonical" href="'.url($path).'"', $html, $path);
            $this->assertStringContainsString('<h1', $html, $path);

            $titles[] = $seo['title'];
            $descriptions[] = $seo['description'];
        }

        $this->assertCount(count($routes), array_unique($titles));
        $this->assertCount(count($routes), array_unique($descriptions));
    }

    public function test_seo_audit_command_passes_for_the_shipped_config(): void
    {
        $this->artisan('emplowise:seo-audit')->assertSuccessful();
    }

    public function test_sitemap_lists_every_public_page(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');

        $xml = simplexml_load_string((string) $response->getContent());
        $this->assertNotFalse($xml);

        $locations = array_map(fn ($url): string => (string) $url->loc, iterator_to_array($xml->url, false));
        $this->assertCount(7, $locations);

        foreach (['/', '/what-we-are', '/what-we-do', '/pricing', '/student-hub', '/faq', '/free-hr-audit'] as $path) {
            $this->assertTrue(
                collect($locations)->contains(fn (string $url): bool => (parse_url($url, PHP_URL_PATH) ?: '/') === $path),
                'Missing sitemap entry for '.$path
            );
        }
    }

    public function test_home_headings_follow_a_strict_order(): void
    {
        $html = (string) $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertSame(0, substr_count($html, '<h4'));
    }

    public function test_faq_page_lists_all_verified_answers(): void
    {
        $total = collect(config('emplowise.faqs'))->sum(fn (array $group): int => count($group['items']));

        $this->get('/faq')
            ->assertOk()
            ->assertSee($total.' questions')
            ->assertSee('Yes. All five tools are free, with no account required.');
    }

    public function test_audit_submission_is_validated_and_stored(): void
    {
        $this->postJson('/api/audit', [])->assertStatus(422);

        $payload = [
            'contact_name' => 'Test Founder',
            'company_name' => 'Acme Labs',
            'email' => 'Founder@AcmeLabs.pk',
            'phone' => '+92 300 1234567',
            'headcount_band' => '11-20',
            'pain_points' => ['payroll', 'policy'],
            'notes' => 'Payroll runs on a spreadsheet.',
            'preferred_date' => now()->addDays(5)->toDateString(),
            'preferred_slot' => 'Morning (9:00 to 12:00)',
            'consent' => '1',
        ];

        $response = $this->postJson('/api/audit', $payload)->assertStatus(201);

        $reference = $response->json('reference');
        $this->assertStringStartsWith('EMP-', $reference);

        $this->assertDatabaseHas('audit_requests', [
            'reference' => $reference,
            'email' => 'founder@acmelabs.pk',
            'headcount_band' => '11-20',
            'status' => 'new',
        ]);

        $audit = AuditRequest::query()->where('reference', $reference)->firstOrFail();
        $this->assertSame(['payroll', 'policy'], $audit->pain_points);
    }

    public function test_audit_honeypot_is_silently_accepted_without_storing(): void
    {
        $this->postJson('/api/audit', ['website' => 'spam.example'])->assertStatus(202);

        $this->assertDatabaseCount('audit_requests', 0);
    }

    public function test_chat_returns_a_grounded_fallback_reply_and_logs_the_turn(): void
    {
        config(['ai.alibaba.api_key' => null]);

        $response = $this->postJson('/api/chat', [
            'message' => 'What do the packages cost?',
            'history' => [],
        ]);

        $response->assertOk()
            ->assertJsonStructure(['session_id', 'reply', 'intent', 'model', 'source', 'suggestions'])
            ->assertJsonPath('intent', 'pricing')
            ->assertJsonPath('source', 'fallback');

        $this->assertCount(3, $response->json('suggestions'));
        $this->assertStringContainsString('PKR 40,000', (string) $response->json('reply'));
        $this->assertDatabaseCount('chat_messages', 2);
        $this->assertSame(2, ChatMessage::query()->count());
    }

    public function test_chat_scores_mixed_intents_and_keeps_follow_up_context(): void
    {
        config(['ai.alibaba.api_key' => null]);

        $this->postJson('/api/chat', ['message' => 'How much is the free audit?'])
            ->assertOk()
            ->assertJsonPath('intent', 'audit');

        $this->postJson('/api/chat', [
            'message' => 'and for 20 people?',
            'previous_intent' => 'pricing',
        ])->assertOk()
            ->assertJsonPath('intent', 'pricing')
            ->assertJsonStructure(['suggestions' => [['label', 'query']]]);

        $this->postJson('/api/chat', ['message' => 'Hi'])
            ->assertOk()
            ->assertJsonPath('intent', 'greeting');
    }

    public function test_browser_chat_knowledge_base_matches_the_server_facts(): void
    {
        $js = (string) file_get_contents(public_path('js/chat.js'));

        foreach (['PKR 40,000', 'PKR 75,000', '14 prompts', '/free-hr-audit', '/student-hub'] as $fact) {
            $this->assertStringContainsString($fact, $js, 'Browser KB missing: '.$fact);
        }
    }

    public function test_interview_questions_come_from_the_local_bank_without_an_api_key(): void
    {
        config(['ai.alibaba.api_key' => null]);

        $this->postJson('/api/student/interview/questions', [
            'role' => 'Data and Analytics',
            'level' => 'Entry level',
        ])->assertOk()
            ->assertJsonCount(3, 'questions')
            ->assertJsonPath('source', 'local');
    }

    public function test_interview_feedback_is_deterministic_and_bounded(): void
    {
        config(['ai.alibaba.api_key' => null]);

        $payload = [
            'role' => 'Data and Analytics',
            'level' => 'Entry level',
            'question' => 'Describe a dataset you cleaned. What was messy?',
            'answer' => 'At my university lab we had a survey dataset with duplicate rows and missing income values. I removed duplicates, standardised city names, and filled missing values with the median. As a result the analysis error dropped and the team could trust the report.',
        ];

        $first = $this->postJson('/api/student/interview/feedback', $payload)->assertOk();
        $second = $this->postJson('/api/student/interview/feedback', $payload)->assertOk();

        $this->assertSame($first->json('score'), $second->json('score'));
        $this->assertGreaterThanOrEqual(0, $first->json('score'));
        $this->assertLessThanOrEqual(100, $first->json('score'));
        $first->assertJsonStructure(['score', 'verdict', 'strengths', 'fixes', 'model_answer', 'source']);
    }

    public function test_interview_feedback_explains_its_score_and_offers_a_probe(): void
    {
        config(['ai.alibaba.api_key' => null]);

        $response = $this->postJson('/api/student/interview/feedback', [
            'role' => 'Data and Analytics',
            'level' => 'Entry level',
            'question' => 'Describe a dataset you cleaned. What was messy?',
            'answer' => 'At my university lab we had a survey dataset with duplicate rows and missing income values. I removed duplicates, standardised city names, and filled missing values with the median. As a result the analysis error dropped and the team could trust the report.',
        ])->assertOk();

        $breakdown = $response->json('breakdown');
        $this->assertCount(4, $breakdown);
        $this->assertSame([35, 25, 25, 15], array_column($breakdown, 'max'));
        $this->assertSame($response->json('score'), array_sum(array_column($breakdown, 'score')));
        $this->assertNotEmpty($response->json('next_probe'));

        $this->postJson('/api/student/interview/feedback', [
            'role' => 'Data and Analytics',
            'level' => 'Entry level',
            'question' => 'Describe a dataset you cleaned. What was messy?',
            'answer' => 'I cleaned data.',
        ])->assertOk()
            ->assertJsonCount(4, 'breakdown')
            ->assertJsonPath('score', 18)
            ->assertJsonStructure(['next_probe']);
    }

    public function test_ats_score_is_explainable_and_bounded(): void
    {
        config(['ai.alibaba.api_key' => null]);

        $resume = <<<'RESUME'
Ayesha Khan
Karachi, Pakistan | ayesha@example.com | +92 300 1234567 | linkedin.com/in/ayesha

Summary
Data analyst with experience in SQL, Excel, and Python dashboards.

Experience
Data Analyst Intern — Northline
- Built SQL reports for 3 teams and reduced manual reporting time by 6 hours a week.
- Cleaned a 40,000 row dataset and documented the data dictionary.
- Presented weekly findings to two product managers.

Education
BS Computer Science — IBA Karachi, 2022 to 2026

Skills
SQL, Python, Excel, Power BI, stakeholder communication, data cleaning
RESUME;

        $jd = 'We are hiring a data analyst. Requirements include strong SQL, Python, Excel, Power BI, dashboarding, data cleaning, stakeholder communication, and reporting. You will build dashboards, analyse product metrics, and present insights to business teams.';

        $response = $this->postJson('/api/student/ats', [
            'resume' => $resume,
            'job_description' => $jd,
        ])->assertOk();

        $response->assertJsonStructure([
            'score', 'verdict', 'breakdown', 'matched', 'missing', 'structure', 'format', 'fixes', 'source',
        ]);

        $score = $response->json('score');
        $this->assertGreaterThanOrEqual(3, $score);
        $this->assertLessThanOrEqual(98, $score);
        $this->assertCount(3, $response->json('breakdown'));
        $this->assertSame(55 + 25 + 20, collect($response->json('breakdown'))->sum('max'));
        $this->assertSame('local', $response->json('source'));
    }

    public function test_ats_matches_synonyms_and_checks_the_target_title(): void
    {
        config(['ai.alibaba.api_key' => null]);

        $resume = "Senior Data Analyst\nKarachi | analyst@example.com | +92 300 1234567\n\nSummary\nAnalyst who communicates clearly and builds dashboards with JS.\n\nExperience\nSenior Data Analyst — Northline\n- Built dashboards in JS for three teams.\n- Communicated findings to two managers every week.\n\nEducation\nBS Computer Science\n\nSkills\nJS, SQL, communication";

        $jd = "Senior Data Analyst\nWe need strong communication and JavaScript skills for reporting work.";

        $response = $this->postJson('/api/student/ats', [
            'resume' => $resume,
            'job_description' => $jd,
        ])->assertOk();

        $this->assertSame('Senior Data Analyst', $response->json('title'));
        $this->assertTrue($response->json('title_match'));
        $this->assertContains('communication', $response->json('matched'));
        $this->assertContains('javascript', $response->json('matched'));

        $titleCheck = collect($response->json('structure'))->firstWhere('label', 'Target title present');
        $this->assertTrue($titleCheck['ok']);
        $this->assertSame(0, $titleCheck['weight']);
    }

    public function test_profile_optimizer_rejects_empty_input_and_rewrites_weak_copy(): void
    {
        config(['ai.alibaba.api_key' => null]);

        $this->postJson('/api/student/profile', [])->assertStatus(422);

        $response = $this->postJson('/api/student/profile', [
            'headline' => 'Final year CS student passionate about data',
            'summary' => 'I am a hardworking student looking for internship opportunities in data analytics.',
            'bullets' => 'Worked on a dashboard project'."\n".'Responsible for cleaning survey data',
        ])->assertOk();

        $response->assertJsonCount(3, 'headlines');
        $this->assertStringNotContainsString('passionate', strtolower((string) $response->json('headlines.0')));
        $this->assertStringContainsString('[add one number]', implode(' ', $response->json('bullets')));
        $response->assertJsonPath('source', 'local');

        $aimed = $this->postJson('/api/student/profile', [
            'target_role' => 'Cybersecurity Analyst',
            'headline' => 'Final year CS student passionate about data',
            'summary' => 'I am a hardworking student looking for internship opportunities.',
        ])->assertOk();

        $this->assertStringStartsWith('Cybersecurity Analyst |', (string) $aimed->json('headlines.0'));
    }
}
