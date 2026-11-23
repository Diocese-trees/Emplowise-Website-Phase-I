<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

final class SiteController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'seo' => config('emplowise.seo.home'),
            'copy' => config('emplowise.copy.home'),
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
            'seo' => config('emplowise.seo.what-we-are'),
            'copy' => config('emplowise.copy.what-we-are'),
            'brand' => config('emplowise.brand'),
            'mission' => config('emplowise.mission'),
            'values' => config('emplowise.values'),
            'positioning' => config('emplowise.positioning'),
            'pending' => config('emplowise.pending_inputs'),
            'team' => config('emplowise.copy.team'),
            'teamSeo' => config('emplowise.seo.team'),
        ]);
    }

    public function whatWeDo(): View
    {
        $services = collect(config('emplowise.services'))->groupBy('pillar');

        return view('pages.what-we-do', [
            'seo' => config('emplowise.seo.what-we-do'),
            'copy' => config('emplowise.copy.what-we-do'),
            'pillars' => config('emplowise.pillars'),
            'servicesByPillar' => $services,
            'methodology' => config('emplowise.methodology'),
            'outcomes' => config('emplowise.outcomes'),
            'outcomesNote' => config('emplowise.outcomes_note'),
        ]);
    }

    public function whyEmplowise(): View
    {
        return view('pages.why-emplowise', [
            'seo' => config('emplowise.seo.why-emplowise'),
            'copy' => config('emplowise.copy.why-emplowise'),
            'packages' => config('emplowise.packages'),
            'notes' => config('emplowise.pricing_notes'),
            'services' => config('emplowise.services'),
        ]);
    }

    public function emploStart(): View
    {
        return view('pages.emplostart', [
            'seo' => config('emplowise.seo.emplostart'),
            'copy' => config('emplowise.copy.emplostart'),
            'student' => config('emplowise.student'),
            'brand' => config('emplowise.brand'),
        ]);
    }

    public function faq(): View
    {
        $groups = config('emplowise.faqs');

        return view('pages.faq', [
            'seo' => config('emplowise.seo.faq'),
            'copy' => config('emplowise.copy.faq'),
            'groups' => $groups,
            'total' => collect($groups)->sum(fn (array $group): int => count($group['items'])),
            'note' => config('emplowise.faq_note'),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact', [
            'seo' => config('emplowise.seo.contact'),
            'copy' => config('emplowise.copy.contact'),
        ]);
    }

    public function whatWeAreBuilding(): View
    {
        return view('pages.what-we-are-building', [
            'seo' => config('emplowise.seo.what-we-are-building'),
            'copy' => config('emplowise.copy.what-we-are-building'),
        ]);
    }

    public function terms(): View
    {
        return view('pages.legal', [
            'seo' => config('emplowise.seo.terms'),
            'copy' => config('emplowise.copy.legal.terms'),
            'kind' => 'terms',
        ]);
    }

    public function privacy(): View
    {
        return view('pages.legal', [
            'seo' => config('emplowise.seo.privacy'),
            'copy' => config('emplowise.copy.legal.privacy'),
            'kind' => 'privacy',
        ]);
    }

    public function companyProfile(): View
    {
        return view('pages.company-profile', [
            'seo' => config('emplowise.seo.company-profile'),
            'page' => config('emplowise.showcase_pages.company-profile'),
            'brand' => config('emplowise.brand'),
            'packages' => config('emplowise.packages'),
            'notes' => config('emplowise.pricing_notes'),
            'services' => config('emplowise.services'),
            'pillars' => config('emplowise.pillars'),
            'methodology' => config('emplowise.methodology'),
            'team' => config('emplowise.copy.team'),
        ]);
    }

    public function portfolio(): View
    {
        return view('pages.portfolio', [
            'seo' => config('emplowise.seo.portfolio'),
            'page' => config('emplowise.showcase_pages.portfolio'),
            'brand' => config('emplowise.brand'),
            'pillars' => config('emplowise.pillars'),
            'methodology' => config('emplowise.methodology'),
        ]);
    }

    public function pitchDeck(): View
    {
        return view('pages.pitch-deck', [
            'seo' => config('emplowise.seo.pitch-deck'),
            'page' => config('emplowise.showcase_pages.pitch-deck'),
            'brand' => config('emplowise.brand'),
            'packages' => config('emplowise.packages'),
            'methodology' => config('emplowise.methodology'),
        ]);
    }

    public function audit(): View
    {
        return view('pages.audit', [
            'seo' => config('emplowise.seo.free-hr-audit'),
            'copy' => config('emplowise.copy.free-hr-audit'),
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
