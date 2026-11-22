<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CspReportController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\AuditRequestController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/what-we-are', [SiteController::class, 'whatWeAre'])->name('what-we-are');
Route::get('/what-we-do', [SiteController::class, 'whatWeDo'])->name('what-we-do');
Route::get('/pricing', [SiteController::class, 'pricing'])->name('pricing');
Route::get('/student-hub', [SiteController::class, 'studentHub'])->name('student-hub');
Route::get('/faq', [SiteController::class, 'faq'])->name('faq');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::get('/team', [SiteController::class, 'team'])->name('team');
Route::get('/ai-automation', [SiteController::class, 'aiAutomation'])->name('ai-automation');
Route::get('/free-hr-audit', [SiteController::class, 'audit'])->name('free-hr-audit');

Route::get('/sitemap.xml', function () {
    $pages = ['home', 'what-we-are', 'what-we-do', 'pricing', 'student-hub', 'faq', 'contact', 'team', 'ai-automation', 'free-hr-audit'];
    $urls = '';
    foreach ($pages as $name) {
        $priority = $name === 'home' ? '1.0' : '0.8';
        $urls .= '<url><loc>'.route($name).'</loc><changefreq>weekly</changefreq><priority>'.$priority.'</priority></url>';
    }
    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
        .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$urls.'</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

Route::prefix('api')->name('api.')->group(function (): void {
    Route::post('/csp-report', CspReportController::class)
        ->middleware('throttle:120,1')
        ->name('csp.report');

    Route::post('/chat', ChatController::class)
        ->middleware('throttle:30,1')
        ->name('chat');

    Route::post('/audit', [AuditRequestController::class, 'store'])
        ->middleware('throttle:15,1')
        ->name('audit.store');

    Route::post('/student/interview/questions', [StudentController::class, 'interviewQuestions'])
        ->middleware('throttle:20,1')
        ->name('student.interview.questions');

    Route::post('/student/interview/feedback', [StudentController::class, 'interviewFeedback'])
        ->middleware('throttle:20,1')
        ->name('student.interview.feedback');

    Route::post('/student/ats', [StudentController::class, 'ats'])
        ->middleware('throttle:20,1')
        ->name('student.ats');

    Route::post('/student/profile', [StudentController::class, 'profile'])
        ->middleware('throttle:20,1')
        ->name('student.profile');
});
