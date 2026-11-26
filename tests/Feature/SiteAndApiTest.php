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
            '/terms' => 'Terms of Service',
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

    public function test_emplostart_is_a_launcher_that_opens_the_standalone_app(): void
    {
        $html = (string) $this->get('/emplostart')->assertOk()->getContent();

        $url = (string) config('emplowise.emplostart_url');
        $this->assertStringContainsString('href="'.e($url).'"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener"', $html);
        $this->assertStringContainsString('Open EmploStart', $html);

        // The launcher embeds no tool UI and no engine scripts.
        $this->assertStringNotContainsString('emplostart-config', $html);
        $this->assertStringNotContainsString('data-hub-panel', $html);
        $this->assertStringNotContainsString('js/student.js', $html);
        $this->assertStringNotContainsString('js/toolkit/', $html);
        $this->assertStringNotContainsString('css/hub.css', $html);

        foreach (config('emplowise.emplostart_modules') as $module) {
            $this->assertStringContainsString(e($module['title']), $html);
            $this->assertStringContainsString(e($module['desc']), $html);
        }
    }

    public function test_emplostart_launcher_maps_the_14_prompt_letters_and_the_three_intake_lanes(): void
    {
        $html = (string) $this->get('/emplostart')->assertOk()->getContent();
        $url = (string) config('emplowise.emplostart_url');

        // Every letter of EMPLOWISE x START is on the page with its prompt id.
        $letters = [
            'P01' => 'E', 'P02' => 'M', 'P03' => 'P', 'P04' => 'L', 'P05' => 'O',
            'P06' => 'W', 'P07' => 'I', 'P08' => 'S', 'P09' => 'E', 'P10' => 'S',
            'P11' => 'T', 'P12' => 'A', 'P13' => 'R', 'P14' => 'T',
        ];

        $this->assertSame(14, substr_count($html, 'class="launcher-prompt"'));

        foreach ($letters as $id => $letter) {
            $this->assertMatchesRegularExpression(
                '#<span class="launcher-prompt-id">'.$id.'</span>\s*<span class="launcher-prompt-letter"><span class="sr-only">Letter</span>'.$letter.'</span>#',
                $html,
                $id.' should render next to the letter '.$letter
            );
        }

        // The two tracks are headings, not prose, and each one links out.
        foreach (['EMPLOWISE', 'START'] as $track) {
            $this->assertStringContainsString('<h3>'.$track.'</h3>', $html);
        }

        $this->assertSame(
            2,
            substr_count($html, 'href="'.e($url).'#library" target="_blank" rel="noopener"'),
            'Each track should deep link to the app library in a new tab'
        );

        foreach (config('emplowise.copy.emplostart.intake_lanes') as $lane) {
            $this->assertStringContainsString('<h3>'.e($lane['title']).'</h3>', $html);
        }

        // The launcher describes the intake but loads none of the app engine.
        foreach (['prompt-library', 'candidate-vault', 'intake-engine', 'js/toolkit'] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $html);
        }
    }

    public function test_stylesheets_ship_as_one_bundle(): void
    {
        $hub = (string) $this->get('/emplostart')->assertOk()->getContent();
        $home = (string) $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('/<link rel="stylesheet"/', $hub), 'The launcher should load one bundle');
        $this->assertSame(1, preg_match_all('/<link rel="stylesheet"/', $home), 'Other pages should load one bundle');

        foreach ([$hub, $home] as $html) {
            foreach (['hub', 'tokens', 'components', 'print', 'student'] as $source) {
                $this->assertStringNotContainsString('css/'.$source.'.css', $html);
            }
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
            '/docs' => 'docs',
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

    public function test_showcase_pages_render_with_downloads_and_no_internal_markers(): void
    {
        $pages = [
            '/company-profile' => [
                'key' => 'company-profile',
                'download' => 'Emplowise-Company-Profile-2026.docx',
                'entry' => 'word/document.xml',
            ],
            '/portfolio' => [
                'key' => 'portfolio',
                'download' => 'Emplowise-Portfolio-2026.docx',
                'entry' => 'word/document.xml',
            ],
            '/pitch-deck' => [
                'key' => 'pitch-deck',
                'download' => 'Emplowise-Pitch-Deck-2026.pptx',
                'entry' => 'ppt/presentation.xml',
            ],
        ];

        $titles = [];

        foreach ($pages as $path => $page) {
            $html = (string) $this->get($path)->assertOk()->getContent();
            $seo = config('emplowise.seo.'.$page['key']);
            $showcase = config('emplowise.showcase_pages.'.$page['key']);

            $this->assertSame(1, substr_count($html, '<h1'), $path.' should carry exactly one h1');
            $this->assertStringContainsString('<title>'.e($seo['title']).'</title>', $html, $path);
            $this->assertStringContainsString('name="robots" content="'.e($showcase['robots']).'"', $html, $path);
            $this->assertStringNotContainsString('[To Verify', $html, $path.' leaks an internal marker');
            $this->assertMatchesRegularExpression(
                '#href="/downloads/'.preg_quote($page['download'], '#').'\?v=\d+"[^>]*download#',
                $html,
                $path.' should link its download with the download attribute',
            );

            $file = public_path('downloads/'.$page['download']);
            $this->assertFileExists($file);

            $zip = new \ZipArchive();
            $this->assertTrue($zip->open($file) === true, $page['download'].' is not a readable Office package');
            $this->assertNotFalse($zip->locateName($page['entry']), $page['download'].' is missing '.$page['entry']);
            $zip->close();

            $titles[] = $seo['title'];
        }

        $this->assertCount(count($pages), array_unique($titles), 'Showcase titles must stay unique');

        // The footer links all three pages under the shared Company and Resources groups.
        $footer = (string) $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('href="'.route('company-profile', [], false).'"', $footer);
        $this->assertStringContainsString('href="'.route('portfolio', [], false).'"', $footer);
        $this->assertStringContainsString('href="'.route('pitch-deck', [], false).'"', $footer);
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
        $this->assertCount(14, $locations);

        foreach ([
            '/', '/what-we-are', '/what-we-do', '/why-emplowise', '/emplostart', '/faq',
            '/contact', '/what-we-are-building', '/free-hr-audit', '/terms', '/privacy',
            '/company-profile', '/portfolio', '/docs',
        ] as $path) {
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
        // Email is the only verified direct channel in this build; a phone
        // number is confirmed in writing after the audit, so no tel: link.
        $this->assertGreaterThanOrEqual(3, substr_count($html, 'href="mailto:'));
        $this->assertStringNotContainsString('href="tel:', $html);
        // The page closes on the shared footer: copyright plus the brand line.
        $this->assertStringContainsString((string) date('Y'), $html);
        $this->assertStringContainsString(config('emplowise.copy.layout.footer_tagline'), $html);

        foreach ($copy['resources'] as $resource) {
            $this->assertStringContainsString('href="'.route($resource['route'], [], false).'"', $html);
            $this->assertStringContainsString(e($resource['label']), $html);
        }

        // Placeholder social profiles are gone until a real address exists (I-25).
        $this->assertStringNotContainsString('data-social-pending', $html);
        $this->assertStringNotContainsString('wa.me/923000000000', $html);
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

        // No simulated browser chrome or stacked-card props: the showcase is
        // one honest card with tabs.
        $this->assertStringNotContainsString('team-window-bar', $html);
        $this->assertStringNotContainsString('team-dots', $html);
        $this->assertStringNotContainsString('team.emplowise', $html);

        $css = (string) file_get_contents(public_path('css/site.css'));
        $this->assertStringContainsString('aspect-ratio: 4 / 5', $css);
    }

    public function test_audit_wizard_exposes_progress_and_autocomplete_hooks(): void
    {
        $html = (string) $this->get('/free-hr-audit')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'id="audit-form"'));
        $this->assertSame(4, preg_match_all('/class="wizard-step(?: is-active)?"/', $html));
        $this->assertStringContainsString('id="wizard-label" aria-live="polite"', $html);
        $this->assertStringContainsString('id="wizard-next"', $html);
        $this->assertStringContainsString('id="wizard-submit"', $html);

        // Autofill hooks stay on the four contact fields, and the wizard ships
        // with novalidate so the inline errors are the single source of truth.
        $this->assertMatchesRegularExpression('/id="audit-form"[^>]*novalidate/', $html);
        foreach (['name', 'organization', 'email', 'tel'] as $token) {
            $this->assertStringContainsString('autocomplete="'.$token.'"', $html);
        }
        $this->assertStringContainsString('name="website"', $html);
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

    public function test_faq_accordion_is_wired_for_assistive_tech(): void
    {
        $total = collect(config('emplowise.faqs'))->sum(fn (array $group): int => count($group['items']));
        $html = (string) $this->get('/faq')->assertOk()->getContent();

        // Every trigger owns a labelled region that starts collapsed.
        $this->assertSame($total, substr_count($html, 'class="acc-trigger"'));
        $this->assertSame($total, substr_count($html, 'role="region"'));
        $this->assertSame($total, preg_match_all('/class="acc-trigger"[^>]*aria-expanded="false"/s', $html));
        $this->assertSame(0, preg_match('/<div class="acc-panel"[^>]*role="region"[^>]*aria-labelledby="[^"]+"(?![^>]*\bhidden\b)/', $html));
        $this->assertSame($total, preg_match_all('/<div class="acc-panel"[^>]*\bhidden\b/', $html));

        foreach (preg_match_all('/id="(faq-panel-\d+-\d+)"/', $html, $panels) ? $panels[1] : [] as $panel) {
            $this->assertStringContainsString('aria-controls="'.$panel.'"', $html);
            $this->assertStringContainsString('id="'.$panel.'-trigger"', $html);
        }
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

        foreach (['PKR 40,000', 'PKR 75,000', '14 ready prompts', '/free-hr-audit', '/emplostart'] as $fact) {
            $this->assertStringContainsString($fact, $js, 'Browser KB missing: '.$fact);
        }

        // The browser answers from its own copy of the fallback text, so every
        // intent must stay byte-identical to the PHP service. A copy edit on
        // one side alone used to ship silently.
        $fallback = new \ReflectionMethod(EmplowiseAssistant::class, 'fallbackReply');
        $assistant = app(EmplowiseAssistant::class);

        foreach ([
            'pricing', 'audit', 'emplostart', 'tools', 'payroll', 'performance', 'policy',
            'services', 'pending', 'contact', 'careers', 'why', 'who', 'where', 'when',
            'greeting', 'thanks', 'general',
        ] as $intent) {
            $text = $fallback->invoke($assistant, $intent);
            $literal = "'".str_replace(['\\', "'", "\n"], ['\\\\', "\\'", '\\n'], $text)."'";

            $this->assertStringContainsString($literal, $js, 'Browser KB drifted from the server copy: '.$intent);
        }
    }
}
