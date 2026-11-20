<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

final class SiteController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'brand' => config('emplowise.brand'),
            'pillars' => config('emplowise.pillars'),
            'methodology' => config('emplowise.methodology'),
            'packages' => config('emplowise.packages'),
            'promptCount' => count(config('emplowise.student.prompts')),
        ]);
    }

    public function whatWeAre(): View
    {
        return view('pages.what-we-are', [
            'brand' => config('emplowise.brand'),
            'mission' => config('emplowise.mission'),
            'values' => config('emplowise.values'),
            'positioning' => config('emplowise.positioning'),
            'pending' => config('emplowise.pending_inputs'),
        ]);
    }

    public function whatWeDo(): View
    {
        $services = collect(config('emplowise.services'))->groupBy('pillar');

        return view('pages.what-we-do', [
            'pillars' => config('emplowise.pillars'),
            'servicesByPillar' => $services,
            'methodology' => config('emplowise.methodology'),
            'outcomes' => config('emplowise.outcomes'),
            'outcomesNote' => config('emplowise.outcomes_note'),
        ]);
    }

    public function pricing(): View
    {
        return view('pages.pricing', [
            'packages' => config('emplowise.packages'),
            'notes' => config('emplowise.pricing_notes'),
            'legacy' => config('emplowise.legacy_pricing'),
            'services' => config('emplowise.services'),
        ]);
    }

    public function studentHub(): View
    {
        return view('pages.student-hub', [
            'student' => config('emplowise.student'),
            'brand' => config('emplowise.brand'),
        ]);
    }

    public function faq(): View
    {
        $groups = config('emplowise.faqs');

        return view('pages.faq', [
            'groups' => $groups,
            'total' => collect($groups)->sum(fn (array $group): int => count($group['items'])),
            'note' => config('emplowise.faq_note'),
        ]);
    }

    public function audit(): View
    {
        return view('pages.audit', [
            'packages' => config('emplowise.packages'),
            'headcountBands' => [
                '1-10' => '1 to 10 people',
                '11-20' => '11 to 20 people',
                '21-50' => '21 to 50 people',
                '51-100' => '51 to 100 people',
                '100+' => 'More than 100 people',
            ],
            'painOptions' => [
                'payroll' => 'Payroll and salary inputs',
                'performance' => 'Performance reviews and manager support',
                'policy' => 'Policies, SOPs, and handbooks',
                'compliance' => 'Compliance and documentation',
                'recruitment' => 'Hiring and onboarding',
                'reporting' => 'Reporting and leadership visibility',
            ],
            'slots' => [
                'Morning (9:00 to 12:00)',
                'Afternoon (12:00 to 16:00)',
                'Evening (16:00 to 19:00)',
            ],
        ]);
    }
}
