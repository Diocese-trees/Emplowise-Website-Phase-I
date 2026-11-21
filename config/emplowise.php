<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Emplowise Content Source of Truth
|--------------------------------------------------------------------------
|
| Every string below is either taken from the Emplowise Brand Kit 2026,
| the 2026 business brief, or the 2025 Replit export recorded in the
| workspace transcripts. Anything the sources do not confirm is labelled
| "Pending Input" instead of being invented.
|
| Authority order: Brand Kit 2026 > 2026 business brief > 2025 Replit export.
|
*/

return [

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

    'outcomes_note' => 'Baseline metrics and client results are confirmed in writing after your Free HR Audit. This build does not publish invented percentages, savings, or client names.',

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

    'legacy_pricing' => [
        'note' => 'The 2025 Replit export listed three differently named tiers. They are recorded here for transparency and are not sold on this site.',
        'tiers' => [
            ['name' => 'Foundation', 'price' => 'PKR 40,000'],
            ['name' => 'Growth', 'price' => 'PKR 65,000'],
            ['name' => 'Scale', 'price' => 'PKR 100,000'],
        ],
    ],

    'faqs' => [
        [
            'category' => 'Working with Emplowise',
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
                [
                    'q' => 'Why did older pages list Foundation, Growth, and Scale?',
                    'a' => 'Those were 2025 tiers from an earlier site export. The current 2026 packages are Starter, Business, and Enterprise. The older tiers are kept in the page footnote for transparency and are not sold here.',
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
            'category' => 'Student hub',
            'items' => [
                [
                    'q' => 'Is the student hub actually free?',
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

    'nav' => [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'What We Are', 'route' => 'what-we-are'],
        ['label' => 'What We Do', 'route' => 'what-we-do'],
        ['label' => 'Pricing', 'route' => 'pricing'],
        ['label' => 'Student Hub', 'route' => 'student-hub'],
        ['label' => 'FAQ', 'route' => 'faq'],
    ],
];
