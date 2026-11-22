<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuditRequest;
use App\Models\ChatMessage;
use App\Services\EmplowiseAssistant;
use App\Support\CssBundles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SiteAndApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_pages_render(): void
    {
        $pages = [
            '/' => 'Remote HR services in Pakistan',
            '/what-we-are' => 'HR outsourcing company in Pakistan',
            '/what-we-do' => 'HR operations outsourcing',
            '/why-emplowise' => 'PKR 40,000',
            '/emplostart' => 'Free ATS resume checker',
            '/faq' => 'HR outsourcing questions',
            '/contact' => 'Contact Emplowise',
            '/what-we-are-building' => 'AI automation for HR',
            '/free-hr-audit' => 'free HR audit in Pakistan',
            '/terms' => 'Terms of service',
            '/privacy' => 'Privacy Policy',
        ];

        foreach ($pages as $path => $needle) {
            $response = $this->get($path);
            $response->assertOk();
            $response->assertSee($needle, false);
        }
    }

    public function test_legacy_routes_redirect_to_the_new_information_architecture(): void
    {
        foreach ([
            '/pricing' => '/why-emplowise',
            '/career-hub' => '/emplostart',
            '/student-hub' => '/emplostart',
            '/ai-automation' => '/what-we-are-building',
        ] as $from => $to) {
            $this->get($from)->assertStatus(301)->assertRedirect($to);
        }

        $this->get('/team')->assertStatus(301)->assertRedirect('/what-we-are#team');
    }

    public function test_why_page_marks_unresolved_inclusions_as_pending(): void
    {
        $this->get('/why-emplowise')
            ->assertOk()
            ->assertSee('PKR 75,000')
            ->assertSee('Confirmed in writing after your Free HR Audit')
            ->assertDontSee('most popular', false);
    }

    public function test_career_assets_load_only_on_the_hub(): void
    {
        $hub = $this->get('/emplostart')->assertOk();
        $hub->assertSee('css/site.css', false);
        $hub->assertSee('css/hub.css', false);
        $hub->assertSee('js/student.js', false);

        $home = $this->get('/')->assertOk();
        $home->assertDontSee('css/hub.css', false);
        $home->assertDontSee('js/student.js', false);
    }

    public function test_stylesheets_ship_as_two_bundles(): void
    {
        $hub = (string) $this->get('/emplostart')->assertOk()->getContent();
        $home = (string) $this->get('/')->assertOk()->getContent();

        $this->assertSame(2, preg_match_all('/<link rel="stylesheet"/', $hub), 'The hub should load two bundles');
        $this->assertSame(1, preg_match_all('/<link rel="stylesheet"/', $home), 'Other pages should load one bundle');

        foreach ([$hub, $home] as $html) {
            $this->assertStringNotContainsString('css/tokens.css', $html);
            $this->assertStringNotContainsString('css/components.css', $html);
            $this->assertStringNotContainsString('css/print.css', $html);
            $this->assertStringNotContainsString('css/student.css', $html);
        }
    }

    public function test_css_bundles_are_regenerated_from_their_sources(): void
    {
        foreach (CssBundles::build() as $name => $expected) {
            $path = public_path('css/'.$name);

            $this->assertFileExists($path);
            $this->assertSame(
                $expected,
                (string) file_get_contents($path),
                $name.' is stale; run php artisan emplowise:bundle-css'
            );
        }
    }

    public function test_primary_nav_stays_compact_and_secondary_pages_move_to_the_footer(): void
    {
        $html = (string) $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match('/<nav class="main-nav".*?<\/nav>/s', $html, $nav));
        $this->assertLessThanOrEqual(7, substr_count($nav[0], '<a '), 'Header nav must fit on one laptop line');
        $this->assertStringNotContainsString('FAQ', $nav[0]);
        $this->assertStringContainsString('Meet Emplowise', $nav[0]);
        $this->assertStringContainsString('Our Story', $nav[0]);
        $this->assertStringContainsString('Meet The Team', $nav[0]);
        $this->assertStringContainsString("Where&#039;s AI", $nav[0]);
        $this->assertStringContainsString('EmploStart', $nav[0]);
        $this->assertStringContainsString('what-we-are#team', $nav[0]);

        $this->assertSame(1, preg_match('/<footer class="site-footer">.*?<\/footer>/s', $html, $footer));
        $this->assertStringContainsString('FAQ', $footer[0]);
        $this->assertStringContainsString('EmploStart', $footer[0]);
        $this->assertStringContainsString('Free HR Audit', $footer[0]);
    }

    public function test_resume_builder_offers_draft_recovery_controls(): void
    {
        $this->get('/emplostart')
            ->assertOk()
            ->assertSee('Load example')
            ->assertSee('Clear draft')
            ->assertSee('The draft saves in this browser only.');
    }

    public function test_prompt_library_shows_counts_and_bulk_actions(): void
    {
        $html = (string) $this->get('/emplostart')->assertOk()->getContent();

        $this->assertStringContainsString('id="prompt-copy-visible"', $html);
        $this->assertStringContainsString('Copy visible prompts', $html);
        $this->assertStringContainsString('id="prompt-clear"', $html);
        $this->assertStringContainsString('class="search-clear"', $html);
        $this->assertMatchesRegularExpression('/data-prompt-filter="all">All <span class="filter-count">14<\/span>/', $html);
        $this->assertMatchesRegularExpression('/data-prompt-filter="Job search">Job search <span class="filter-count">3<\/span>/', $html);
    }

    public function test_print_rules_stay_scoped_and_scripts_defer(): void
    {
        $bundle = (string) file_get_contents(public_path('css/hub.css'));

        $this->assertStringContainsString('@media print', $bundle);
        $this->assertStringContainsString('.resume-paper', $bundle);

        $hub = (string) $this->get('/emplostart')->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/<script src="\/js\/student\.js\?v=\d+" defer><\/script>/', $hub);

        $home = (string) $this->get('/')->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/<script src="\/js\/app\.js\?v=\d+" defer><\/script>/', $home);
        $this->assertMatchesRegularExpression('/<script src="\/js\/chat\.js\?v=\d+" defer><\/script>/', $home);
    }

    public function test_the_hub_loads_every_browser_engine_before_student_js(): void
    {
        $html = (string) $this->get('/emplostart')->assertOk()->getContent();

        $student = strpos($html, '/js/student.js');
        $this->assertNotFalse($student);

        foreach (['interview', 'ats', 'profile'] as $engine) {
            $position = strpos($html, '/js/toolkit/'.$engine.'.js');
            $this->assertNotFalse($position, 'Missing toolkit script for '.$engine);
            $this->assertLessThan($student, $position, $engine.'.js must load before student.js');
        }
    }

    public function test_pages_render_without_php_diagnostics(): void
    {
        $pages = ['/', '/what-we-are', '/what-we-do', '/why-emplowise', '/emplostart', '/faq', '/contact', '/what-we-are-building', '/free-hr-audit', '/terms', '/privacy'];
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
            '/why-emplowise' => 'why-emplowise',
            '/emplostart' => 'emplostart',
            '/faq' => 'faq',
            '/contact' => 'contact',
            '/what-we-are-building' => 'what-we-are-building',
            '/free-hr-audit' => 'free-hr-audit',
            '/terms' => 'terms',
            '/privacy' => 'privacy',
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
        $this->assertCount(11, $locations);

        foreach (['/', '/what-we-are', '/what-we-do', '/why-emplowise', '/emplostart', '/faq', '/contact', '/what-we-are-building', '/free-hr-audit', '/terms', '/privacy'] as $path) {
            $this->assertTrue(
                collect($locations)->contains(fn (string $url): bool => (parse_url($url, PHP_URL_PATH) ?: '/') === $path),
                'Missing sitemap entry for '.$path
            );
        }
    }

    public function test_contact_hub_links_out_safely_and_keeps_the_email_copyable(): void
    {
        $copy = config('emplowise.copy.contact');
        $query = rawurlencode($copy['ai_query']);
        $html = (string) $this->get('/contact')->assertOk()->getContent();

        foreach ($copy['ai_links'] as $link) {
            $this->assertStringContainsString('href="'.$link['href'].$query.'"', $html);
            $this->assertStringContainsString('target="_blank" rel="noopener noreferrer"', $html);
        }

        $this->assertStringContainsString('mailto:'.$copy['email'], $html);
        $this->assertStringContainsString('id="copy-email"', $html);
        // The page closes on the shared footer: copyright plus the brand line.
        $this->assertStringContainsString((string) date('Y'), $html);
        $this->assertStringContainsString(config('emplowise.copy.layout.footer_tagline'), $html);

        foreach ($copy['resources'] as $resource) {
            $this->assertStringContainsString('href="'.route($resource['route'], [], false).'"', $html);
            $this->assertStringContainsString(e($resource['label']), $html);
        }

        foreach ($copy['socials'] as $social) {
            $this->assertStringContainsString('aria-label="'.$social['label'].'"', $html);
        }
    }

    public function test_what_we_are_page_includes_the_team_showcase(): void
    {
        $html = (string) $this->get('/what-we-are')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'role="tablist"'));
        $this->assertSame(3, substr_count($html, 'role="tab"'));
        $this->assertSame(3, substr_count($html, 'role="tabpanel"'));
        $this->assertStringContainsString('data-team-tab="ali"', $html);
        $this->assertStringContainsString('class="team-photo"', $html);
        $this->assertStringContainsString('<h2>'.e(config('emplowise.seo.team.h2')).'</h2>', $html);

        foreach (config('emplowise.copy.team.profiles') as $profile) {
            $this->assertStringNotContainsString($profile['portrait'], $html);
            $this->assertStringContainsString('aria-label="'.e($profile['name']).'"', $html);
            $this->assertStringContainsString($profile['name'], $html);
            $this->assertStringContainsString(e($profile['bio']), $html);
        }

        $this->assertSame(3, substr_count($html, 'class="team-avatar"'));

        $css = (string) file_get_contents(public_path('css/site.css'));
        $this->assertStringContainsString('aspect-ratio: 4 / 5', $css);
    }

    public function test_what_we_are_building_page_is_monochrome_and_prelaunch(): void
    {
        $copy = config('emplowise.copy.what-we-are-building');
        $html = (string) $this->get('/what-we-are-building')->assertOk()->getContent();

        $this->assertStringContainsString('class="page-ai"', $html);
        $this->assertStringContainsString($copy['banner'], $html);
        $this->assertStringContainsString('disabled aria-disabled="true"', $html);
        $this->assertStringContainsString($copy['cta'], $html);
        $this->assertStringContainsString(route('what-we-do', [], false), $html);
        $this->assertStringContainsString('<h2>'.e(config('emplowise.seo.what-we-are-building.h2')).'</h2>', $html);

        foreach ($copy['modules'] as $module) {
            $this->assertStringContainsString($module['name'], $html);
            $this->assertStringContainsString(e($module['body']), $html);
        }

        $css = (string) file_get_contents(public_path('css/site.css'));
        $this->assertStringContainsString('body.page-ai > *:not(script):not(.skip-link)', $css);
        $this->assertStringContainsString('filter: grayscale(100%)', $css);
    }

    public function test_home_is_a_compact_snapshot_without_the_duplicated_pillar_lists(): void
    {
        $html = (string) $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'id="hero-panel"'));
        $this->assertSame(0, substr_count($html, 'pillar-detail'));
        $this->assertSame(0, substr_count($html, 'pillar-list'));

        foreach (['payroll', 'performance', 'policy'] as $pillar) {
            $this->assertStringContainsString(route('what-we-do', [], false).'#'.$pillar, $html);
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

        foreach (['PKR 40,000', 'PKR 75,000', '14 prompts', '/free-hr-audit', '/emplostart'] as $fact) {
            $this->assertStringContainsString($fact, $js, 'Browser KB missing: '.$fact);
        }

        // The browser answers from its own copy of the fallback text, so every
        // intent must stay byte-identical to the PHP service. A copy edit on
        // one side alone used to ship silently.
        $fallback = new \ReflectionMethod(EmplowiseAssistant::class, 'fallbackReply');
        $assistant = app(EmplowiseAssistant::class);

        foreach ([
            'pricing', 'audit', 'student', 'tools', 'payroll', 'performance', 'policy',
            'services', 'pending', 'contact', 'careers', 'why', 'who', 'where', 'when',
            'greeting', 'thanks', 'general',
        ] as $intent) {
            $text = $fallback->invoke($assistant, $intent);
            $literal = "'".str_replace(['\\', "'", "\n"], ['\\\\', "\\'", '\\n'], $text)."'";

            $this->assertStringContainsString($literal, $js, 'Browser KB drifted from the server copy: '.$intent);
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

    public function test_student_tools_accept_arabic_text_without_corrupting_it(): void
    {
        config(['ai.alibaba.api_key' => null]);

        // U+0645 (م) contains the byte 0x85. A byte-mode /\R/ split used to cut
        // the character in half, which left the JSON response unencodable and
        // turned the whole endpoint into a 500.
        $resume = "محللة بيانات\nكراتشي | ayesha@example.com\n\nالملخص\nمحللة بيانات بخبرة ثلاث سنوات في SQL و Excel.\n\nالخبرة\nمحللة بيانات - نورثلاين\n- بنت لوحات معلومات لثلاثة فرق.\n\nالمهارات\nSQL, Excel, Power BI";
        $jobDescription = 'نبحث عن محللة بيانات. المتطلبات تشمل SQL و Excel و Power BI وتنظيف البيانات وإعداد التقارير.';

        $this->postJson('/api/student/ats', [
            'resume' => $resume,
            'job_description' => $jobDescription,
        ])->assertOk()->assertJsonStructure(['score', 'verdict', 'title', 'source']);

        $profile = $this->postJson('/api/student/profile', [
            'headline' => 'محلل بيانات بخبرة ثلاث سنوات',
            'summary' => 'أعمل على تنظيف البيانات وبناء لوحات المعلومات لفريق من خمسة أشخاص.',
            'bullets' => "• حللت ١٢ تقريراً شهرياً\n- حسّنت المبيعات ٢٥٪",
        ])->assertOk();

        // The bullet must survive whole; the old byte split dropped "حسّنت المبيعات ".
        $this->assertStringContainsString('حسّنت المبيعات ٢٥٪', implode(' ', $profile->json('bullets')));
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
