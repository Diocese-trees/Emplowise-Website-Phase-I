<?php

use App\Services\EmplowiseAssistant;
use Illuminate\Contracts\Console\Kernel;

// Generates the chat routing corpus with the PHP oracle. Run this whenever the
// rules in EmplowiseAssistant::detectIntent change, then run
// tools/parity/check-chat-routing.mjs against an exported site.

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$assistant = $app->make(EmplowiseAssistant::class);

$cases = [
    ['What do the packages cost?', ''],
    ['How much is the Starter package per month?', ''],
    ['What is included in the Business plan?', ''],
    ['Do you have a free plan?', ''],
    ['Which HR services do you run?', ''],
    ['How many HR services do you offer in total?', ''],
    ['What are the three pillars of your HR service?', ''],
    ['How does the engagement process work and how many stages are there?', ''],
    ['When does the review cycle happen?', ''],
    ['How long does onboarding take?', ''],
    ['How much is the free audit?', ''],
    ['How does the free HR audit work?', ''],
    ['Can I book a meeting this week?', ''],
    ['What is in the EmploStart?', ''],
    ['How does the ATS score work?', ''],
    ['Is the resume builder free?', ''],
    ['Do you store my resume or interview answers in your database?', ''],
    ['Are my interview answers saved anywhere?', ''],
    ['How many prompts are in the library?', ''],
    ['Who is Emplowise?', ''],
    ['Where are you based? Can we visit an office?', ''],
    ['Do you work onsite or remote?', ''],
    ['Why should we choose you over an in-house HR person?', ''],
    ['How do I contact you?', ''],
    ['Is there an email address or phone number?', ''],
    ['Are there any open roles?', ''],
    ['I want to apply for a job', ''],
    ['Do you handle payroll and tax?', ''],
    ['What about leave and attendance?', ''],
    ['How do you handle performance reviews?', ''],
    ['Do you write policies and SOPs?', ''],
    ['Do you guarantee we pass a compliance audit?', ''],
    ['Can you guarantee legal compliance for our payroll?', ''],
    ['Which details are still pending?', ''],
    ['Thanks, that helps', ''],
    ['Hi there', ''],
    ['What is the weather today?', ''],
    ['Do you have testimonials from clients?', ''],
    ['How many customers do you have?', ''],
    // Follow-up turns: the previous intent should win when the scores are close.
    ['How much is it?', 'pricing'],
    ['And for a bigger team?', 'pricing'],
    ['How do I start?', 'audit'],
    ['Does it cost anything?', 'audit'],
    ['What about salary processing?', 'payroll'],
    ['Tell me more', 'services'],
    ['And the review part?', 'performance'],
    ['Where exactly?', 'where'],
    ['When can we begin?', 'when'],
];

$rows = [];
foreach ($cases as [$message, $previous]) {
    $rows[] = [
        'message' => $message,
        'previous' => $previous,
        'intent' => $assistant->detectIntent($message, $previous),
    ];
}

file_put_contents(__DIR__.'/chat-routes.json', json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n");

echo 'Wrote '.count($rows)." routing cases\n";
