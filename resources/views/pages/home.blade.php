@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@section('content')
    @php
        $serviceCount = count(config('emplowise.services'));
    @endphp

    <section class="hero">
        <div class="hero-mesh" aria-hidden="true">
            <span class="orb orb--teal"></span>
            <span class="orb orb--cyan"></span>
            <span class="orb orb--indigo"></span>
            <span class="mesh-grid"></span>
            <span class="mesh-noise"></span>
        </div>

        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="kicker">{{ $copy['kicker'] }}</p>
                @include('partials.seo-h1', ['seo' => $seo])
                <p class="hero-sub">
                    {{ $copy['hero_sub'] }}
                </p>

                <p class="hero-rotate">
                    <span>{{ $copy['rotator_prefix'] }}</span>
                    <span class="rotator" id="hero-rotator" aria-live="off"><b>payroll inputs</b></span>
                </p>

                <div class="hero-actions">
                    <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">
                        {{ $copy['cta_primary'] }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="{{ route('student-hub') }}" class="btn btn--outline-light">{{ $copy['cta_secondary'] }}</a>
                </div>

                <dl class="hero-proof">
                    @foreach ($copy['proof'] as $proof)
                        <div>
                            <dt>{{ $proof['label'] }}</dt>
                            <dd>{{ $proof['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                <a class="scroll-cue" href="#pillars"><i aria-hidden="true"></i> {{ $copy['scroll_cue'] }}</a>
            </div>

            <aside class="hero-panel" id="hero-panel" aria-label="{{ $copy['panel']['aria'] }}">
                <span class="hero-float hero-float--a">{{ $copy['panel']['float_a'] }}</span>
                <span class="hero-float hero-float--b">{{ $copy['panel']['float_b'] }}</span>

                <div class="hero-panel-head">
                    <p class="hero-panel-title">{{ $copy['panel']['title'] }}</p>
                    <span class="live-dot">{{ $copy['panel']['live'] }}</span>
                </div>

                <div class="stack-rows">
                    <div class="stack-row stack-row--payroll">
                        <span class="stack-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>
                        </span>
                        <span class="stack-body">
                            <span class="stack-label">{{ $copy['panel']['rows'][0]['label'] }}</span>
                            <span class="stack-count">{{ count($pillars[0]['services']) }} {{ $copy['panel']['services_suffix'] }}</span>
                        </span>
                        <span class="stack-state">{{ $copy['panel']['rows'][0]['state'] }}</span>
                    </div>

                    <div class="stack-row stack-row--performance">
                        <span class="stack-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
                        </span>
                        <span class="stack-body">
                            <span class="stack-label">{{ $copy['panel']['rows'][1]['label'] }}</span>
                            <span class="stack-count">{{ count($pillars[1]['services']) }} {{ $copy['panel']['services_suffix'] }}</span>
                        </span>
                        <span class="stack-state">{{ $copy['panel']['rows'][1]['state'] }}</span>
                    </div>

                    <div class="stack-row stack-row--policy">
                        <span class="stack-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 15l2 2 4-4"/></svg>
                        </span>
                        <span class="stack-body">
                            <span class="stack-label">{{ $copy['panel']['rows'][2]['label'] }}</span>
                            <span class="stack-count">{{ count($pillars[2]['services']) }} {{ $copy['panel']['services_suffix'] }}</span>
                        </span>
                        <span class="stack-state">{{ $copy['panel']['rows'][2]['state'] }}</span>
                    </div>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat"><span>{{ $copy['panel']['stats']['services'] }}</span><b data-count="{{ $serviceCount }}">{{ $serviceCount }}</b></div>
                    <div class="hero-stat"><span>{{ $copy['panel']['stats']['stages'] }}</span><b data-count="{{ count($methodology) }}">{{ count($methodology) }}</b></div>
                    <div class="hero-stat"><span>{{ $copy['panel']['stats']['tools'] }}</span><b data-count="5">5</b></div>
                </div>
            </aside>
        </div>
    </section>

    <div class="strip">
        <div class="shell strip-inner">
            <span class="strip-label">{{ $copy['strip_label'] }}</span>
            <div class="marquee" id="service-marquee">
                <div class="marquee-track">
                    <div class="strip-items">
                        @foreach (config('emplowise.services') as $service)
                            <span>{{ $service['name'] }}</span>
                        @endforeach
                    </div>
                    <div class="strip-items" aria-hidden="true">
                        @foreach (config('emplowise.services') as $service)
                            <span>{{ $service['name'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section" id="pillars">
        <div class="shell">
            <div class="section-head section-head--split reveal">
                <div>
                    <p class="kicker">{{ $copy['pillars']['kicker'] }}</p>
                    <h2>{{ $seo['h2'] }}</h2>
                </div>
                <p class="lede">
                    {{ $copy['pillars']['lede'] }}
                </p>
            </div>

            <div class="pillar-grid">
                @foreach ($pillars as $index => $pillar)
                    <article class="pillar pillar--{{ $pillar['slug'] }} reveal" data-delay="{{ $index + 1 }}">
                        <a class="pillar-head pillar-head--link" href="{{ route('what-we-do') }}#{{ $pillar['slug'] }}">
                            <span class="pillar-top">
                                <span class="pillar-badge">
                                    <span class="pillar-icon" aria-hidden="true">
                                        @if ($pillar['slug'] === 'payroll')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>
                                        @elseif ($pillar['slug'] === 'performance')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 15l2 2 4-4"/></svg>
                                        @endif
                                    </span>
                                    <span class="pillar-num">{{ $pillar['number'] }}</span>
                                </span>
                                <span class="pillar-arrow" aria-hidden="true">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </span>
                            </span>
                            <h3>{{ $pillar['name'] }}</h3>
                            <span class="pillar-kicker">{{ $pillar['kicker'] }}</span>
                            <span class="pillar-summary">{{ $pillar['summary'] }}</span>
                            <span class="pillar-card-link">{{ $copy['pillars']['card_link'] }}</span>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--alt">
        <div class="shell">
            <div class="section-head reveal">
                <p class="kicker">{{ $copy['method']['kicker'] }}</p>
                <h2>{{ $copy['method']['heading'] }}</h2>
                <p class="lede">
                    {{ $copy['method']['lede'] }}
                </p>
            </div>

            <div class="method-grid">
                @foreach ($methodology as $index => $step)
                    <div class="method-step reveal" data-delay="{{ $index + 1 }}">
                        <span class="method-num">{{ $step['step'] }}</span>
                        <h3>{{ $step['name'] }}</h3>
                        <p>{{ $step['summary'] }}</p>
                        <span class="method-output">{{ $step['output'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="section-head section-head--split reveal">
                <div>
                    <p class="kicker">{{ $copy['pricing']['kicker'] }}</p>
                    <h2>{{ $copy['pricing']['heading'] }}</h2>
                </div>
                <p class="lede">
                    {{ $copy['pricing']['lede'] }}
                </p>
            </div>

            <div class="price-grid price-grid--compact">
                @foreach ($packages as $index => $package)
                    <a href="{{ route('pricing') }}" class="price-card price-card--compact {{ $package['featured'] ? 'price-card--featured' : '' }} reveal" data-delay="{{ $index + 1 }}">
                        @if ($package['featured'])
                            <span class="price-tag">{{ $copy['pricing']['tag'] }}</span>
                        @endif
                        <span class="price-name">{{ $package['name'] }}</span>
                        <span class="price-amount">{{ $package['price_display'] }}</span>
                        <span class="price-period">{{ $package['period'] }}</span>
                        <span class="price-capacity">{{ $package['capacity'] }}</span>
                        <span class="price-card-link">
                            {{ $copy['pricing']['link'] }}
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </span>
                    </a>
                @endforeach
            </div>

            <p class="small muted s1">
                {{ $copy['pricing']['note'] }}
            </p>
        </div>
    </section>

    <section class="section section--navy">
        <div class="shell hub-teaser hub-teaser--solo">
            <div class="reveal">
                <p class="kicker">{{ $copy['student']['kicker'] }}</p>
                <h2>{{ $copy['student']['heading'] }}</h2>
                <p class="lede">
                    {{ $copy['student']['lede'] }}
                </p>
                <div class="hero-actions s2">
                    <a href="{{ route('student-hub') }}#prompts" class="btn btn--student">{{ str_replace(':count', $promptCount, $copy['student']['cta_prompts']) }}</a>
                    <a href="{{ route('student-hub') }}#ats" class="btn btn--outline-light">{{ $copy['student']['cta_ats'] }}</a>
                </div>
            </div>

        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>{{ $copy['cta_band']['heading'] }}</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">{{ $copy['cta_band']['primary'] }}</a>
                <a href="{{ route('what-we-do') }}" class="btn btn--outline-light">{{ $copy['cta_band']['secondary'] }}</a>
            </div>
        </div>
    </section>
@endsection
