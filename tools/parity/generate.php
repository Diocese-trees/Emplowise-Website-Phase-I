<?php

declare(strict_types=1);

/**
 * Generates the parity fixture used to prove the browser toolkit returns the
 * same results as the PHP engines. Run from the project root:
 *
 *   php tools/parity/generate.php
 *
 * The output is committed so the Node check can run without booting Laravel.
 */

use App\Services\StudentToolkit;

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config(['ai.alibaba.api_key' => '']);

/** @var StudentToolkit $toolkit */
$toolkit = $app->make(StudentToolkit::class);

$fixtures = [
    'interview' => [
        'thin' => [
            'role' => 'Data and Analytics',
            'level' => 'Entry level',
            'question' => 'Describe a dataset you cleaned. What was messy, and what did you change?',
            'answer' => 'I cleaned a dataset.',
        ],
        'star' => [
            'role' => 'Data and Analytics',
            'level' => '1 to 3 years',
            'question' => 'Tell me about an analysis that changed a decision. What was the decision?',
            'answer' => 'Last year at my internship I was asked to explain why weekly signups dropped. I pulled the last eight weeks of funnel data, found a broken onboarding step on Android, and wrote a one page summary for the growth lead. I then tested the fix with two cohorts. As a result signups recovered 18 percent in three weeks and the team kept the weekly check.',
        ],
        'no_numbers' => [
            'role' => 'Software Engineering',
            'level' => 'Internship',
            'question' => 'Walk me through a project you built end to end. What broke, and how did you fix it?',
            'answer' => 'During my final year project I built a small booking app with two classmates. My task was the payment flow. I wrote the API, tested it with fake cards, and fixed a race condition that created duplicate bookings. The result was a working demo and a clearer test plan for the team, though we never measured how many duplicates we prevented.',
        ],
        'rambling' => [
            'role' => 'Marketing',
            'level' => 'Career switch',
            'question' => 'How would you find an audience for a product with no budget?',
            'answer' => 'Basically I would start by talking to people, like honestly I think you have to kind of find out where they already are, sort of, and then uh make something useful for that place instead of just posting everywhere. I mean at my last job I ran a small newsletter for a student society, and I wrote the emails and organised the schedule, and it grew because we asked people to forward it, and I think that is the sort of thing that works with no budget, because you are not paying for ads, you are just being useful, and people share useful things, and then you learn from what they reply to, and you do more of that, and it compounds over time if you keep showing up, which is the hard part, because everyone gets bored after a month, but if you keep a simple cadence and a clear promise, people stay, and the list becomes the asset, and then you can test offers against it without spending anything, so the first thing is to pick one place and one promise and commit to it for a quarter.',
        ],
        'hyphens' => [
            'role' => 'Human Resources',
            'level' => '1 to 3 years',
            'question' => "Tell me about a cross-functional project you owned. What did you change, and what did the team's results look like?",
            'answer' => "At my last role I owned the onboarding re-vamp for a 35 person team. The old check-list was out of date and new joiners spelled it 'on-boarding', so nobody trusted it. I ran four one-to-one interviews, re-wrote every step as a single PDF, and set a 30 day review with each manager's input. Time-to-first-task fell from 9 days to 4, and the 90-day check-in score went up by 11 points.",
        ],
        'long_question' => [
            'role' => 'Marketing',
            'level' => 'Entry level',
            'question' => 'Walk me through a campaign you ran end to end — the brief, the audience, the channels you picked, the budget you had, the copy you wrote, the results you measured, what you would do differently next time, and how you explained all of it to a manager who only had five minutes to read it.',
            'answer' => 'I ran a launch campaign for a student discount card. I wrote the brief, picked two channels, and spent the whole budget on campus posters and a referral code. We sold 240 cards in three weeks against a target of 150, and the referral code brought 60 of them, so next time I would put more budget there.',
        ],
    ],
    'questions' => [
        ['role' => 'Data and Analytics', 'level' => 'Entry level'],
        ['role' => 'Software Engineering', 'level' => 'Internship'],
        ['role' => 'Product Management', 'level' => 'Career switch'],
        ['role' => 'Human Resources', 'level' => '1 to 3 years'],
        ['role' => 'Something Unmapped', 'level' => 'Entry level'],
    ],
    'ats' => [
        'synonyms' => [
            'resume' => "Senior Data Analyst\nKarachi | analyst@example.com | +92 300 1234567\n\nSummary\nAnalyst who communicates clearly and builds dashboards with JS.\n\nExperience\nSenior Data Analyst - Northline\n- Built dashboards in JS for three teams.\n- Communicated findings to two managers every week.\n- Cleaned survey data for 1,200 responses.\n\nEducation\nBS Computer Science\n\nSkills\nJS, SQL, communication, data cleaning",
            'job_description' => "Senior Data Analyst\nWe need strong communication and JavaScript skills for reporting work. Experience with SQL and data cleaning is required. You will build dashboards and present findings to managers.",
        ],
        'title_mismatch' => [
            'resume' => "Marketing Coordinator\nLahore | coordinator@example.com | +92 301 7654321\n\nProfile\nCoordinator with two years of campaign experience.\n\nEmployment\nMarketing Coordinator - Bluebird\n- Ran 4 email campaigns with a 22 percent open rate.\n- Wrote 12 landing pages and tested three headlines.\n\nQualifications\nBS Marketing\n\nTools\nHubSpot, Google Analytics, SEO",
            'job_description' => "Growth Marketing Manager\nWe are hiring a growth marketer to own paid acquisition and lifecycle campaigns. You will manage a budget, run experiments, and report on CAC and retention.",
        ],
        'sparse' => [
            'resume' => "Ali Khan\nKarachi\n\nI am a student looking for my first job. I have worked on class projects and I learn fast. I enjoy teamwork and I am available immediately for any role in a good company.",
            'job_description' => "Junior Operations Associate\nWe are seeking a junior associate to support logistics, track shipments, and keep records accurate. Excel and communication skills are required.",
        ],
        'hyphens' => [
            'resume' => "Hana Yousuf\nKarachi | hana@example.com | +92 300 1112223\n\nSummary\nCross-functional HR generalist who owns the team's onboarding and e-commerce hiring.\n\nExperience\nHR Generalist - Northline\n- Ran 12 cross-functional onboarding sessions for the team's new joiners.\n- Cut time-to-hire from 45 to 28 days across three e-commerce roles.\n- Wrote the on-boarding check-list that four managers now use.\n\nEducation\nBBA Human Resources\n\nSkills\nOnboarding, stakeholder communication, HRIS, e-commerce hiring",
            'job_description' => "HR Generalist\nWe are hiring an HR generalist for our e-commerce team. You will own cross-functional onboarding, cut time-to-hire, and keep the team's check-list current. Experience with HRIS and stakeholder communication is required.",
        ],
    ],
    'profile' => [
        'weak' => [
            'headline' => 'Final year CS student passionate about data',
            'summary' => 'I am a hardworking student looking for internship opportunities in data analytics.',
            'bullets' => "Worked on a dashboard project\nResponsible for cleaning survey data",
            'target_role' => '',
        ],
        'aimed' => [
            'headline' => 'Marketing graduate who likes content',
            'summary' => 'Aspiring marketer and team player with a passion for storytelling.',
            'bullets' => "Helped with social media\nDid research for campaigns\nManaged a small budget",
            'target_role' => 'Growth Marketing Associate',
        ],
        'empty_summary' => [
            'headline' => 'Software engineering student',
            'summary' => '',
            'bullets' => "Built a booking app in PHP\nTested APIs with Postman\nWrote documentation for the team",
            'target_role' => '',
        ],
        'long_summary' => [
            'headline' => 'HR generalist who likes people — and processes, check-lists, and the unglamorous work of keeping a 40 person team running smoothly',
            'summary' => 'A people-first HR generalist with six years across payroll, policy, and onboarding — mostly for small teams in Karachi. I have owned the full employee life-cycle for a 40 person company: contracts, the staff handbook, the payroll calendar, and the review cycle. I rebuilt an onboarding pack that cut time-to-first-task from nine days to four, and I keep every process documented so nobody depends on my memory. I am looking for a role where HR is treated as a system, not a rescue service. I write plainly, I chase the details, and I am comfortable saying no to work that has no owner.',
            'bullets' => "Rebuilt the onboarding pack and cut time-to-first-task from 9 to 4 days\nOwned payroll for 40 people across two entities\nWrote the staff handbook with the team's managers",
            'target_role' => 'HR Operations Manager',
        ],
    ],
];

$out = [
    'interview' => [],
    'questions' => [],
    'ats' => [],
    'profile' => [],
];

foreach ($fixtures['interview'] as $name => $case) {
    $out['interview'][$name] = $toolkit->interviewFeedback($case['role'], $case['level'], $case['question'], $case['answer']);
}

foreach ($fixtures['questions'] as $case) {
    $out['questions'][] = $toolkit->interviewQuestions($case['role'], $case['level']);
}

foreach ($fixtures['ats'] as $name => $case) {
    $out['ats'][$name] = $toolkit->atsScore($case['resume'], $case['job_description']);
}

foreach ($fixtures['profile'] as $name => $case) {
    $out['profile'][$name] = $toolkit->optimizeProfile($case['headline'], $case['summary'], $case['bullets'], $case['target_role']);
}

$out['inputs'] = $fixtures;

$json = json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
if ($json === false) {
    fwrite(STDERR, "Could not encode fixtures: ".json_last_error_msg()."\n");
    exit(1);
}

file_put_contents(__DIR__.'/fixtures.json', $json."\n");
echo "Wrote ".strlen($json)." bytes to tools/parity/fixtures.json\n";
