@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@section('content')
    @php
        // Mirrors the EMPLOWISE x START library shipped in the standalone
        // EmploStart app. Kept inline in this view, and above the sections, so
        // the launcher stays a plain link out: it loads no app code and no app
        // data files.
        $promptLibrary = [
            'EMPLOWISE' => [
                ['id' => 'P01', 'letter' => 'E', 'title' => 'Evidence & Career Inventory'],
                ['id' => 'P02', 'letter' => 'M', 'title' => 'Market Value Assessment'],
                ['id' => 'P03', 'letter' => 'P', 'title' => 'Positioning & JD Semantic Decoder'],
                ['id' => 'P04', 'letter' => 'L', 'title' => 'LinkedIn & Digital Profile Architect'],
                ['id' => 'P05', 'letter' => 'O', 'title' => 'Optimized Human & ATS Resume Strategist'],
                ['id' => 'P06', 'letter' => 'W', 'title' => 'Work Proof & Portfolio Case Studies'],
                ['id' => 'P07', 'letter' => 'I', 'title' => 'Instant Parser-Safe ATS Export'],
                ['id' => 'P08', 'letter' => 'S', 'title' => 'Strategic Outreach, Referrals & Cover Letters'],
                ['id' => 'P09', 'letter' => 'E', 'title' => 'Error & Rejection Diagnostics'],
            ],
            'START' => [
                ['id' => 'P10', 'letter' => 'S', 'title' => 'Structured Interview & Claim Defensibility'],
                ['id' => 'P11', 'letter' => 'T', 'title' => 'Take-Home, Case & 30-60-90 Day Pitch'],
                ['id' => 'P12', 'letter' => 'A', 'title' => 'Ask & Fair-Wage Offer Negotiation'],
                ['id' => 'P13', 'letter' => 'R', 'title' => 'Reskilling, Credentials & Scholarship ROI'],
                ['id' => 'P14', 'letter' => 'T', 'title' => 'Trajectory: Career Pivot, Reimagination & Venture'],
            ],
        ];
    @endphp

    <section class="page-head launcher-head">
        <div class="shell">
            <p class="kicker">{{ $copy['kicker'] }}</p>
            @php($h1Text = $seo['h1'] ?? '')
            @php($h1Accent = $seo['h1_accent'] ?? null)
            <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
            <p class="lede">{{ $seo['intro'] }}</p>

            <div class="launcher-actions">
                <a href="{{ $emplostartUrl }}" class="btn btn--primary" target="_blank" rel="noopener">
                    {{ $copy['launch_cta'] }}
                    <x-icon name="arrow-right" :size="16" />
                </a>
                <a href="#modules" class="btn btn--ghost">{{ $copy['launch_secondary'] }}</a>
            </div>
            <p class="launcher-note small muted">{{ $copy['launch_note'] }}</p>

            <div class="launcher-stats">
                <div class="launcher-stat"><b>{{ count($modules) }}</b><span>{{ $copy['stats'][0] }}</span></div>
                <div class="launcher-stat"><b>0</b><span>{{ $copy['stats'][1] }}</span></div>
                <div class="launcher-stat"><b>0</b><span>{{ $copy['stats'][2] }}</span></div>
                <div class="launcher-stat"><b>Off</b><span>{{ $copy['stats'][3] }}</span></div>
            </div>
        </div>
    </section>

    <section class="section section--tight" id="modules">
        <div class="shell">
            <div class="section-head section-head--split">
                <div>
                    <p class="kicker">{{ $copy['modules_kicker'] }}</p>
                    <h2>{{ $seo['h2'] }}</h2>
                </div>
                <p class="lede">{{ $copy['modules_lede'] }}</p>
            </div>

            <ol class="launcher-modules">
                @foreach ($modules as $module)
                    <li class="card launcher-module">
                        <span class="launcher-module-num">{{ $module['num'] }}</span>
                        <h3>{{ $module['title'] }}</h3>
                        <p>{{ $module['desc'] }}</p>
                        <a class="launcher-module-link" href="{{ $emplostartUrl }}" target="_blank" rel="noopener">
                            {{ $copy['modules_live'] }}
                            <x-icon name="arrow-right" :size="14" />
                        </a>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="section" id="prompt-letters" aria-labelledby="prompt-letters-heading">
        <div class="shell">
            <div class="section-head section-head--split">
                <div>
                    <p class="kicker">{{ $copy['library_kicker'] }}</p>
                    <h2 id="prompt-letters-heading">{{ $copy['library_heading'] }}</h2>
                </div>
                <p class="lede">{{ $copy['library_lede'] }}</p>
            </div>

            <div class="launcher-library">
                @foreach ($copy['library_tracks'] as $track)
                    <article class="launcher-track">
                        <p class="launcher-track-range">{{ $track['range'] }}</p>
                        <h3>{{ $track['name'] }}</h3>
                        <p class="launcher-track-promise">{{ $track['promise'] }}</p>

                        <ol class="launcher-prompt-list">
                            @foreach ($promptLibrary[$track['name']] as $prompt)
                                <li class="launcher-prompt">
                                    <span class="launcher-prompt-id">{{ $prompt['id'] }}</span>
                                    <span class="launcher-prompt-letter"><span class="sr-only">Letter</span>{{ $prompt['letter'] }}</span>
                                    <span class="launcher-prompt-title">{{ $prompt['title'] }}</span>
                                </li>
                            @endforeach
                        </ol>

                        <a class="launcher-track-link" href="{{ $emplostartUrl }}#library" target="_blank" rel="noopener">
                            {{ $copy['library_link'] }}
                            <x-icon name="arrow-right" :size="14" />
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--tight" id="intake" aria-labelledby="intake-heading">
        <div class="shell">
            <div class="section-head section-head--split">
                <div>
                    <p class="kicker">{{ $copy['intake_kicker'] }}</p>
                    <h2 id="intake-heading">{{ $copy['intake_heading'] }}</h2>
                </div>
                <p class="lede">{{ $copy['intake_lede'] }}</p>
            </div>

            <ol class="launcher-lanes">
                @foreach ($copy['intake_lanes'] as $index => $lane)
                    <li class="launcher-lane">
                        <span class="launcher-lane-num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $lane['title'] }}</h3>
                        <p>{{ $lane['desc'] }}</p>
                        <p class="launcher-lane-detail">{{ $lane['detail'] }}</p>
                    </li>
                @endforeach
            </ol>

            <p class="launcher-note small muted">{{ $copy['intake_note'] }}</p>
        </div>
    </section>

    <section class="section section--ice" id="runs-where">
        <div class="shell">
            <div class="section-head section-head--split">
                <div>
                    <p class="kicker">{{ $copy['where_kicker'] }}</p>
                    <h2>{{ $copy['where_heading'] }}</h2>
                </div>
                <p class="lede">{{ $copy['where_lede'] }}</p>
            </div>

            <div class="split launcher-where">
                <article class="card launcher-where-card">
                    <h3>{{ $copy['where_local_heading'] }}</h3>
                    <ul class="launcher-list">
                        @foreach ($copy['where_local'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>

                <article class="card launcher-where-card">
                    <h3>{{ $copy['where_ai_heading'] }}</h3>
                    <ul class="launcher-list">
                        @foreach ($copy['where_ai'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            </div>

            <p class="launcher-note small muted">{{ $copy['where_note'] }}</p>
        </div>
    </section>

    <section class="pledge-band" id="pledge" aria-labelledby="pledge-heading">
        <div class="shell">
            <p class="kicker">{{ $pledge['kicker'] }}</p>
            <h2 id="pledge-heading">{{ $pledge['heading'] }}</h2>
            <p class="pledge-body">{{ $pledge['body'] }}</p>
            <ul class="pledge-points">
                @foreach ($pledge['points'] as $point)
                    <li><b>{{ $point['label'] }}</b><span>{{ $point['text'] }}</span></li>
                @endforeach
            </ul>
            <p class="pledge-note">{{ $pledge['disclaimer'] }}</p>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>{{ $copy['cta_band']['heading'] }}</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">{{ $copy['cta_band']['primary'] }}</a>
                <a href="{{ route('faq') }}" class="btn btn--outline-light">{{ $copy['cta_band']['secondary'] }}</a>
            </div>
        </div>
    </section>
@endsection
