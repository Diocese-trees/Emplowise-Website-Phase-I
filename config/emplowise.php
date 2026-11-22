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
            'title' => 'Remote HR Services in Pakistan for Growing Teams | Emplowise',
            'description' => 'Emplowise runs remote HR for startups and SMEs in Pakistan. Payroll, performance, and policy from PKR 40,000 a month. Book a free audit.',
            'h1' => 'Remote HR services in Pakistan, run by one accountable team.',
            'h1_accent' => 'one accountable team',
            'h2' => 'What our remote HR services in Pakistan cover.',
            'intro' => 'Emplowise runs remote HR services in Pakistan for startups and SMEs. We run payroll inputs, performance cycles, and policy for growing teams, with retainers from PKR 40,000 a month.',
        ],

        // === PAGE: WHAT WE ARE ===
        'what-we-are' => [
            'primary_keyword' => 'HR outsourcing company in Pakistan',
            'secondary_keywords' => ['outsourced HR support', 'dedicated HR representative'],
            'title' => 'HR Outsourcing Company in Pakistan, Remote Team | Emplowise',
            'description' => 'Emplowise is an HR outsourcing company in Pakistan. A remote HR team installs payroll, performance, and policy structure for startups and SMEs.',
            'h1' => 'An HR outsourcing company in Pakistan that answers directly.',
            'h1_accent' => 'that answers directly',
            'h2' => 'How an HR outsourcing company in Pakistan delivers.',
            'intro' => 'Emplowise is an HR outsourcing company in Pakistan and the remote HR team for startups and SMEs. We install the payroll, performance, and policy structure behind your people operations, then run it, so founders stop acting as the HR helpdesk.',
        ],

        // === PAGE: WHAT WE DO ===
        'what-we-do' => [
            'primary_keyword' => 'HR operations outsourcing',
            'secondary_keywords' => ['payroll and compliance services', 'HR process management'],
            'title' => 'HR Operations Outsourcing for Pakistani SMEs | Emplowise',
            'description' => 'HR operations outsourcing across payroll, performance, and policy. Fourteen confirmed services, remote delivery in Pakistan, one four-stage method.',
            'h1' => 'HR operations outsourcing across three confirmed pillars.',
            'h1_accent' => 'three confirmed pillars',
            'h2' => 'Fourteen services inside HR operations outsourcing.',
            'intro' => 'Emplowise runs HR operations outsourcing for startups and SMEs in Pakistan, fully remote. Fourteen confirmed services sit across three pillars, and every engagement follows the same four-stage method. Retainers start at PKR 40,000 a month.',
        ],

        // === PAGE: WHY EMPLOWISE ===
        'why-emplowise' => [
            'primary_keyword' => 'why Emplowise',
            'secondary_keywords' => ['remote HR partner in Pakistan', 'HR outsourcing pricing in Pakistan'],
            'title' => 'Why Emplowise for Remote HR in Pakistan | Emplowise',
            'description' => 'See why Emplowise is the remote HR partner for growing teams in Pakistan, with published pricing from PKR 40,000, clear scope, and a free HR audit.',
            'h1' => 'Why Emplowise is the remote HR partner for growing teams.',
            'h1_accent' => 'the remote HR partner',
            'h2' => 'Why Emplowise publishes HR pricing by headcount.',
            'intro' => 'Why Emplowise? Because remote HR should come with published pricing, a named team, and a scope you can read before you commit. Starter is PKR 40,000 for up to 20 staff, Business is PKR 75,000 for up to 50, and Enterprise is quoted above 50.',
        ],

        // === PAGE: EMPLOSTART ===
        'emplostart' => [
            'primary_keyword' => 'free ATS resume checker',
            'secondary_keywords' => ['mock interview practice', 'free resume builder'],
            'title' => 'Free ATS Resume Checker and 5 Career Tools | Emplowise',
            'description' => 'Five free career tools from EmploStart: an ATS resume checker, mock interview practice, resume builder, profile optimizer, and 14 prompts. No account.',
            'h1' => 'A free ATS resume checker built for early career candidates.',
            'h1_accent' => 'free ATS resume checker',
            'h2' => 'Free ATS resume checker and JD analyzer.',
            'intro' => 'EmploStart gives early career candidates five free tools: a free ATS resume checker, mock interviews, a resume builder, a profile optimizer, and 14 prompts. No account, no cost, no invented feedback.',
        ],

        // === PAGE: FAQ ===
        'faq' => [
            'primary_keyword' => 'HR outsourcing questions',
            'secondary_keywords' => ['Emplowise pricing answers', 'HR scope questions'],
            'title' => 'HR Outsourcing Questions Answered Clearly | Emplowise',
            'description' => 'Answers to HR outsourcing questions about Emplowise services, pricing from PKR 40,000, the free HR audit, and EmploStart. Confirmed facts only.',
            'h1' => 'HR outsourcing questions answered with confirmed facts.',
            'h1_accent' => 'with confirmed facts',
            'h2' => 'HR outsourcing questions',
            'intro' => 'Emplowise answers HR outsourcing questions for startups and SMEs in Pakistan. EmploStart answers career questions with the same rule: confirmed facts only.',
        ],

        // === PAGE: FREE HR AUDIT ===
        'free-hr-audit' => [
            'primary_keyword' => 'free HR audit in Pakistan',
            'secondary_keywords' => ['HR gap assessment', 'HR diagnostic review'],
            'title' => 'Free HR Audit in Pakistan, Booked in 4 Minutes | Emplowise',
            'description' => 'Book a free HR audit in Pakistan. Share headcount, payroll, performance, and policy pain points in about four minutes and get a prioritised gap list.',
            'h1' => 'A free HR audit in Pakistan, booked in four minutes.',
            'h1_accent' => 'booked in four minutes',
            'h2' => 'Your free HR audit in Pakistan: next steps.',
            'intro' => 'Emplowise runs a free HR audit in Pakistan for startups and SMEs, delivered remotely. You get a structured review of payroll inputs, performance practice, and policy coverage, plus a prioritised gap list.',
        ],

        // === PAGE: CONTACT ===
        'contact' => [
            'primary_keyword' => 'contact Emplowise',
            'secondary_keywords' => ['Emplowise contact details', 'remote HR support contact'],
            'title' => 'Contact Emplowise for Remote HR Support | Emplowise',
            'description' => 'Contact Emplowise for remote HR support in Pakistan. Email the team, ask an AI assistant, or book a free HR audit and get a prioritised gap list.',
            'h1' => 'Contact Emplowise without a maze of forms.',
            'h1_accent' => 'without a maze of forms',
            'h2' => 'Contact Emplowise through the channel you prefer.',
            'intro' => 'Contact Emplowise for remote HR support in Pakistan. Ask an AI assistant, email the team, or jump straight to services, pricing, and the free HR audit.',
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
            'title' => 'What We Are Building: AI Automation for HR | Emplowise',
            'description' => 'Emplowise is building AI automation for HR: a payroll engine, a performance insight layer, and a policy co-pilot. The division is pre-launch.',
            'h1' => 'What we are building: AI automation for HR, in the open.',
            'h1_accent' => 'AI automation for HR',
            'h2' => 'Where AI automation for HR will plug in.',
            'intro' => 'Emplowise is building AI automation for HR. A payroll engine, a performance insight layer, and a policy co-pilot are in pre-launch, and this page shows the shape of the division.',
        ],
    ],

    'brand' => [
        'name' => 'Emplowise',
        'legal_name' => 'Emplowise HR Services',
        'essence' => 'Clarity built on structure.',
        'headline' => 'Remote HR Solutions for Growing Businesses',
        'subheadline' => 'Professional remote HR support for growing businesses.',
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

    'mission' => [
        'statement' => 'Empower people. Simplify work. Build momentum.',
        'body' => 'Emplowise exists so growing teams stop treating HR as paperwork that happens after the real work. We run the structure behind people operations, remote, so founders keep momentum and employees get clarity.',
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
            'title' => 'For emerging talent',
            'text' => 'Students and early career candidates get the same structured thinking, free. Interview practice, ATS feedback, resume building, profile rewrites, and a prompt library.',
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
                    'a' => 'Yes. Copy any prompt and adapt it. It is written for job seekers, students, and early career candidates.',
                ],
            ],
        ],
    ],

    'faq_note' => 'Answers use only confirmed business data. Anything unresolved is confirmed in writing after your Free HR Audit.',

    'student' => [
        'intro' => 'Five working tools. No account. No cost. Built on the same structure Emplowise uses for client HR, pointed at your career.',
        'roles' => [
            'Software Engineering',
            'Data and Analytics',
            'Product and Project',
            'Marketing',
            'Sales and Business Development',
            'Finance and Accounting',
            'HR and People Operations',
            'Operations and Supply Chain',
            'General / Any role',
        ],
        'levels' => ['Internship', 'Entry level', '1 to 3 years', 'Career switch'],
        'prompts' => [
            [
                'category' => 'Job search',
                'title' => 'Target role shortlist',
                'use' => 'Turn a vague job hunt into a ranked list.',
                'text' => 'Act as a career strategist. I am a final year student in [degree] looking for roles in [city or remote]. My strengths are [strengths]. List 8 realistic role titles, rank them by fit, and for each one name the single strongest proof point I should build in the next 30 days.',
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
            'footer_note' => 'Remote HR for growing businesses in Pakistan. One accountable team, published pricing, and scope you can read before you commit.',
            'footer_company_title' => 'Company',
            'footer_resources_title' => 'Resources',
            'footer_start_title' => 'Start here',
            'footer_legal' => 'Scope details are confirmed in writing after your Free HR Audit. Nothing on this site is a legal or compliance guarantee.',
            'footer_market' => 'Market:',
            'footer_rights' => 'All rights reserved.',
            'back_to_top' => 'Back to top',
        ],

        'contact' => [
            'breadcrumb' => 'Contact',
            'kicker' => 'Reach the team',
            'ai_heading' => 'Ask an AI you already use about Emplowise',
            'ai_body' => 'Each button opens a new tab with a ready-made question. Use it for a second opinion before you talk to us.',
            'ai_query' => 'What is Emplowise, what remote HR services does it offer in Pakistan, and what are its package prices?',
            'ai_links' => [
                ['label' => 'ChatGPT', 'href' => 'https://chatgpt.com/?q='],
                ['label' => 'Claude', 'href' => 'https://claude.ai/new?q='],
                ['label' => 'Gemini', 'href' => 'https://gemini.google.com/app?q='],
                ['label' => 'Perplexity', 'href' => 'https://www.perplexity.ai/search?q='],
            ],
            'email_heading' => 'Email us',
            'email_body' => 'Scope questions, hiring support, or anything the audit form does not cover. Email the team directly.',
            'email' => 'hello@emplowise.com',
            'copy_label' => 'Copy email',
            'copy_done' => 'Email copied.',
            'copy_fail' => 'Copy failed. Select the address and copy it manually.',
            'social_heading' => 'Socials',
            'social_body' => 'Profiles are being set up, so these links are marked pending.',
            'socials' => [
                ['label' => 'LinkedIn', 'icon' => 'linkedin'],
                ['label' => 'Instagram', 'icon' => 'instagram'],
                ['label' => 'X', 'icon' => 'x'],
                ['label' => 'GitHub', 'icon' => 'github'],
            ],
            'resources_heading' => 'Resources',
            'resources' => [
                ['label' => 'What We Are', 'route' => 'what-we-are'],
                ['label' => 'What We Do', 'route' => 'what-we-do'],
                ['label' => 'What We Are Building', 'route' => 'what-we-are-building'],
                ['label' => 'Why Emplowise', 'route' => 'why-emplowise'],
                ['label' => 'EmploStart', 'route' => 'emplostart'],
                ['label' => 'FAQ', 'route' => 'faq'],
                ['label' => 'Free HR Audit', 'route' => 'free-hr-audit'],
            ],
            'micro_copyright' => '© 2026 Emplowise.',
            'micro_status' => 'Website is actively being built in the background.',
        ],

        'team' => [
            'breadcrumb' => 'Team',
            'kicker' => 'The people behind the work',
            'window_label' => 'Emplowise team showcase',
            'window_title' => 'team.emplowise',
            'window_meta' => 'people operations',
            'tablist_label' => 'Emplowise team members',
            'showcase_lede' => 'Three people, three owners, one operating rhythm. Switch tabs to meet the team.',
            'kicker' => 'The people behind the work',
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
                    'alt' => 'Portrait placeholder for Samarah Nabil',
                    'bio' => "Brings 9+ years of experience in strategic HR, talent acquisition, and performance management, having engaged and supported 2,000+ employees. Leads Emplowise's core people operations, analytics, and organizational frameworks.",
                    'bio_todo' => null,
                ],
            ],
            'cta_heading' => 'Work with the team behind the structure.',
            'cta_body' => 'Bring a people problem, a payroll question, or a policy gap. The first conversation is a free HR audit, not a pitch.',
        ],

        'what-we-are-building' => [
            'breadcrumb' => 'What We Are Building',
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
            'kicker' => 'Remote HR, built on structure',
            'hero_sub' => 'Get payroll, performance, and policy off your desk. One remote HR team, a dedicated representative, and retainers from PKR 40,000 a month.',
            'rotator_prefix' => 'Currently running',
            'cta_primary' => 'Book a free HR audit',
            'cta_secondary' => 'Open the EmploStart',
            'proof' => [
                ['label' => 'Delivery', 'value' => 'One remote HR team, backed by specialist consultants.'],
                ['label' => 'Focus', 'value' => 'Payroll, performance, and policy on one monthly rhythm.'],
                ['label' => 'First step', 'value' => 'A free audit and a prioritised gap list in one conversation.'],
            ],
            'scroll_cue' => 'Scroll to the operating model',
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
                    'tools' => 'Free tools in the EmploStart',
                ],
            ],
            'strip_label' => 'Confirmed services',
            'pillars' => [
                'kicker' => 'What we run',
                'lede' => 'Every pillar opens the confirmed services, workflow checks, and templates behind it. Nothing is listed that the team does not deliver.',
                'card_link' => 'See the confirmed services',
            ],
            'method' => [
                'kicker' => 'The method',
                'heading' => 'Assess. Identify. Improve. Review.',
                'lede' => 'One four-stage method runs every engagement, from the first audit conversation to the monthly review pack your leadership reads.',
            ],
            'pricing' => [
                'kicker' => 'Monthly retainers',
                'heading' => 'Pricing by headcount, not by guesswork.',
                'lede' => 'Three confirmed tiers. Starter is PKR 40,000 for up to 20 staff, Business is PKR 75,000 for up to 50, and Enterprise is quoted above 50.',
                'tag' => 'Core tier',
                'inclusions_line' => 'Inclusions: confirmed in writing after your Free HR Audit.',
                'link' => 'See HR outsourcing pricing in Pakistan',
                'note' => 'Prices are monthly retainers in PKR. Recruitment fees, payroll tax scope, and legal advice qualifications are confirmed in writing after your Free HR Audit.',
            ],
            'student' => [
                'kicker' => 'Free career tools',
                'heading' => 'Five free career tools. No account. No cost.',
                'lede' => 'EmploStart gives early career candidates ATS scoring, mock interviews, a resume builder, profile rewrites, and 14 prompts. The local engine runs even without an AI key.',
                'cta_prompts' => 'Explore the :count prompts',
                'cta_ats' => 'Score your resume',
            ],
            'cta_band' => [
                'heading' => 'Start with a free HR audit. Leave with a gap list.',
                'primary' => 'Book the free audit',
                'secondary' => 'See how the four-stage method works',
            ],
        ],

        // === PAGE: WHAT WE ARE ===
        'what-we-are' => [
            'breadcrumb' => 'What We Are',
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
            'breadcrumb' => 'Why Emplowise',
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
            'kicker' => 'Free career tools',
            'filter_all' => 'All',
            'stats' => ['Working tools', 'Cost, forever', 'Accounts required', 'Ready prompts'],
            'tabs' => [
                ['id' => 'interview', 'num' => '01', 'title' => 'Mock interview', 'desc' => 'Three questions, structured feedback'],
                ['id' => 'ats', 'num' => '02', 'title' => 'ATS score', 'desc' => 'Match a resume to a job description'],
                ['id' => 'resume', 'num' => '03', 'title' => 'Resume builder', 'desc' => 'Single column, parser safe, printable'],
                ['id' => 'profile', 'num' => '04', 'title' => 'Profile optimizer', 'desc' => 'Headline, summary, and bullet rewrites'],
                ['id' => 'prompts', 'num' => '05', 'title' => 'Prompt library', 'desc' => 'Copy, paste, adapt, use'],
            ],
            'interview' => [
                'heading' => 'Mock interview simulator',
                'body' => 'Pick a role and a level. Answer one question at a time. Get a score, three strengths, three fixes, and a model answer built without invented facts.',
                'status' => 'Local engine ready',
                'how_heading' => 'What happens after you press start',
                'how_steps' => [
                    'Three questions for your role and level, served one at a time.',
                    'Each answer is scored out of 100 on structure, evidence, and clarity.',
                    'You get three strengths, three fixes, and a model answer to adapt.',
                ],
                'how_note' => 'Nothing is uploaded to an account. Answers stay in this browser session, and the local scoring engine runs even when no AI key is configured.',
                'feedback_heading' => 'Structured feedback',
                'next' => 'Next question',
                'start' => 'Start set',
                'role_label' => 'Role',
                'level_label' => 'Level',
                'answer_label' => 'Your answer',
                'answer_meta' => 'Use one concrete example. Numbers help.',
                'counter_zero' => '0 characters',
                'submit' => 'Score this answer',
                'out_of' => 'Out of 100',
                'score_note' => 'Scored on structure, evidence, clarity, and ownership. Local heuristics run first. AI refines when a key is configured.',
                'breakdown_heading' => 'Why this score',
                'probe_heading' => 'Ask yourself next',
                'worked' => 'What worked',
                'fix_next' => 'Fix next time',
                'model_answer' => 'Model answer shape',
            ],
            'ats' => [
                'heading' => 'Free ATS resume checker and JD analyzer',
                'body' => 'Paste a resume and a job description. Get a 0 to 100 score, the keywords you are missing, and the structure and formatting checks a parser cares about.',
                'status' => 'Explainable scoring',
                'submit' => 'Score the match',
                'report_heading' => 'Match report',
                'fixes_heading' => 'Fix list',
                'resume_label' => 'Resume text',
                'resume_hint' => 'Text is processed for this request and not stored.',
                'jd_label' => 'Job description',
                'jd_hint' => 'The keyword list is built from this posting only.',
                'out_of' => 'Out of 100',
                'score_note' => 'Weighting: keywords 55, structure 25, formatting 20. The score is deterministic, so re-running it stays honest.',
                'matched' => 'Matched keywords',
                'missing' => 'Missing keywords',
                'structure' => 'Structure checks',
                'format' => 'Formatting checks',
                'edits' => 'AI suggested edits',
            ],
            'resume' => [
                'heading' => 'ATS resume builder',
                'body' => 'One column. Standard headings. Plain text friendly. Fill the form, watch the preview, then print to PDF or copy the text. No account, no watermark.',
                'status' => 'Print ready',
                'print' => 'Print or save PDF',
                'download' => 'Download .txt',
                'copy' => 'Copy text',
                'preview_note' => 'Print it, save it as PDF, or copy the plain text into any application form.',
                'add_role' => 'Add role',
                'add_education' => 'Add education',
                'example' => 'Load example',
                'clear' => 'Clear draft',
                'save_note' => 'The draft saves in this browser only. Clear it any time, and nothing is sent to a server.',
                'skills_hint' => 'Comma separated. Use the exact tool names from the job posting.',
                'form' => [
                    'name' => 'Full name',
                    'title' => 'Target title',
                    'email' => 'Email',
                    'phone' => 'Phone',
                    'location' => 'Location',
                    'linkedin' => 'LinkedIn',
                    'summary' => 'Summary',
                    'experience' => 'Experience',
                    'education' => 'Education',
                    'skills' => 'Skills',
                ],
                'preview' => [
                    'name' => 'Your Name',
                    'title' => 'Target title',
                    'contact' => 'email · phone · location · LinkedIn',
                    'summary_section' => 'Summary',
                    'summary_empty' => 'Add a three line summary.',
                    'experience_section' => 'Experience',
                    'experience_empty' => 'Add at least one role.',
                    'education_section' => 'Education',
                    'education_empty' => 'Add your degree or qualification.',
                    'skills_section' => 'Skills',
                    'skills_empty' => 'Add skills.',
                ],
            ],
            'profile' => [
                'heading' => 'Profile optimizer',
                'body' => 'Paste your headline, summary, and bullets. Get three headline options, a tighter summary, and bullets rewritten to start with an action verb. Placeholders appear where a number is missing.',
                'status' => 'No invented metrics',
                'submit' => 'Rewrite my profile',
                'headlines_heading' => 'Headline options',
                'summary_heading' => 'Summary',
                'bullets_heading' => 'Rewritten bullets',
                'copy' => 'Copy',
                'fixes_heading' => 'Fix list',
                'form' => [
                    'target' => 'Target role',
                    'headline' => 'Current headline',
                    'summary' => 'Current summary',
                    'bullets' => 'Experience bullets',
                ],
            ],
            'prompts' => [
                'heading' => 'Prompt library',
                'body' => ':count copy-paste prompts for job search, resumes, interviews, LinkedIn, and career clarity. Fill the brackets with your details and run them in any assistant.',
                'status' => 'Yours to adapt',
                'empty' => 'No prompts match that search. Try a broader word.',
                'copy' => 'Copy prompt',
                'expand' => 'Expand',
                'search_label' => 'Search prompts',
                'search_placeholder' => 'Search prompts by task or keyword',
                'clear_search' => 'Clear',
                'copy_visible' => 'Copy visible prompts',
            ],
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

    'nav' => [
        ['label' => 'What We Are', 'route' => 'what-we-are'],
        ['label' => 'What We Do', 'route' => 'what-we-do'],
        ['label' => 'What We Are Building', 'route' => 'what-we-are-building'],
        ['label' => 'Why Emplowise', 'route' => 'why-emplowise'],
        ['label' => 'Team', 'route' => 'what-we-are', 'hash' => 'team'],
        ['label' => 'EmploStart', 'route' => 'emplostart'],
        ['label' => 'Contact Us', 'route' => 'contact'],
    ],

    // The footer repeats the primary links plus the action pages.
    'footer_company' => [
        ['label' => 'What We Are', 'route' => 'what-we-are'],
        ['label' => 'What We Do', 'route' => 'what-we-do'],
        ['label' => 'What We Are Building', 'route' => 'what-we-are-building'],
        ['label' => 'Why Emplowise', 'route' => 'why-emplowise'],
    ],

    'footer_resources' => [
        ['label' => 'EmploStart', 'route' => 'emplostart'],
        ['label' => 'FAQ', 'route' => 'faq'],
        ['label' => 'Contact Us', 'route' => 'contact'],
        ['label' => 'Free HR Audit', 'route' => 'free-hr-audit'],
    ],
];
