<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Emplowise Content Source of Truth
|--------------------------------------------------------------------------
|
| Every public sentence lives in this file. Edit only the text inside the
| quotes and leave the array keys alone. After editing, run:
|
|   php artisan emplowise:seo-audit
|   php artisan config:clear && php artisan config:cache
|
| Facts come from the Emplowise Brand Kit 2026 and the 2026 business brief.
| Where a detail still depends on the client's own records, the copy says it
| is confirmed in writing after the free HR audit. Nothing is invented.
|
*/

return [

    /*
    | === SEARCH SNIPPETS FOR EACH PAGE ===
    | Edit only the text inside quotes. Keep array keys unchanged.
    |
    | primary_keyword     the one phrase the page should rank for
    | secondary_keywords  two supporting phrases, all different per page
    | title               browser tab and search result headline (45 to 60 characters)
    | description         search result snippet (135 to 155 characters)
    | h1                  the main page heading
    | intro               the first paragraph, shown directly under the h1
    */
    'seo' => [

        // === PAGE: HOME ===
        'home' => [
            'primary_keyword' => 'remote HR services in Pakistan',
            'secondary_keywords' => ['HR outsourcing in Pakistan', 'managed HR services'],
            'title' => 'Remote HR Services in Pakistan, One Team | Emplowise',
            'description' => 'Remote HR services in Pakistan for startups and SMEs. Payroll, performance and policy on one monthly retainer from PKR 40,000. Book a free audit.',
            'h1' => 'Remote HR services in Pakistan, run by one accountable team.',
            'h1_accent' => 'one accountable team',
            'h2' => 'What our remote HR services in Pakistan cover.',
            'intro' => 'Emplowise provides remote HR services in Pakistan for startups and SMEs. It runs payroll inputs, performance cycles and policy on one monthly retainer priced by headcount, from PKR 40,000 for up to 20 staff. Every engagement starts with a free HR audit and a written gap list, delivered remotely by one accountable team.',
        ],

        // === PAGE: WHAT WE ARE ===
        'what-we-are' => [
            'primary_keyword' => 'HR outsourcing company in Pakistan',
            'secondary_keywords' => ['outsourced HR support', 'dedicated HR representative'],
            'title' => 'HR Outsourcing Company in Pakistan, Named Team | Emplowise',
            'description' => 'Emplowise is an HR outsourcing company in Pakistan. Three named people install payroll, performance and policy structure, then run it remotely.',
            'h1' => 'An HR outsourcing company in Pakistan that answers directly.',
            'h1_accent' => 'that answers directly',
            'h2' => 'How an HR outsourcing company in Pakistan delivers.',
            'intro' => 'Emplowise is an HR outsourcing company in Pakistan delivering remote HR to startups and SMEs. A named team installs payroll, performance and policy structure, then runs it, so founders stop acting as the HR helpdesk. Delivery is remote, and a dedicated HR representative gives your founder or CEO direct access.',
        ],

        // === PAGE: WHAT WE DO ===
        'what-we-do' => [
            'primary_keyword' => 'HR operations outsourcing',
            'secondary_keywords' => ['payroll and compliance services', 'HR process management'],
            'title' => 'HR Operations Outsourcing for Teams in Pakistan | Emplowise',
            'description' => 'HR operations outsourcing in Pakistan across payroll, performance and policy. Fourteen confirmed services, one four-stage method, remote delivery.',
            'h1' => 'HR operations outsourcing across three confirmed pillars.',
            'h1_accent' => 'three confirmed pillars',
            'h2' => 'Fourteen services inside HR operations outsourcing.',
            'intro' => 'Emplowise provides HR operations outsourcing across three pillars: payroll, performance management and policy making. Fourteen confirmed services sit inside them, each engagement follows a four-stage method (assess, identify, improve, review), and retainers start at PKR 40,000 a month, delivered remotely in Pakistan.',
        ],

        // === PAGE: WHY EMPLOWISE ===
        'why-emplowise' => [
            'primary_keyword' => 'why Emplowise',
            'secondary_keywords' => ['remote HR partner in Pakistan', 'HR outsourcing pricing in Pakistan'],
            'title' => 'Why Emplowise Publishes HR Pricing in Pakistan | Emplowise',
            'description' => 'Why Emplowise: remote HR for growing teams in Pakistan with pricing on the page. Starter PKR 40,000, Business PKR 75,000, Enterprise custom, audit first.',
            'h1' => 'Why Emplowise is the remote HR partner for growing teams.',
            'h1_accent' => 'the remote HR partner',
            'h2' => 'Why Emplowise publishes HR pricing by headcount.',
            'intro' => 'Why Emplowise publishes HR outsourcing pricing in Pakistan by headcount: Starter is PKR 40,000 a month for up to 20 staff, Business is PKR 75,000 for up to 50, and Enterprise is custom quoted above 50. Each is a monthly retainer, and which services are included is confirmed in writing after your free HR audit.',
        ],

        // === PAGE: EMPLOSTART ===
        'emplostart' => [
            'primary_keyword' => 'free ATS resume checker',
            'secondary_keywords' => ['career pivot planning', 'mock interview practice'],
            'title' => 'Free ATS Resume Checker and 6 Career Tools | Emplowise',
            'description' => 'EmploStart is a permanently free career app: a free ATS resume checker, interview prep, profile builder and three planning modules. Runs in your browser.',
            'h1' => 'A free ATS resume checker for career pivoters and job seekers.',
            'h1_accent' => 'free ATS resume checker',
            'h2' => 'Free ATS resume checker and five more free tools.',
            'intro' => 'EmploStart is a permanently free career app from Emplowise. It includes a free ATS resume checker, mock interview practice, a profile builder and three planning modules, six tools that run in your browser with no account and optional AI, funded by Emplowise remote HR services.',
        ],

        // === PAGE: FAQ ===
        'faq' => [
            'primary_keyword' => 'HR outsourcing questions',
            'secondary_keywords' => ['Emplowise pricing answers', 'HR scope questions'],
            'title' => 'HR Outsourcing Questions, Answered With Facts | Emplowise',
            'description' => 'Twenty-one answers to HR outsourcing questions in Pakistan: pricing from PKR 40,000, what a retainer includes, the free audit and the EmploStart tools.',
            'h1' => 'HR outsourcing questions answered with confirmed facts.',
            'h1_accent' => 'with confirmed facts',
            'h2' => 'HR outsourcing questions',
            'intro' => 'These HR outsourcing questions are answered with confirmed facts only: prices published in PKR, scope confirmed in writing after your free HR audit, and no claims about results the team has not yet verified. Where something is unconfirmed, the answer says so plainly.',
        ],

        // === PAGE: FREE HR AUDIT ===
        'free-hr-audit' => [
            'primary_keyword' => 'free HR audit in Pakistan',
            'secondary_keywords' => ['HR gap assessment', 'HR diagnostic review'],
            'title' => 'Free HR Audit in Pakistan, Written Gap List | Emplowise',
            'description' => 'Book a free HR audit in Pakistan. Four minutes to share headcount, payroll, performance and policy pain points, then a prioritised written gap list.',
            'h1' => 'A free HR audit in Pakistan, booked in four minutes.',
            'h1_accent' => 'booked in four minutes',
            'h2' => 'Your free HR audit in Pakistan: next steps.',
            'intro' => 'Emplowise offers a free HR audit in Pakistan for startups and SMEs. In about four minutes you share headcount, payroll, performance and policy pain points, and Emplowise returns a structured review and a prioritised written gap list, delivered remotely with no cost or obligation.',
        ],

        // === PAGE: CONTACT ===
        'contact' => [
            'primary_keyword' => 'contact Emplowise',
            'secondary_keywords' => ['Emplowise contact details', 'remote HR support contact'],
            'title' => 'Contact Emplowise About Remote HR in Pakistan | Emplowise',
            'description' => 'Contact Emplowise for remote HR in Pakistan. Email the team, book the free HR audit, or ask ChatGPT, Claude, Gemini or Perplexity about us.',
            'h1' => 'Contact Emplowise without a maze of forms.',
            'h1_accent' => 'without a maze of forms',
            'h2' => 'Contact Emplowise through the channel you prefer.',
            'intro' => 'Contact Emplowise for remote HR in Pakistan by email at hello@emplowise.com, by booking the free HR audit on the site, or by asking a connected AI assistant using the prefilled prompts for ChatGPT, Claude, Gemini or Perplexity. Direct phone and inbox details are confirmed in writing after your free HR audit.',
        ],

        // === PAGE: TEAM ===
        'team' => [
            'primary_keyword' => 'Emplowise team',
            'secondary_keywords' => ['remote HR leadership team', 'Emplowise founders and advisor'],
            'title' => 'Meet the Emplowise team behind remote HR | Emplowise',
            'description' => 'Meet the Emplowise team behind remote HR in Pakistan: an advisor, a content strategist, and a senior HR manager running people operations.',
            'h1' => 'The Emplowise team that runs your people operations.',
            'h1_accent' => 'Emplowise team',
            'h2' => 'How the Emplowise team splits the work.',
            'intro' => 'The Emplowise team runs remote HR for startups and SMEs in Pakistan. Three people own strategy, marketing, and people operations, and they answer directly.',
        ],

        // === PAGE: WHAT WE ARE BUILDING ===
        'what-we-are-building' => [
            'primary_keyword' => 'AI automation for HR',
            'secondary_keywords' => ['HR payroll automation', 'HR policy co-pilot'],
            'title' => 'AI Automation for HR in Pakistan, Pre-Launch | Emplowise',
            'description' => 'Emplowise is building AI automation for HR in Pakistan: a payroll engine, a performance insight layer and a policy co-pilot. All three are pre-launch.',
            'h1' => 'Where AI fits: AI automation for HR, in the open.',
            'h1_accent' => 'AI automation for HR',
            'h2' => 'Where AI automation for HR will plug in.',
            'intro' => 'Emplowise is building AI automation for HR: a payroll engine, a performance insight layer and a policy co-pilot. All three are pre-launch and shown as static previews with no live data, so the confirmed remote HR services remain the offer available today.',
        ],

        // === PAGE: TERMS ===
        'terms' => [
            'primary_keyword' => 'Emplowise terms of service',
            'secondary_keywords' => ['remote HR service terms', 'HR retainer agreement Pakistan'],
            'title' => 'Emplowise Terms of Service for HR Retainers | Emplowise',
            'description' => 'The terms covering the Emplowise website, the free HR audit and monthly remote HR retainers. Plain language, Pakistan law, scope confirmed in writing.',
            'h1' => 'Emplowise terms of service, written to be read.',
            'h1_accent' => 'written to be read',
            'h2' => 'Emplowise terms of service',
            'intro' => 'These Emplowise terms of service cover this website, the free HR audit and any monthly retainer with Emplowise HR Services. Where a signed scope or proposal says something different, that document wins.',
        ],

        // === PAGE: PRIVACY ===
        'privacy' => [
            'primary_keyword' => 'Emplowise privacy policy',
            'secondary_keywords' => ['HR audit data privacy', 'cookie policy Pakistan'],
            'title' => 'Emplowise Privacy Policy and Cookie Choices | Emplowise',
            'description' => 'How the Emplowise privacy policy works: what we collect, cookies and local storage, processors, retention, and how to access or delete your data.',
            'h1' => 'The Emplowise privacy policy, in plain language.',
            'h1_accent' => 'in plain language',
            'h2' => 'Emplowise privacy policy',
            'intro' => 'This Emplowise privacy policy explains what we collect when you use this site, book a free HR audit or email the team, and the choices you have. It covers cookies, local storage and the tools we use to run the site.',
        ],

        // === PAGE: COMPANY PROFILE (showcase) ===
        'company-profile' => [
            'primary_keyword' => 'remote HR company profile',
            'secondary_keywords' => ['Emplowise company document', 'HR service provider profile'],
            'title' => 'Remote HR Company Profile: The 2026 Edition | Emplowise',
            'description' => 'The Emplowise remote HR company profile in one document: who we serve, the four-stage method, fourteen services, published retainers and the named team.',
            'h1' => 'A remote HR company profile written for buyers, not archives.',
            'h1_accent' => 'written for buyers',
            'h2' => 'What this remote HR company profile includes.',
            'intro' => 'The Emplowise remote HR company profile is one 2026 document covering who we serve (startups, SMEs and emerging talent), the four-stage method, published monthly retainers of PKR 40,000, PKR 75,000 and a custom Enterprise quote, and the named team owning each part of remote HR delivery.',
        ],

        // === PAGE: PORTFOLIO (showcase) ===
        'portfolio' => [
            'primary_keyword' => 'remote HR portfolio',
            'secondary_keywords' => ['shipped HR platform work', 'Emplowise work evidence'],
            'title' => 'Remote HR Portfolio of Shipped Work, Repo Proof | Emplowise',
            'description' => 'A remote HR portfolio listing only shipped work: the platform, the career toolkit, the audit wizard and the brand system, each with repo proof.',
            'h1' => 'A remote HR portfolio that lists shipped work only.',
            'h1_accent' => 'shipped work only',
            'h2' => 'What this remote HR portfolio documents.',
            'intro' => 'This remote HR portfolio lists work that exists in a repository, not work that exists in a slide. It documents four shipped artifacts, the supporting systems behind them, and the standard a future client case study will have to meet before it is published here. Emplowise has no published client case studies yet, and this page does not invent any.',
        ],

        // === PAGE: DOCS HUB ===
        'docs' => [
            'primary_keyword' => 'Emplowise documentation',
            'secondary_keywords' => ['brand kit downloads', 'company profile document'],
            'title' => 'Emplowise Documentation, Downloads and Editions | Emplowise',
            'description' => 'Every Emplowise document in one place: brand kit, company profile, portfolio, pitch deck and engineering notes, with live editions and editable downloads.',
            'h1' => 'Emplowise documentation, with live editions and editable downloads.',
            'h1_accent' => 'Emplowise documentation',
            'h2' => 'Emplowise documentation index',
            'intro' => 'Emplowise documentation lives here in one place, every public document with its current edition marked, so a client, candidate or contractor works from the same file the team uses. Downloads are editable rather than flattened, and each carries a version stamp.',
        ],

        // === PAGE: PITCH DECK (showcase) ===
        'pitch-deck' => [
            'primary_keyword' => 'Emplowise pitch deck',
            'secondary_keywords' => ['remote HR slide deck', 'HR service pitch slides'],
            'title' => 'Emplowise Pitch Deck: Eight Slides, One Question | Emplowise',
            'description' => 'The Emplowise pitch deck in eight slides: product, problem, method, pricing, market wedge, go-to-market, shipped proof and roadmap. Shareable by link.',
            'h1' => 'The Emplowise pitch deck, eight slides, one question each.',
            'h1_accent' => 'eight slides',
            'h2' => 'What the Emplowise pitch deck answers.',
            'intro' => 'This is the Emplowise pitch deck as a slide-by-slide page. Each slide answers one buyer question, in the order buyers ask it, with unresolved numbers kept in the speaker notes. The deck is shareable by direct link and is not indexed by search engines.',
        ],
    ],

    'brand' => [
        'name' => 'Emplowise',
        'legal_name' => 'Emplowise HR Services',
        'essence' => 'Senior HR, without the senior hire.',
        'headline' => 'Remote HR Solutions for Growing Businesses',
        'subheadline' => 'Professional remote HR support for growing businesses.',
        'description' => 'Remote HR for teams of 5 to 100 in Pakistan. Payroll, performance, and policy on one published monthly retainer.',
        'category' => 'HR outsourcing and managed HR services',
        'market' => 'Pakistan',
        'expansion' => 'International expansion planned long term',
        'audience' => 'Startups, SMEs, and emerging talent',
        'delivery' => [
            'Remote HR support',
            'HR consultants backed by specialist support',
            'A dedicated HR representative',
            'Direct access to the client CEO or founder',
        ],
        'contact_note' => 'The Free HR Audit form is the only verified contact channel in this build. Direct phone and inbox details are confirmed in writing after your Free HR Audit.',
    ],

    /*
    | Public URL of the standalone EmploStart app. The launcher page links out
    | to it; override with EMPLOSTART_URL once the product repo is deployed.
    */
    'emplostart_url' => env('EMPLOSTART_URL', 'https://diocese-trees.github.io/EmploStart/'),

    /*
    | EmploStart is a standalone, permanently free utility. The pledge is
    | voluntary alignment, never a claim of UN endorsement or certification.
    */
    'fair_jobs_pledge' => [
        'kicker' => 'Fair jobs, fair wages',
        'heading' => 'Free because the HR work funds it.',
        'body' => 'EmploStart is a permanently free public utility. Emplowise remote HR services fund it, so there is no account, payment, or advertising inside the tools.',
        'points' => [
            ['label' => 'SDG 4', 'text' => 'Quality education: free career learning for anyone with a browser.'],
            ['label' => 'SDG 8', 'text' => 'Decent work and economic growth: better-prepared candidates and clearer hiring.'],
            ['label' => 'SDG 10', 'text' => 'Reduced inequalities: the same tools whether or not you can pay for coaching.'],
            ['label' => 'Generations', 'text' => 'Built for career pivoters, returners, and first-job candidates of every age.'],
            ['label' => 'Fair wages', 'text' => 'A voluntary pledge: no fabricated salary data, no pay-to-rank, and transparent benchmarking guidance.'],
        ],
        'disclaimer' => 'Voluntary alignment only. This is not a UN endorsement, certification, or partnership.',
    ],

    /*
    | The six utility modules, all shipped in the standalone EmploStart app.
    */
    'emplostart_modules' => [
        ['id' => 'pivot', 'num' => '01', 'title' => 'Career Pivot Planner', 'desc' => 'Transferable skills, priority gaps, bridge roles, and a 30-60-90 day plan.', 'status' => 'Live'],
        ['id' => 'reimagine', 'num' => '02', 'title' => 'Career Reimagination Lab', 'desc' => 'Three direction archetypes, each with a concrete validation experiment.', 'status' => 'Live'],
        ['id' => 'market', 'num' => '03', 'title' => 'Market Assessment', 'desc' => 'Role readiness, evidence gaps, and fair-wage negotiation readiness.', 'status' => 'Live'],
        ['id' => 'ats', 'num' => '04', 'title' => 'ATS Resume Analyzer', 'desc' => 'Score a resume against a posting, out of 100, with a fix list.', 'status' => 'Live'],
        ['id' => 'interview', 'num' => '05', 'title' => 'Interview Prep', 'desc' => 'Role and level questions with structured feedback and a model answer.', 'status' => 'Live'],
        ['id' => 'profile', 'num' => '06', 'title' => 'Profile Builder', 'desc' => 'Headline, summary, bullets, an ATS resume, and 14 prompts.', 'status' => 'Live'],
    ],

    'mission' => [
        'statement' => 'Everyone knows who owns what.',
        'body' => 'Emplowise exists so growing teams stop treating HR as paperwork that happens after the real work. We run the structure behind people operations, remote, so founders keep momentum and employees get clarity. It starts with your yearly goals: the HR is built to deliver them before the next quarter.',
        'origin' => 'Emplowise started after its founder experienced the steep cost of outsourced HR while building an earlier startup. The gap was obvious. Informal HR on one side, expensive enterprise consultancies on the other. Emplowise was built to sit in between, as accessible remote HR for growing Pakistani businesses.',
    ],

    'values' => [
        [
            'title' => 'Clarity',
            'text' => 'Plain language, defined ownership, and decisions that are written down. If a process cannot be explained in one page, it is not finished.',
        ],
        [
            'title' => 'Structure',
            'text' => 'Policies, records, payroll inputs, and review cadences that hold up when the team doubles. Structure is what makes growth calm.',
        ],
        [
            'title' => 'Substance',
            'text' => 'Practical execution over theory. Documentation, manager coaching, and clean handovers beat decks about culture.',
        ],
        [
            'title' => 'Trust',
            'text' => 'We do not promise legal outcomes, guaranteed productivity, or board authority. We promise a clear scope, honest status, and visible progress.',
        ],
    ],

    'positioning' => [
        'startups' => [
            'title' => 'For startups',
            'text' => 'You are hiring faster than you are documenting. Emplowise installs the minimum viable HR stack: contracts, policies, onboarding, payroll rhythm, and a person your team can actually ask.',
        ],
        'smes' => [
            'title' => 'For SMEs',
            'text' => 'You have people operations that grew by accident. Emplowise audits the gaps, then runs attendance, payroll inputs, performance cycles, and reporting on a fixed monthly retainer.',
        ],
        'talent' => [
            'title' => 'For career pivoters',
            'text' => 'Career pivoters and early career candidates get the same structured thinking, free. Six modules cover pivot planning, career reimagining, market assessment, ATS feedback, interview practice, and a profile builder.',
        ],
    ],

    /*
    | Three pillar showcase. Pillar grouping and the automation layer are
    | proposed packaging. Individual capabilities are confirmed services.
    */
    'pillars' => [
        [
            'slug' => 'payroll',
            'number' => '01',
            'name' => 'Payroll',
            'kicker' => 'Payroll and HR operations',
            'summary' => 'Salary inputs, leave, attendance, records, and onboarding run on one calendar instead of five spreadsheets.',
            'automation' => [
                'Automated input checks that flag missing attendance, unpaid leave, and salary changes before payroll closes.',
                'Leave balances that update from a single approved record, not a manual tally.',
                'Generated letters and records for onboarding, confirmation, and exits from approved templates.',
            ],
            'services' => [
                'Payroll',
                'Leave and attendance management',
                'Employee onboarding',
                'HR documentation and employee records',
            ],
            'status' => 'Delivered remotely by your HR team.',
        ],
        [
            'slug' => 'performance',
            'number' => '02',
            'name' => 'Performance Management',
            'kicker' => 'Performance and people insight',
            'summary' => 'Review cycles, manager support, and conflict handling that produce decisions rather than another form.',
            'automation' => [
                'Review cycle reminders, manager prompts, and summary drafts generated from submitted feedback.',
                'Structured assessment questionnaires scored and grouped before the leadership review.',
                'Monthly people reports assembled from attendance, headcount, and review data.',
            ],
            'services' => [
                'Performance support',
                'Employee relations and conflict support',
                'Organizational assessments',
                'Monthly HR reporting',
            ],
            'status' => 'Delivered remotely by your HR team.',
        ],
        [
            'slug' => 'policy',
            'number' => '03',
            'name' => 'Policy Making',
            'kicker' => 'Policy, compliance, and documentation',
            'summary' => 'Policies and SOPs written for how the team actually works, drafted in days and reviewed on a schedule.',
            'automation' => [
                'Policy drafts generated from a short intake of team size, industry, and current practice.',
                'Version tracking so every employee handbook change has an owner, a date, and an approval.',
                'Compliance checklists mapped to the policies that cover them, with gaps listed explicitly.',
            ],
            'services' => [
                'HR policies and SOPs',
                'Legal and compliance support',
                'Ongoing HR consultation',
                'Complete recruitment',
            ],
            'status' => 'Delivered remotely by your HR team.',
        ],
    ],

    'services' => [
        ['name' => 'Complete recruitment', 'pillar' => 'Managed HR Operations', 'note' => 'End to end hiring execution. Recruitment fee terms are confirmed in writing after your Free HR Audit.'],
        ['name' => 'Employee onboarding', 'pillar' => 'Managed HR Operations', 'note' => 'Structured onboarding workflow for new team members.'],
        ['name' => 'HR documentation and employee records', 'pillar' => 'Managed HR Operations', 'note' => 'Centralised personnel records and HR files.'],
        ['name' => 'Leave and attendance management', 'pillar' => 'Managed HR Operations', 'note' => 'Ongoing tracking of employee leave and attendance.'],
        ['name' => 'HR policies and SOPs', 'pillar' => 'Managed HR Operations', 'note' => 'Operational policies aligned to day to day management.'],
        ['name' => 'Payroll', 'pillar' => 'Managed HR Operations', 'note' => 'Payroll management. Exact tax and disbursement scope is confirmed in writing after your Free HR Audit.'],
        ['name' => 'Legal and compliance support', 'pillar' => 'Managed HR Operations', 'note' => 'Compliance coordination. Legal advice qualifications are confirmed in writing after your Free HR Audit.'],
        ['name' => 'Performance support', 'pillar' => 'Organizational Intelligence', 'note' => 'Structured support for manager and employee performance.'],
        ['name' => 'Employee relations and conflict support', 'pillar' => 'Organizational Intelligence', 'note' => 'Guidance for interdepartmental friction and workplace conflict.'],
        ['name' => 'Organizational assessments', 'pillar' => 'Organizational Intelligence', 'note' => 'Review of HR gaps ahead of quarterly planning. Cadence is confirmed in writing after your Free HR Audit.'],
        ['name' => 'Monthly HR reporting', 'pillar' => 'Executive HR Partnership', 'note' => 'Monthly people visibility delivered to client leadership.'],
        ['name' => 'Ongoing HR consultation', 'pillar' => 'Executive HR Partnership', 'note' => 'Continuous HR guidance backed by specialist consultants.'],
        ['name' => 'Specialist HR consultant support', 'pillar' => 'Executive HR Partnership', 'note' => 'Specialist input on defined people problems within agreed scope.'],
        ['name' => 'Dedicated HR representative', 'pillar' => 'Executive HR Partnership', 'note' => 'Direct access to the client CEO or founder. Full time versus shared allocation is confirmed in writing after your Free HR Audit.'],
    ],

    'methodology' => [
        [
            'step' => '01',
            'name' => 'Assess',
            'summary' => 'Audit the current state. Structure, contracts, records, payroll inputs, leave practice, performance habits, and unresolved people issues.',
            'output' => 'A written gap map and a prioritised list of fixes.',
        ],
        [
            'step' => '02',
            'name' => 'Identify',
            'summary' => 'Separate symptoms from causes. Pin down unclear ownership, missing policy coverage, and process steps that rely on memory.',
            'output' => 'A short action plan with owners and a sequence.',
        ],
        [
            'step' => '03',
            'name' => 'Improve',
            'summary' => 'Install the fix. Policies, SOPs, payroll calendar, onboarding flow, manager coaching, and the reporting that follows.',
            'output' => 'Running processes, not recommendations.',
        ],
        [
            'step' => '04',
            'name' => 'Review',
            'summary' => 'Check what held. Monthly reporting, quarterly assessment, and a standing list of open risks.',
            'output' => 'A review pack for leadership every cycle.',
        ],
    ],

    'outcomes' => [
        'Founders stop acting as the HR helpdesk.',
        'Payroll and leave run to a published calendar.',
        'Every employee has a record, a contract, and a policy set that matches reality.',
        'Managers get a script and a process for difficult conversations.',
        'Leadership sees headcount, attendance, and open people risks in one report.',
    ],

    'outcomes_note' => 'Baseline metrics and verified client results are confirmed in writing after your Free HR Audit. We do not publish invented percentages, savings, or client names.',

    'packages' => [
        [
            'slug' => 'starter',
            'name' => 'Starter',
            'capacity' => 'Up to 20 employees',
            'price_pkr' => 40000,
            'price_display' => 'PKR 40,000',
            'period' => 'per month',
            'summary' => 'For a small team that needs the basics done properly.',
            'best_for' => 'Startups up to 20 staff without a dedicated HR hire.',
            'featured' => false,
            'inclusions' => 'Service inclusions are confirmed in writing after your Free HR Audit.',
        ],
        [
            'slug' => 'business',
            'name' => 'Business',
            'capacity' => 'Up to 50 employees',
            'price_pkr' => 75000,
            'price_display' => 'PKR 75,000',
            'period' => 'per month',
            'summary' => 'For a growing company with real process volume.',
            'best_for' => 'SMEs up to 50 staff running payroll and performance cycles.',
            'featured' => true,
            'inclusions' => 'Service inclusions are confirmed in writing after your Free HR Audit.',
        ],
        [
            'slug' => 'enterprise',
            'name' => 'Enterprise',
            'capacity' => '50+ employees',
            'price_pkr' => null,
            'price_display' => 'Custom',
            'period' => 'scoped monthly',
            'summary' => 'For larger headcount, multiple departments, and specialist needs.',
            'best_for' => 'Organisations above 50 staff with layered management.',
            'featured' => false,
            'inclusions' => 'Scope, inclusions, and representative allocation are confirmed in writing after your Free HR Audit. Enterprise pricing is quoted after that audit.',
        ],
    ],

    'pricing_notes' => [
        'Prices are monthly retainers priced by employee headcount, confirmed in the 2026 business brief.',
        'Package by package inclusions are confirmed in writing after your Free HR Audit. The pricing page does not invent deliverables per tier.',
        'All packages are delivered remotely unless a different arrangement is agreed.',
        'Recruitment fees, payroll tax and disbursement scope, and legal advice qualifications are confirmed in writing after your Free HR Audit.',
    ],

    'faqs' => [
        [
            'category' => 'HR outsourcing questions',
            'items' => [
                [
                    'q' => 'What does Emplowise actually do?',
                    'a' => 'Emplowise runs remote HR for growing businesses. That covers recruitment, onboarding, employee records, leave and attendance, HR policies and SOPs, payroll, legal and compliance support, performance support, employee relations, monthly reporting, ongoing consultation, and organisational assessments.',
                ],
                [
                    'q' => 'Is Emplowise a software product or a service?',
                    'a' => 'A service. Emplowise is a remote HR team backed by specialist consultants and a dedicated HR representative. The people operations dashboard and automation described on this site support the service. They are not sold as standalone proprietary software.',
                ],
                [
                    'q' => 'Who is the right fit?',
                    'a' => 'Startups and SMEs in Pakistan that have people operations but no senior HR owner. Teams up to 20, up to 50, and above 50 each have a package.',
                ],
                [
                    'q' => 'Is delivery onsite or remote?',
                    'a' => 'Remote HR support is the confirmed delivery model. A dedicated HR representative provides direct access to the client CEO or founder. Full time versus shared allocation is confirmed in writing after your Free HR Audit.',
                ],
                [
                    'q' => 'Does Emplowise replace our leadership or board?',
                    'a' => 'No. Emplowise supplies structure, execution, and reporting. Decisions about the company stay with its leadership.',
                ],
                [
                    'q' => 'Can you guarantee legal compliance?',
                    'a' => 'No. Emplowise provides legal and compliance support and coordination. Legal advice qualifications and jurisdiction coverage are confirmed in writing after your Free HR Audit, and no compliance outcome is guaranteed.',
                ],
            ],
        ],
        [
            'category' => 'Pricing and packages',
            'items' => [
                [
                    'q' => 'How much does Emplowise cost?',
                    'a' => 'Starter is PKR 40,000 per month for up to 20 employees. Business is PKR 75,000 per month for up to 50 employees. Enterprise is custom priced for 50 or more employees.',
                ],
                [
                    'q' => 'What is included in each package?',
                    'a' => 'Package inclusions are confirmed in writing after your Free HR Audit. Rather than publish invented deliverables, that conversation confirms which services apply to your headcount and risk profile.',
                ],
                [
                    'q' => 'Are recruitment fees included?',
                    'a' => 'Recruitment is a confirmed service. Whether recruitment fees sit inside the retainer or are billed separately is confirmed in writing after your Free HR Audit.',
                ],
                [
                    'q' => 'Does payroll include tax filing and salary disbursement?',
                    'a' => 'Payroll management is confirmed. Exact tax, statutory contribution, and disbursement scope is confirmed in writing before work starts.',
                ],
                [
                    'q' => 'Is there a minimum contract length?',
                    'a' => 'Contract length is confirmed in writing after your Free HR Audit. Pricing is quoted as a monthly retainer.',
                ],
            ],
        ],
        [
            'category' => 'The free HR audit',
            'items' => [
                [
                    'q' => 'What is the Free HR Audit?',
                    'a' => 'A structured diagnostic conversation and review. It covers payroll inputs, performance practice, and policy coverage, then returns a prioritised gap list. It is the first stage of the Emplowise method.',
                ],
                [
                    'q' => 'How long does it take?',
                    'a' => 'The intake form takes about four minutes. The audit conversation itself is scheduled in the window you select. Assessment cadence is confirmed in writing after your Free HR Audit.',
                ],
                [
                    'q' => 'What do I need to prepare?',
                    'a' => 'Your current headcount, how payroll is run today, where performance reviews live, and which policies exist. If any of those are missing, that is itself a finding.',
                ],
                [
                    'q' => 'Is it really free?',
                    'a' => 'The audit is free. Any implementation work is quoted separately against a package.',
                ],
                [
                    'q' => 'Where do my details go?',
                    'a' => 'Submissions are stored in the Emplowise database and used to prepare your audit. The form collects company, work email, phone, headcount, pain points, and your preferred window. No payment details are collected.',
                ],
            ],
        ],
        [
            'category' => 'EmploStart',
            'items' => [
                [
                    'q' => 'Is the EmploStart actually free?',
                    'a' => 'Yes. All five tools are free, with no account required. They exist so early career candidates get structured feedback instead of guesswork.',
                ],
                [
                    'q' => 'How does the ATS score work?',
                    'a' => 'Your resume text is compared against the job description. The score weights keyword coverage, resume structure, and formatting hygiene. Missing keywords are listed so you can fix them yourself.',
                ],
                [
                    'q' => 'Will the resume builder pass every ATS?',
                    'a' => 'No tool can promise that. The builder produces a single column, plain text friendly resume with standard section headings, which is the format most parsers handle best.',
                ],
                [
                    'q' => 'Do you store my resume?',
                    'a' => 'Resume text and job descriptions are processed for the request and are not written to the database. Interview answers are also processed without storage.',
                ],
                [
                    'q' => 'Can I use the prompt library for work?',
                    'a' => 'Yes. Copy any prompt and adapt it. It is written for career pivoters, job seekers, and early career candidates.',
                ],
            ],
        ],
    ],

    'faq_note' => 'Answers use only confirmed business data. Anything unresolved is confirmed in writing after your Free HR Audit.',

    'student' => [
        'prompts' => [
            [
                'category' => 'Job search',
                'title' => 'Target role shortlist',
                'use' => 'Turn a vague job hunt into a ranked list.',
                'text' => 'Act as a career strategist. I work in [current field] and want to move into [target field] in [city or remote]. My strengths are [strengths]. List 8 realistic bridge roles, rank them by fit, and for each one name the single strongest proof point I should build in the next 30 days.',
            ],
            [
                'category' => 'Job search',
                'title' => 'Cold outreach that gets replies',
                'use' => 'Message a hiring manager without sounding generic.',
                'text' => 'Write a 90 word LinkedIn message to a hiring manager at [company] for the [role] opening. Reference one specific thing about their product. State what I bring in one sentence. End with a low pressure question. No flattery, no buzzwords.',
            ],
            [
                'category' => 'Job search',
                'title' => 'Referral request to an alum',
                'use' => 'Ask for a referral without making it awkward.',
                'text' => 'Draft a short message to a university alum at [company] asking about the [role]. Include why I am a fit in two concrete lines, attach nothing, and make saying no easy. Keep it under 110 words.',
            ],
            [
                'category' => 'Resume',
                'title' => 'Bullet rewrite with impact',
                'use' => 'Turn a duty into an achievement.',
                'text' => 'Rewrite this resume bullet using the pattern action verb, what I did, measurable result. Keep it under 22 words and ATS friendly. If a number is missing, give me a placeholder in brackets. Bullet: [paste bullet]',
            ],
            [
                'category' => 'Resume',
                'title' => 'Project to resume bullet',
                'use' => 'Make coursework sound like work.',
                'text' => 'Convert this academic project into two resume bullets for a [role] application. Emphasise tools, scope, and outcome. Do not invent metrics. Project details: [paste details]',
            ],
            [
                'category' => 'Resume',
                'title' => 'Gap and keyword audit',
                'use' => 'Compare your resume to a job post.',
                'text' => 'Compare my resume to this job description. List the 12 most important keywords in the posting, mark which appear in my resume, and rewrite my summary to include the top 5 missing ones naturally. Job description: [paste]. Resume: [paste]',
            ],
            [
                'category' => 'Interview',
                'title' => 'STAR answer builder',
                'use' => 'Structure a story before you tell it.',
                'text' => 'Turn these rough notes into a STAR answer for the question [question]. Situation in one sentence, task in one sentence, action in three steps, result with a number if I have one. Then give me a 20 second version. Notes: [paste]',
            ],
            [
                'category' => 'Interview',
                'title' => 'Mock interview, one question at a time',
                'use' => 'Practice under realistic pressure.',
                'text' => 'Act as a strict but fair interviewer for a [role] role at a [company type] company. Ask one question at a time. After each answer, score it out of 10 on structure, evidence, and clarity. Give one fix. Start with the question a real interviewer would open with.',
            ],
            [
                'category' => 'Interview',
                'title' => 'Weakness answer without a cliche',
                'use' => 'Answer the hardest question honestly.',
                'text' => 'Help me answer what is your greatest weakness for a [role] interview. Use a real weakness I can defend: [weakness]. Show the concrete system I use to manage it. Under 90 words, no humble brag phrasing.',
            ],
            [
                'category' => 'LinkedIn',
                'title' => 'Headline that ranks and reads well',
                'use' => 'Fix the line recruiters see first.',
                'text' => 'Write 5 LinkedIn headlines for a [role] candidate with skills in [skills]. Each must be under 120 characters, include one searchable keyword, and avoid the words passionate, aspiring, and enthusiast.',
            ],
            [
                'category' => 'LinkedIn',
                'title' => 'About section rewrite',
                'use' => 'Replace a biography with a pitch.',
                'text' => 'Rewrite my LinkedIn About section in first person, 120 words max. Open with what I do and for whom, give two proof points, close with what I am looking for. No adjectives without evidence. Current text: [paste]',
            ],
            [
                'category' => 'Career clarity',
                'title' => 'Two career paths, compared',
                'use' => 'Decide between options with a framework.',
                'text' => 'Compare a career in [path A] and [path B] for someone with my background: [background]. Use a table with day to day work, entry requirements, salary trajectory in Pakistan, and risk of automation. End with the three questions I should ask myself.',
            ],
            [
                'category' => 'Career clarity',
                'title' => '90 day plan for a new job',
                'use' => 'Start strong without overpromising.',
                'text' => 'Build a 90 day plan for a new [role] at a [company size] company. Split into days 1 to 30, 31 to 60, 61 to 90. For each phase give three actions, one relationship to build, and one thing to avoid.',
            ],
            [
                'category' => 'Career clarity',
                'title' => 'Explain a career gap',
                'use' => 'Handle the gap question calmly.',
                'text' => 'Write a 60 word answer explaining a [length] career gap caused by [reason]. Be direct, take one useful thing from the period, and pivot to why I am ready now. No apology spiral.',
            ],
        ],
    ],

    'pending_inputs' => [
        'Package by package service inclusions and limits',
        'Recruitment fee inclusion versus separate billing',
        'Payroll tax, statutory contribution, and disbursement scope',
        'Legal advice qualifications and jurisdiction coverage',
        'Dedicated representative allocation, full time versus shared',
        'Assessment cadence and industry focus',
        'Contract length and notice terms',
        'Verified client results, testimonials, and baseline metrics',
        'Direct contact phone number and inbox',
    ],

    /*
    | === PAGE COPY AND BUTTON LABELS ===
    | Headings, paragraphs, button labels, table headers, and helper notes
    | shown on the public site live here. Edit only the text inside quotes and
    | keep array keys unchanged. Input placeholders stay in the Blade file
    | because they are example values tied to one field.
    */
    'copy' => [

        'layout' => [
            'skip_link' => 'Skip to content',
            'logo_alt' => 'Emplowise home',
            'breadcrumb_home' => 'Home',
            'nav_audit_label' => 'Free HR Audit',
            'menu_open' => 'Open menu',
            'footer_note' => 'Emplowise is the HR partner for growing businesses in Pakistan. We run remote HR for teams of 5 to 100 and start every engagement with a free audit and a written gap list.',
            'footer_company_title' => 'Company',
            'footer_resources_title' => 'Resources',
            'footer_start_title' => 'Start here',
            'footer_tagline' => 'People analytics without bias, so CEOs can set new standards.',
            'footer_legal_label' => 'Legal',
            'footer_terms' => 'Terms',
            'footer_privacy' => 'Privacy',
            'footer_rights' => 'All rights reserved.',
            'back_to_top' => 'Back to top',
            'theme_toggle_dark' => 'Switch to dark theme',
            'theme_toggle_light' => 'Switch to light theme',
            'theme_footer_dark' => 'Dark mode',
            'theme_footer_light' => 'Light mode',
            'social_heading' => 'Socials',
            'consent_title' => 'Cookies, kept light.',
            'consent_body' => 'One small cookie stores this choice. Analytics and marketing stay off until you turn them on.',
            'consent_accept' => 'Accept all',
            'consent_essential' => 'Essential only',
            'consent_manage' => 'Manage',
            'consent_save' => 'Save choices',
            'consent_essential_label' => 'Essential',
            'consent_essential_desc' => 'Remembers this choice and keeps the site working. Always on.',
            'consent_analytics_label' => 'Analytics',
            'consent_analytics_desc' => 'Anonymous page counts, so we know what to improve.',
            'consent_marketing_label' => 'Marketing',
            'consent_marketing_desc' => 'Measures campaign links. Off by default.',
            'consent_footer' => 'Cookie settings',
            'consent_saved' => 'Cookie preferences saved.',
            'consent_note' => 'No analytics or marketing tags load today. These switches decide what may load later.',
        ],

        'contact' => [
            'breadcrumb' => 'Contact Us',
            'kicker' => 'Reach the team',
            'panel_kicker' => 'Direct line',
            'panel_heading' => 'Tell us what needs to work better.',
            'panel_body' => 'One policy, a full payroll cycle, or a second opinion on your current setup. Email the team or start with the free audit, and a named lead reads every message.',
            'panel_email_cta' => 'Email the team',
            'panel_audit_cta' => 'Start the Free HR Audit',
            'fact_hours_label' => 'Office hours',
            'fact_hours' => 'Mon–Fri, 9:00–18:00 PKT',
            'fact_reply_label' => 'First reply',
            'fact_reply' => 'Within one business day',
            'fact_market_label' => 'Based in',
            'fact_market' => 'Remote team, Pakistan',
            'next_heading' => 'What happens next',
            'next_body' => 'No discovery-call maze, and no form to fill in twice.',
            'next_steps' => [
                ['title' => 'You write once', 'body' => 'Send the context in a single email: team size, the gap, and the deadline you are working to.'],
                ['title' => 'A named lead replies', 'body' => 'One person answers with clarifying questions and a straight read on fit, usually within a business day.'],
                ['title' => 'Scope in writing', 'body' => 'If it fits, you get a fixed retainer, a start date, and what sits outside the scope before work begins.'],
            ],
            'ai_heading' => 'Ask your favourite GPT about Emplowise',
            'ai_body' => 'Each button opens a new tab with a ready-made question, so you get a second opinion before you talk to us.',
            'ai_query' => 'What is Emplowise, what remote HR services does it offer in Pakistan, and what are its package prices?',
            'ai_links' => [
                ['label' => 'ChatGPT', 'href' => 'https://chatgpt.com/?q='],
                ['label' => 'Claude', 'href' => 'https://claude.ai/new?q='],
                ['label' => 'Gemini', 'href' => 'https://gemini.google.com/app?q='],
                ['label' => 'Perplexity', 'href' => 'https://www.perplexity.ai/search?q='],
            ],
            'email_heading' => 'Email us',
            'email_body' => 'Scope questions, hiring support, or anything the audit form does not cover. Email the team directly.',
            'email_write' => 'Compose email',
            'email' => 'hello@emplowise.com',
            'copy_label' => 'Copy email',
            'copy_done' => 'Email copied.',
            'copy_fail' => 'Copy failed. Select the address and copy it manually.',
            'social_heading' => 'Direct line',
            'social_body' => 'Direct phone and inbox details are confirmed in writing after your free HR audit. Until then, email and the audit form are the two channels that work, and both reach the same three people.',
            'social_note' => 'A LinkedIn page is in progress. Until the address is real, it is not on this page.',
            'resources_heading' => 'Resources',
            'resources_note' => 'Every page here is public. The audit is the only form.',
            'resources' => [
                ['label' => 'Our Story', 'route' => 'what-we-are'],
                ['label' => 'What We Do', 'route' => 'what-we-do'],
                ['label' => 'Where\'s AI', 'route' => 'what-we-are-building'],
                ['label' => 'Why Us', 'route' => 'why-emplowise'],
                ['label' => 'EmploStart', 'route' => 'emplostart'],
                ['label' => 'FAQ', 'route' => 'faq'],
                ['label' => 'Free HR Audit', 'route' => 'free-hr-audit'],
            ],
        ],

        'legal' => [
            'terms' => [
                'updated' => '22 November 2026',
                'toc_label' => 'On this page',
                'sections' => [
                    [
                        'id' => 'who-we-are',
                        'heading' => 'Who we are',
                        'paragraphs' => [
                            'Emplowise HR Services ("Emplowise", "we") provides remote HR support to businesses in Pakistan. You can reach us at hello@emplowise.com.',
                            'These terms apply to this website and to any free HR audit or paid retainer you request from us. They do not replace a signed proposal or scope document; if the two differ, the signed document controls.',
                        ],
                    ],
                    [
                        'id' => 'using-this-site',
                        'heading' => 'Using this site',
                        'paragraphs' => [
                            'The site is informational. It describes our services, method, and published starting prices, but it is not legal, tax, or employment advice, and it is not a binding quote.',
                            'Prices, capacities, and inclusions are indicative until they are confirmed in writing after your free HR audit. We may correct errors and change published prices at any time.',
                        ],
                    ],
                    [
                        'id' => 'free-hr-audit',
                        'heading' => 'The free HR audit',
                        'paragraphs' => [
                            'The audit is free and carries no obligation to buy. You get a structured review of the information you share and a prioritised gap list.',
                            'The audit is a diagnostic conversation, not a compliance certification. It does not replace legal advice, a statutory audit, or a review of your full records. Do not send payroll data, identity documents, or other sensitive records until we agree on a secure route.',
                        ],
                    ],
                    [
                        'id' => 'retainers',
                        'heading' => 'Retainers and scope',
                        'paragraphs' => [
                            'Paid work runs on a monthly retainer. The scope, start date, named lead, and what sits outside the scope are confirmed in writing before work begins.',
                            'Prices are in Pakistani rupees and exclude applicable taxes unless the proposal says otherwise. Either side may end the retainer with the notice period stated in the proposal.',
                        ],
                    ],
                    [
                        'id' => 'outcomes',
                        'heading' => 'No guarantee of a business outcome',
                        'paragraphs' => [
                            'We commit to the method, the deliverables, and the response times in your scope. We cannot guarantee a hiring result, a revenue target, or a regulatory outcome, because those depend on decisions and records outside our control.',
                        ],
                    ],
                    [
                        'id' => 'intellectual-property',
                        'heading' => 'Intellectual property',
                        'paragraphs' => [
                            'The content on this site, including text, templates, and design, belongs to Emplowise or its licensors. You may read it and share links, but you may not resell it or present it as your own.',
                            'Templates and documents we create for a client are licensed to that client for internal use during and after the engagement, as set out in the proposal.',
                        ],
                    ],
                    [
                        'id' => 'liability',
                        'heading' => 'Liability',
                        'paragraphs' => [
                            'To the extent permitted by law, Emplowise is not liable for indirect or consequential loss, lost profits, or loss of data. Our total liability for a claim is limited to the fees paid for the month in which the issue arose.',
                            'Nothing in these terms limits liability that cannot be limited by law.',
                        ],
                    ],
                    [
                        'id' => 'law',
                        'heading' => 'Governing law and changes',
                        'paragraphs' => [
                            'These terms are governed by the laws of Pakistan, and the courts of Pakistan have jurisdiction over disputes.',
                            'If we change these terms, we update the date at the top of this page. Material changes will be flagged on the site before they take effect.',
                        ],
                    ],
                ],
            ],
            'privacy' => [
                'updated' => '22 November 2026',
                'toc_label' => 'On this page',
                'sections' => [
                    [
                        'id' => 'what-we-collect',
                        'heading' => 'What we collect',
                        'paragraphs' => [
                            'When you book a free HR audit, we collect the details you enter: your name, work email, company, headcount band, the areas you want reviewed, and any notes you add. If you email us, we keep that message and our reply.',
                            'Like most sites, our host may record basic technical information such as the requested page, browser type, and IP address in server logs. We use this to keep the site working and secure.',
                        ],
                    ],
                    [
                        'id' => 'why',
                        'heading' => 'Why we use it',
                        'paragraphs' => [
                            'We use audit responses to prepare and deliver the audit, to reply to you, and to keep a record of what was agreed. We use technical logs to operate and protect the site.',
                            'We do not sell personal data, and we do not use it for advertising. Analytics and marketing storage stay off until you turn them on in the cookie panel.',
                        ],
                    ],
                    [
                        'id' => 'cookies',
                        'heading' => 'Cookies and local storage',
                        'paragraphs' => [
                            'One essential preference stores your cookie choice for up to 180 days. It is kept in a first-party cookie and in local storage so the panel does not ask again on every page.',
                            'If you allow analytics or marketing, the site may load the relevant measurement later. No third-party analytics or advertising tags are installed today. You can change or withdraw your choice through the cookie settings link in the footer.',
                        ],
                    ],
                    [
                        'id' => 'sharing',
                        'heading' => 'Sharing and processors',
                        'paragraphs' => [
                            'We share data only with providers that help us run the site and reply to you, such as hosting and email. They act on our instructions and are not allowed to use your data for their own marketing.',
                            'We may disclose information if the law requires it, or to protect the rights and safety of Emplowise, our clients, or others.',
                        ],
                    ],
                    [
                        'id' => 'retention',
                        'heading' => 'How long we keep it',
                        'paragraphs' => [
                            'Audit responses are kept while we assess fit and for a reasonable period afterwards so we can answer follow-up questions. Email is kept for as long as needed to manage the relationship.',
                            'You can ask us to delete your audit response or email at any time, unless we are required to keep it for a legal or accounting reason.',
                        ],
                    ],
                    [
                        'id' => 'rights',
                        'heading' => 'Your choices and rights',
                        'paragraphs' => [
                            'You can ask for a copy of the personal data we hold about you, ask us to correct it, or ask us to delete it. You can also withdraw consent for optional cookies at any time.',
                            'Email hello@emplowise.com with the request. We may ask a clarifying question to make sure we are dealing with the right person.',
                        ],
                    ],
                    [
                        'id' => 'security',
                        'heading' => 'Security and international processing',
                        'paragraphs' => [
                            'We use HTTPS, access controls, and reputable hosting providers. No method of transmission or storage is perfectly secure, so please do not send sensitive records until we agree on a secure route.',
                            'Our providers may process data outside Pakistan. Where that happens, we rely on the provider\'s contractual safeguards and keep the set of providers small.',
                        ],
                    ],
                    [
                        'id' => 'contact-privacy',
                        'heading' => 'Contact and changes',
                        'paragraphs' => [
                            'Questions about this policy can go to hello@emplowise.com. If we change the policy, we update the date at the top of this page.',
                        ],
                    ],
                ],
            ],
        ],

        'team' => [
            'breadcrumb' => 'Meet The Team',
            'kicker' => 'The people behind the work',
            'tablist_label' => 'Emplowise team members',
            'showcase_lede' => 'Three people, three owners, one operating rhythm. Switch tabs to meet the team.',
            'focus_label' => 'Focus',
            'panel_note' => 'Profiles are confirmed. Portraits are placeholders until the official shoot.',
            'photo_todo' => 'TODO: replace this placeholder portrait with the confirmed team photo at the same 4:5 ratio.',
            'profiles' => [
                [
                    'id' => 'ali',
                    'num' => '01',
                    'name' => 'Muhammad Ali',
                    'role' => 'Advisor',
                    'focus' => 'Vision, governance, and operational growth',
                    'portrait' => 'img/team/placeholder-ali.png',
                    'portrait_webp' => 'img/team/placeholder-ali-840.webp',
                    'portrait_webp_small' => 'img/team/placeholder-ali-420.webp',
                    'alt' => 'Portrait placeholder for Muhammad Ali',
                    'bio' => "Strategic advisor guiding Emplowise's long-term vision, governance, and operational growth.",
                    'bio_todo' => 'TODO: replace the advisor bio with confirmed copy before launch.',
                ],
                [
                    'id' => 'hadi',
                    'num' => '02',
                    'name' => 'Abdul Hadi',
                    'role' => 'Co-Founder',
                    'focus' => 'Content Strategist & Strategic Marketer',
                    'portrait' => 'img/team/placeholder-hadi.png',
                    'portrait_webp' => 'img/team/placeholder-hadi-840.webp',
                    'portrait_webp_small' => 'img/team/placeholder-hadi-420.webp',
                    'alt' => 'Portrait placeholder for Abdul Hadi',
                    'bio' => 'A marketer by design and storyteller by passion, driving Strategic Partnerships at Advegen alongside founding roles at Emplowise and The Creative Nexus. Navigates the gray to align brand narrative, growth strategy, and impact across SDGs 1, 3, 4, and 8.',
                    'bio_todo' => null,
                ],
                [
                    'id' => 'nabil',
                    'num' => '03',
                    'name' => 'Samarah Nabil',
                    'role' => 'Co-Founder',
                    'focus' => 'Senior HR Business Manager & Analyst',
                    'portrait' => 'img/team/placeholder-nabil.png',
                    'portrait_webp' => 'img/team/placeholder-nabil-840.webp',
                    'portrait_webp_small' => 'img/team/placeholder-nabil-420.webp',
                    'alt' => 'Portrait placeholder for Samarah Nabil',
                    'bio' => "Brings 9+ years of experience in strategic HR, talent acquisition, and performance management, having engaged and supported 2,000+ employees. Leads Emplowise's core people operations, analytics, and organizational frameworks.",
                    'bio_todo' => null,
                ],
            ],
            'cta_heading' => 'Work with the team behind the structure.',
            'cta_body' => 'Bring a people problem, a payroll question, or a policy gap. The first conversation is a free HR audit, not a pitch.',
        ],

        'what-we-are-building' => [
            'breadcrumb' => 'Where\'s AI',
            'kicker' => 'Pre-launch division',
            'banner' => 'Emplowise AI Automation Division. Building soon.',
            'hero_sub' => 'Three modules are built and waiting on launch. The interfaces are real, the data is frozen, and nothing here pretends to be live.',
            'cta' => 'Building Soon',
            'cta_note' => 'No waitlist and no intake form yet. The confirmed HR services stay live while this division finishes its build.',
            'modules_heading' => 'Three modules already in the build.',
            'modules_lede' => 'Each module is scoped, designed, and waiting on launch approval. The dashboard shows how they fit together.',
            'module_status' => 'In build',
            'modules' => [
                [
                    'name' => 'Payroll Engine',
                    'icon' => 'payroll',
                    'body' => 'Turns attendance, leave, and salary inputs into a checked payroll run. Variance flags surface before payday, not after.',
                ],
                [
                    'name' => 'Performance Insight Layer',
                    'icon' => 'performance',
                    'body' => 'Reads review cycles and manager notes to show where goals drift, which teams need support, and what changed since last quarter.',
                ],
                [
                    'name' => 'Policy Co-Pilot',
                    'icon' => 'policy',
                    'body' => 'Answers policy questions from the approved handbook, cites the clause, and drafts an update when the rule changes.',
                ],
            ],
            'dashboard' => [
                'label' => 'Static preview of the Emplowise AI operations desk',
                'window' => 'AI operations desk',
                'tab' => 'Preview',
                'status' => 'Static preview',
                'caption' => 'Static preview. No live data, no client records.',
                'kpis' => [
                    ['label' => 'Payroll inputs', 'value' => 'Mapped'],
                    ['label' => 'Review signals', 'value' => 'Mapped'],
                    ['label' => 'Policy answers', 'value' => 'Mapped'],
                ],
                'rows' => [
                    ['name' => 'Payroll Engine', 'meta' => 'Attendance, leave, salary inputs', 'state' => 'Staged'],
                    ['name' => 'Performance Insight Layer', 'meta' => 'Review cycles and manager notes', 'state' => 'Staged'],
                    ['name' => 'Policy Co-Pilot', 'meta' => 'Approved handbook and clause citations', 'state' => 'Staged'],
                ],
            ],
            'back_label' => 'See the confirmed HR services',
        ],

        'chat' => [
            'aria_label' => 'Emplowise assistant',
            'title' => 'Emplowise assistant',
            'sub' => 'Grounded answers only',
            'close' => 'Close chat',
            'reset' => 'Start over',
            'intro' => 'hey. I am the Emplowise assistant. Ask about packages, what we run, or the free audit. Prices stay exact, guesses do not happen.',
            'quick' => [
                ['label' => 'Pricing', 'query' => 'What do the packages cost?'],
                ['label' => 'Services', 'query' => 'What HR services do you run?'],
                ['label' => 'Free audit', 'query' => 'How does the free HR audit work?'],
                ['label' => 'EmploStart', 'query' => 'What is in the EmploStart?'],
            ],
            'input_label' => 'Your message',
            'send' => 'Send',
            'note' => 'Verified data only. Scope details open in the free audit.',
            'launcher' => 'Ask Emplowise',
        ],

        // === PAGE: HOME ===
        'home' => [
            'kicker' => 'Remote HR for teams of 5 to 100',
            'hero_sub' => 'You do not need an HR department. You need someone who owns the work. Emplowise takes the payroll inputs, the appraisal cycle and the policy stack off your desk, and reports on them monthly like any other function. Same three people, every month, in writing.',
            'hero_more' => 'Why teams switch',
            'rotator_prefix' => 'Currently running',
            'cta_primary' => 'Book a free HR audit',
            'cta_secondary' => 'Open the free EmploStart tools',
            'proof' => [
                ['label' => 'Delivery', 'value' => 'One remote HR team, backed by specialist consultants.'],
                ['label' => 'Focus', 'value' => 'Payroll, performance, and policy on one monthly rhythm.'],
                ['label' => 'First step', 'value' => 'A free audit and a prioritised gap list in one conversation.'],
            ],
            'scroll_cue' => 'Jump to the shortcuts',
            'panel' => [
                'aria' => 'Emplowise operating stack',
                'float_a' => 'Remote delivery',
                'float_b' => 'Free career tools',
                'title' => 'The operating stack',
                'live' => 'One calendar',
                'rows' => [
                    ['label' => 'Payroll inputs, leave, records', 'state' => 'One source'],
                    ['label' => 'Review cycles and manager prompts', 'state' => 'One cadence'],
                    ['label' => 'Policies with owners and versions', 'state' => 'One register'],
                ],
                'services_suffix' => 'services',
                'stats' => [
                    'services' => 'Confirmed services in scope',
                    'stages' => 'Method stages per engagement',
                    'tools' => 'Free modules in EmploStart',
                ],
            ],
            'strip_label' => 'Confirmed services',
            'shortcuts' => [
                'kicker' => 'Navigate',
                'heading' => 'Four doors. Pick the one that fits.',
                'lede' => 'This page is the map, not the manual. Each card opens the page that answers that question in full.',
                'items' => [
                    ['label' => 'Our story', 'desc' => 'Who we are, who signs the work, and what we will not pretend.', 'route' => 'what-we-are', 'icon' => 'shield-check'],
                    ['label' => 'What we do', 'desc' => 'Fourteen confirmed services across payroll, performance and policy.', 'route' => 'what-we-do', 'icon' => 'chart-bars'],
                    ['label' => 'Pricing', 'desc' => 'PKR figures on the page, not behind a call.', 'route' => 'why-emplowise', 'icon' => 'card'],
                    ['label' => 'Free HR audit', 'desc' => 'Four minutes, then a written gap list.', 'route' => 'free-hr-audit', 'icon' => 'file-check'],
                ],
            ],
            'pillars' => [
                'kicker' => 'What we run',
                'lede' => 'Three pillars carry the work: payroll inputs and cycles, performance management, and policy making. Fourteen services sit inside them. Anything outside that list gets written down as out of scope rather than quietly absorbed.',
                'card_link' => 'See the confirmed services',
            ],
            'method' => [
                'kicker' => 'The method',
                'heading' => 'Assess. Identify. Improve. Review.',
                'lede' => 'One four-stage method runs every engagement, from the first audit conversation to the monthly review pack your leadership reads.',
            ],
            'pricing' => [
                'kicker' => 'Monthly retainers',
                'heading' => 'Priced by headcount, published on the page.',
                'lede' => 'Starter is PKR 40,000 a month for up to 20 staff. Business is PKR 75,000 for up to 50. Enterprise is quoted above 50. All monthly, all remote, all starting with the audit. Which services land in your retainer is confirmed in writing after that audit, so we publish the price, not a guess at your scope.',
                'tag' => 'Core tier',
                'inclusions_line' => 'Inclusions: confirmed in writing after your Free HR Audit.',
                'link' => 'See HR outsourcing pricing in Pakistan',
                'note' => 'Prices are monthly retainers in PKR. Recruitment fees, payroll tax scope, and legal advice qualifications are confirmed in writing after your Free HR Audit.',
            ],
            'student' => [
                'kicker' => 'Free career modules',
                'heading' => 'Six free career modules. No account. No cost.',
                'lede' => 'EmploStart gives career pivoters ATS scoring, mock interviews, a resume builder, profile rewrites, and 14 prompts today, with three planning modules in the standalone app. The local engine runs even without an AI key.',
                'cta_prompts' => 'Explore the :count prompts',
                'cta_ats' => 'Score your resume',
            ],
            'cta_band' => [
                'heading' => 'Start with a free HR audit. Leave with a written gap list.',
                'primary' => 'Book the free audit',
                'secondary' => 'Why teams pick Emplowise',
            ],
        ],

        // === PAGE: WHAT WE ARE ===
        'what-we-are' => [
            'breadcrumb' => 'Our Story',
            'why_kicker' => 'Why we exist',
            'category_line' => 'Category: :category. Market: :market. :expansion.',
            'quote_cite' => 'The Emplowise brand essence, 2026 Brand Kit',
            'values' => [
                'kicker' => 'Core values',
                'heading' => 'Four rules that decide how we work.',
                'lede' => 'Four operating commitments. Each one changes what we deliver and what we refuse to promise.',
            ],
            'audiences' => [
                'kicker' => 'Who we sit with',
                'heading' => 'Three audiences. One standard of clarity.',
                'lede' => 'Startups, SMEs, and early career candidates get the same structured thinking. Two are paid engagements. The third is free, on purpose.',
                'link_talent' => 'Open the free EmploStart',
                'link_client' => 'See our HR outsourcing pricing in Pakistan',
            ],
            'delivery' => [
                'kicker' => 'How we deliver',
                'lede' => 'Delivery is remote. A dedicated HR representative gives your CEO or founder direct access, backed by specialist consultants on defined problems.',
                'note' => 'Full time versus shared allocation of the dedicated representative is confirmed in writing after your Free HR Audit.',
            ],
            'scope' => [
                'kicker' => 'Scope boundaries',
                'heading' => 'What we confirm in writing after the audit.',
                'badge' => 'Confirmed after audit',
                'intro' => 'These details depend on your own records and contracts. We confirm each one in writing instead of filling the gap with invented facts.',
            ],
            'cta_band' => [
                'heading' => 'Bring us your current HR reality.',
                'primary' => 'Book the free audit',
                'secondary' => 'See the confirmed HR services',
            ],
        ],

        // === PAGE: WHAT WE DO ===
        'what-we-do' => [
            'breadcrumb' => 'What We Do',
            'kicker' => 'Confirmed services, one method',
            'chips' => [
                'Remote delivery',
                'Dedicated HR representative',
                'Specialist consultant backup',
                'Monthly reporting',
                'Free diagnostic audit',
            ],
            'pillar_label' => 'Pillar',
            'pillar_link' => 'Audit this pillar',
            'checks' => 'Workflow checks and templates',
            'services' => 'Confirmed services',
            'pillar_table' => ['Service', 'Scope note'],
            'catalogue' => [
                'kicker' => 'The full service catalogue',
                'lede' => 'The pillar showcase explains how the work fits together. This catalogue lists the actual service families used to scope an engagement.',
                'table' => ['Family', 'Service', 'Status and scope'],
                'note' => 'Every service above is confirmed in the 2026 brief. Package inclusion per tier is confirmed in writing after your Free HR Audit.',
            ],
            'method' => [
                'kicker' => 'Diagnostic method',
                'heading' => 'A repeatable path from audit to review.',
                'lede' => 'The same four-stage method sits behind every pillar and shapes the free HR audit, minus the implementation.',
            ],
            'outcomes' => [
                'kicker' => 'Operating outcomes',
                'heading' => 'What changes when the structure lands.',
                'lede' => 'These are operating outcomes Emplowise works toward. Baseline metrics and verified client results are confirmed in writing after your Free HR Audit, so no percentages are published here.',
                'cta' => 'Start with the audit',
            ],
            'cta_band' => [
                'heading' => 'See where your HR stack actually breaks.',
                'primary' => 'Book the free audit',
                'secondary' => 'See HR outsourcing pricing',
            ],
        ],

        // === PAGE: WHY EMPLOWISE ===
        'why-emplowise' => [
            'breadcrumb' => 'Why Us',
            'kicker' => 'Published pricing and scope',
            'why' => [
                'kicker' => 'The case for Emplowise',
                'heading' => 'One remote HR partner, with the price on the page.',
                'lede' => 'Three facts decide whether an HR partner is worth the retainer: what the work covers, who owns it, and what it costs. Emplowise publishes all three before the first call.',
                'cards' => [
                    ['title' => 'Remote by design', 'text' => 'Payroll, performance, and policy run remotely, so the cost goes into the work rather than an office.'],
                    ['title' => 'One accountable team', 'text' => 'A dedicated HR representative works with your founder or CEO, backed by specialist support.'],
                    ['title' => 'Published headcount pricing', 'text' => 'Starter, Business, and Enterprise are priced by headcount, with inclusions confirmed in writing.'],
                ],
            ],
            'tag' => 'Core tier',
            'bullets' => [
                'Services drawn from the confirmed catalogue.',
                'Deliverables per tier: confirmed in writing after your Free HR Audit.',
            ],
            'button_quote' => 'Request a quote',
            'button_audit' => 'Start with the free audit',
            'includes' => [
                'heading' => 'What the price includes, and what it does not',
            ],
            'guardrail' => [
                'kicker' => 'Scope guardrail',
                'heading' => 'No invented deliverables',
                'body' => 'A pricing page that invents inclusions is worse than one that admits the gap. The audit confirms which of the fourteen services apply to your headcount, risk profile, and current documentation.',
                'link' => 'Book the free audit',
            ],
            'compare' => [
                'kicker' => 'Side by side',
                'lede' => 'Headcount and price are confirmed. Inclusion rows are confirmed in writing after your Free HR Audit.',
                'table' => ['Package', 'Headcount', 'Monthly price', 'Best for', 'Inclusions'],
                'scoped' => 'scoped monthly',
                'per_month' => '/ month',
                'pending_cell' => 'Confirmed in writing after your Free HR Audit.',
                'note' => 'Recruitment fee inclusion, payroll tax scope, and legal advice qualifications are confirmed in writing after your Free HR Audit.',
            ],
            'catalogue' => [
                'kicker' => 'Confirmed catalogue',
                'heading' => 'Every service that can sit inside a retainer.',
                'lede' => 'Packages are priced by headcount. Which of the fourteen services sit inside your retainer is confirmed in scope.',
                'table' => ['Service', 'Operating family', 'Scope note'],
            ],
            'cta_band' => [
                'heading' => 'Pick a package after the audit, not before.',
                'primary' => 'Book the free audit',
                'secondary' => 'Read pricing questions',
            ],
        ],

        // === PAGE: EMPLOSTART ===
        'emplostart' => [
            'kicker' => 'Free career app',
            'stats' => ['Free modules', 'Cost, forever', 'Accounts required', 'AI by default'],
            'modules_kicker' => 'Six modules',
            'modules_heading' => 'One free app, from first question to signed offer.',
            'modules_lede' => 'Every module now lives in the standalone EmploStart app. This page is the map; the app is where the work happens.',
            'modules_live' => 'Open in app',
            'modules_next' => 'In build',
            'launch_cta' => 'Open EmploStart',
            'launch_secondary' => 'See the six modules',
            'launch_note' => 'Opens the standalone app in a new tab. Nothing installs, and no account is needed.',
            'library_kicker' => '14 prompt letters',
            'library_heading' => 'The EMPLOWISE and START prompt library: 14 letters, 14 prompts.',
            'library_lede' => 'Every letter in the name stands for one prompt, and the 14 prompts cover the whole journey from a first skills inventory to a signed offer or a planned pivot. Each prompt has its own id, so you can come back to it later.',
            'library_tracks' => [
                ['name' => 'EMPLOWISE', 'range' => 'P01–P09', 'promise' => 'Get shortlisted and into the interview'],
                ['name' => 'START', 'range' => 'P10–P14', 'promise' => 'Ace interviews, land fair offers, and pivot'],
            ],
            'library_link' => 'Open the 14-prompt library',
            'intake_kicker' => 'Three ways in',
            'intake_heading' => 'Start with a questionnaire, your LinkedIn profile, or your CV.',
            'intake_lede' => 'The app opens with one question: how do you want to start? Each lane collects the same readiness picture from a different source and saves it to your own device.',
            'intake_lanes' => [
                [
                    'title' => 'Diagnostic test and questionnaire',
                    'desc' => 'Answer 12 plain questions about your work, evidence, and targets. It takes about 2 to 3 minutes.',
                    'detail' => 'You get one of four readiness bands, plus the prompts that fit that band. There is no pass or fail, only a starting point.',
                ],
                [
                    'title' => 'LinkedIn profile and gap questions',
                    'desc' => 'Paste your LinkedIn URL and the profile text you want reviewed, then answer follow up questions.',
                    'detail' => 'The app works only from what you paste. It does not visit LinkedIn or scrape your profile, and it asks you what is missing instead of guessing.',
                ],
                [
                    'title' => 'CV upload or PDF-paste repair',
                    'desc' => 'Upload a .docx, .txt, or .md file, or paste text out of a PDF that will not read cleanly.',
                    'detail' => 'Extraction happens in your browser. You see a parse preview and confirm it before anything is saved.',
                ],
            ],
            'intake_note' => 'Each lane writes to a candidate vault that stays in your browser. Nothing is uploaded. Shared PC mode is session only: closing the tab wipes the vault.',
            'where_kicker' => 'What runs where',
            'where_heading' => 'Local by default. AI only when you choose it.',
            'where_lede' => 'The app is built so the useful work happens in your browser. Artificial intelligence is an optional extra, never the default.',
            'where_local_heading' => 'Runs locally in your browser',
            'where_local' => [
                'All six modules work with no network call.',
                'Nothing you type is uploaded, stored on a server, or sold.',
                'No account, no payment, and no advertising inside the tools.',
            ],
            'where_ai_heading' => 'Optional AI, off until you consent',
            'where_ai' => [
                'AI stays off until you turn it on and accept the consent note.',
                'Only the text you send in that moment leaves your browser.',
                'Every result has a local fallback, so the tool still works without AI.',
            ],
            'where_note' => 'EmploStart is free because Emplowise remote HR services fund it. The pledge below is voluntary alignment, not an endorsement.',
            'cta_band' => [
                'heading' => 'Hiring soon? Run the free HR audit before the first start date.',
                'primary' => 'Book the free HR audit',
                'secondary' => 'Read ATS resume questions',
            ],
        ],

        // === PAGE: FAQ ===
        'faq' => [
            'breadcrumb' => 'FAQ',
            'kicker' => ':count questions, :groups categories',
            'search_placeholder' => 'Search pricing, audit, ATS, payroll...',
            'search_label' => 'Search frequently asked questions',
            'clear_search' => 'Clear the search',
            'filter_all' => 'All',
            'answers_suffix' => 'answers',
            'empty' => 'No answer matches that search. Try one broader word, or ask the assistant.',
            'assistant' => [
                'kicker' => 'Still stuck',
                'heading' => 'Ask the Emplowise assistant',
                'badge' => 'Grounded answers',
                'body' => 'The assistant answers from the same verified data as this page. It does not guess prices, client names, or legal outcomes.',
                'button' => 'Open the assistant',
            ],
            'cta_band' => [
                'heading' => 'Ready for a real answer about your HR stack?',
                'primary' => 'Book the free audit',
                'secondary' => 'Open EmploStart free career tools',
            ],
        ],

        // === PAGE: FREE HR AUDIT ===
        'free-hr-audit' => [
            'breadcrumb' => 'Free HR Audit',
            'kicker' => 'Free diagnostic, about four minutes',
            'trust_note' => 'No cost. No obligation. No payroll data at this stage.',
            'steps' => [
                [
                    'heading' => 'Who are we speaking with?',
                    'note' => 'Work details only. No payment information is collected at any point.',
                ],
                [
                    'heading' => 'What does the team look like?',
                    'note' => 'Select every pain point that is true today. There is no wrong answer, only a diagnosis.',
                ],
                [
                    'heading' => 'When should we run it?',
                    'note' => 'Pick a preferred date within the next 60 days and a time window. We confirm the exact slot after reviewing the intake.',
                ],
                [
                    'heading' => 'Review and submit.',
                    'note' => 'Check the details, then submit. You will get a reference number on the next screen.',
                ],
            ],
            'pain_hint' => 'Pick at least one. Up to six.',
            'consent' => 'I agree to be contacted about this audit. Submissions are stored in the Emplowise database and used only to prepare the review.',
            'form' => [
                'name' => 'Contact name',
                'company' => 'Company',
                'email' => 'Work email',
                'phone' => 'Phone',
                'headcount' => 'Current headcount',
                'headcount_placeholder' => 'Select a band',
                'pain' => 'Where does it hurt most?',
                'notes' => 'Anything else we should know?',
                'notes_optional' => '(optional)',
                'date' => 'Preferred date',
                'date_hint' => 'Today through :date.',
                'window' => 'Preferred window',
                'window_placeholder' => 'Select a window',
                'website' => 'Website',
            ],
            'buttons' => [
                'back' => 'Back',
                'next' => 'Continue',
                'submit' => 'Submit audit request',
            ],
            'success' => [
                'kicker' => 'Request received',
                'body' => 'Keep the reference below. We review your intake, prepare a gap map, and confirm your window.',
                'date_label' => 'Preferred date',
                'slot_label' => 'Preferred window',
                'pending' => 'To be confirmed',
                'reference_placeholder' => 'EMP-000000-XXXX',
                'steps' => [
                    'We read the intake and flag missing records, payroll gaps, and policy exposure.',
                    'We confirm the audit window you selected, or propose the nearest available slot.',
                    'The audit conversation ends with a prioritised gap list and a short action plan.',
                ],
                'back' => 'Back to the site',
            ],
            'aside' => [
                'covers_heading' => 'What the audit covers',
                'covers' => [
                    'Payroll inputs, attendance, leave, and employee records.',
                    'Review cadence, manager capability, and unresolved people issues.',
                    'Policy coverage, SOPs, and documentation gaps.',
                    'A prioritised gap list and a short action plan.',
                ],
                'prepare_heading' => 'What to prepare',
                'prepare' => [
                    'Current headcount and how the team is structured.',
                    'How payroll is run today, and by whom.',
                    'Where performance reviews live, if anywhere.',
                    'Which policies exist and when they were last reviewed.',
                ],
                'not_heading' => 'What it is not',
                'not_body' => 'The audit is free and diagnostic. It is not legal advice, not a compliance guarantee, and not a commitment to a package. Implementation is quoted separately.',
                'packages_heading' => 'Package context',
                'packages_note' => 'Inclusions are confirmed in writing after your Free HR Audit.',
            ],
        ],
    ],

    /*
     * Showcase pages: public HTML editions of the brand-kit documents. The seo
     * block above owns the metadata; this block owns the download file and the
     * indexing decision. The deck stays out of the index until the dates and
     * open numbers in its notes are confirmed.
     */
    'showcase_pages' => [
        'company-profile' => [
            'kicker' => 'Company profile',
            'robots' => 'index,follow',
            'download' => [
                'path' => 'downloads/Emplowise-Company-Profile-2026.docx',
                'label' => 'Download the company profile',
                'meta' => 'Word document, 57 KB, editable',
            ],
        ],
        'portfolio' => [
            'kicker' => 'Portfolio',
            'robots' => 'index,follow',
            'download' => [
                'path' => 'downloads/Emplowise-Portfolio-2026.docx',
                'label' => 'Download the portfolio',
                'meta' => 'Word document, 56 KB, editable',
            ],
        ],
        'pitch-deck' => [
            'kicker' => 'Pitch deck',
            'robots' => 'noindex,follow',
            'download' => [
                'path' => 'downloads/Emplowise-Pitch-Deck-2026.pptx',
                'label' => 'Download the deck',
                'meta' => 'PowerPoint file, 80 KB, 8 slides',
            ],
        ],
    ],

    /*
     * The /docs hub indexes every public document. Downloads are the built
     * Office files; routes point at the HTML editions where one exists.
     */
    'docs_index' => [
        'intro' => 'Eight downloadable documents and three live editions, grouped by the question they answer. Every file is editable and every figure is traceable to the site configuration.',
        'groups' => [
            [
                'id' => 'brand',
                'kicker' => 'Brand and voice',
                'heading' => 'Brand system and writing rules.',
                'intro' => 'The 2026 identity, plus the voice, evidence tiers, and naming rules every other document follows.',
                'items' => [
                    [
                        'title' => 'Brand Kit 2026',
                        'desc' => 'Logo, colour, typography, and surface rules for every public asset.',
                        'path' => 'downloads/Emplowise-Brand-Kit-2026.docx',
                        'download_label' => 'Download the brand kit',
                    ],
                    [
                        'title' => 'Branding Guidelines 2026',
                        'desc' => 'Voice, evidence tiers, interface rules, and the naming lock.',
                        'path' => 'downloads/Emplowise-Branding-Guidelines-2026.docx',
                        'download_label' => 'Download the guidelines',
                    ],
                ],
            ],
            [
                'id' => 'commercial',
                'kicker' => 'Commercial',
                'heading' => 'What Emplowise sells, and the proof behind it.',
                'intro' => 'The company profile, the shipped-work portfolio, and the eight-slide pitch deck. Each one has a live HTML edition and an editable Office file.',
                'items' => [
                    [
                        'title' => 'Company Profile 2026',
                        'desc' => 'Category, market, services, pricing, and scope in one profile.',
                        'path' => 'downloads/Emplowise-Company-Profile-2026.docx',
                        'download_label' => 'Download the profile',
                        'route' => 'company-profile',
                        'route_label' => 'Read the profile',
                    ],
                    [
                        'title' => 'Portfolio 2026',
                        'desc' => 'Repo-evidenced shipped work, with no invented clients.',
                        'path' => 'downloads/Emplowise-Portfolio-2026.docx',
                        'download_label' => 'Download the portfolio',
                        'route' => 'portfolio',
                        'route_label' => 'Read the portfolio',
                    ],
                    [
                        'title' => 'Pitch Deck 2026',
                        'desc' => 'Eight slides, one buyer question each, with speaker notes.',
                        'path' => 'downloads/Emplowise-Pitch-Deck-2026.pptx',
                        'download_label' => 'Download the deck',
                        'route' => 'pitch-deck',
                        'route_label' => 'Read the deck',
                    ],
                ],
            ],
            [
                'id' => 'employability',
                'kicker' => 'Employability',
                'heading' => 'The free career library candidates actually use.',
                'intro' => 'The EMPLOWISE x START 14-prompt library, the three intake lanes, and the local candidate vault that powers EmploStart.',
                'items' => [
                    [
                        'title' => 'Prompt Library 2026',
                        'desc' => 'Fourteen prompts, one per letter, plus the three intake lanes and the private vault rules.',
                        'path' => 'downloads/Emplowise-Start-Prompt-Library-2026.docx',
                        'download_label' => 'Download the prompt library',
                    ],
                ],
            ],
            [
                'id' => 'engineering',
                'kicker' => 'Engineering',
                'heading' => 'How the site is built and shipped.',
                'intro' => 'The route inventory, the module specs, the static export pipeline, and the verified toolchain.',
                'items' => [
                    [
                        'title' => 'Website Breakdown 2026',
                        'desc' => 'Routes, partials, engines, and the static export pipeline.',
                        'path' => 'downloads/Emplowise-Website-Breakdown-2026.docx',
                        'download_label' => 'Download the breakdown',
                    ],
                    [
                        'title' => 'Tool Stack 2026',
                        'desc' => 'Verified versions, commands, and the upgrade ledger.',
                        'path' => 'downloads/Emplowise-Tool-Stack-2026.docx',
                        'download_label' => 'Download the tool stack',
                    ],
                ],
            ],
        ],
        'notes' => [
            'kicker' => 'Operational notes',
            'heading' => 'Notes that live with the code.',
            'body' => 'Five Markdown notes stay in the repository beside the code they describe. They are versioned with the implementation, not downloaded as Office files.',
            'items' => [
                ['file' => 'STATIC_HOSTING.md', 'desc' => 'GitHub Pages build, Web3Forms key, and the keyless fallback.'],
                ['file' => 'VERCEL.md', 'desc' => 'Vercel static deployment, headers, redirects, and rollback.'],
                ['file' => 'REPO_TOPOLOGY.md', 'desc' => 'The original repository, the private mirror, and the export path.'],
                ['file' => 'THEME_AND_MOTION.md', 'desc' => 'Tokens, dark mode, motion timings, and reduced motion.'],
                ['file' => 'CONTENT_EDITING_GUIDE.md', 'desc' => 'Editing config copy without breaking SEO.'],
            ],
        ],
        'gate' => 'Every download is generated from one Markdown source and checked for OOXML validity, raw Markdown tokens, and internal verification tags before it ships.',
        'cta' => [
            'heading' => 'Need a document this hub does not carry?',
            'body' => 'Ask the team. Anything not published here is confirmed in writing rather than drafted from memory.',
            'primary' => 'Talk to the team',
            'secondary' => 'Read the FAQ',
        ],
    ],

    'nav' => [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Our Story', 'route' => 'what-we-are'],
        ['label' => 'Meet The Team', 'route' => 'what-we-are', 'hash' => 'team'],
        ['label' => 'Why Us', 'route' => 'why-emplowise'],
        ['label' => 'EmploStart', 'route' => 'emplostart'],
        ['label' => 'Where\'s AI', 'route' => 'what-we-are-building'],
        ['label' => 'Contact Us', 'route' => 'contact'],
    ],

    // The footer repeats the primary links plus the action pages.
    'footer_company' => [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Our Story', 'route' => 'what-we-are'],
        ['label' => 'What We Do', 'route' => 'what-we-do'],
        ['label' => 'Meet The Team', 'route' => 'what-we-are', 'hash' => 'team'],
        ['label' => 'Why Us', 'route' => 'why-emplowise'],
        ['label' => 'Company Profile', 'route' => 'company-profile'],
        ['label' => 'Portfolio', 'route' => 'portfolio'],
        ['label' => 'Where\'s AI', 'route' => 'what-we-are-building'],
    ],

    'footer_resources' => [
        ['label' => 'EmploStart', 'route' => 'emplostart'],
        ['label' => 'Pitch Deck', 'route' => 'pitch-deck'],
        ['label' => 'Documentation', 'route' => 'docs'],
        ['label' => 'FAQ', 'route' => 'faq'],
        ['label' => 'Contact Us', 'route' => 'contact'],
        ['label' => 'Free HR Audit', 'route' => 'free-hr-audit'],
    ],
];
