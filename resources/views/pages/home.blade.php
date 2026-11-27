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
                @php($h1Text = $seo['h1'] ?? '')
                @php($h1Accent = $seo['h1_accent'] ?? null)
                <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
                <p class="hero-lead">
                    {{ $seo['intro'] }}
                </p>

                {{-- The support paragraph collapses on phones so the two calls
                     to action stay inside the first screen. It renders open,
                     so a browser without JavaScript still shows the copy; the
                     header script closes it below 641px and leaves the summary
                     line as the way back in. --}}
                <details class="hero-more" data-hero-more open>
                    <summary>{{ $copy['hero_more'] }}</summary>
                    <p class="hero-sub">
                        {{ $copy['hero_sub'] }}
                    </p>
                </details>

                <p class="hero-rotate">
                    <span>{{ $copy['rotator_prefix'] }}</span>
                    <span class="rotator" id="hero-rotator" aria-live="off"><b>payroll inputs</b></span>
                </p>

                <div class="hero-actions">
                    <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">
                        {{ $copy['cta_primary'] }}
                        <x-icon name="arrow-right" :size="16" />
                    </a>
                    <a href="{{ route('emplostart') }}" class="btn btn--outline-light">{{ $copy['cta_secondary'] }}</a>
                </div>

                <dl class="hero-proof">
                    @foreach ($copy['proof'] as $proof)
                        <div>
                            <dt>{{ $proof['label'] }}</dt>
                            <dd>{{ $proof['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                <a class="scroll-cue" href="#shortcuts"><i aria-hidden="true"></i> {{ $copy['scroll_cue'] }}</a>
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
                            <x-icon name="card" :size="18" />
                        </span>
                        <span class="stack-body">
                            <span class="stack-label">{{ $copy['panel']['rows'][0]['label'] }}</span>
                            <span class="stack-count">{{ count($pillars[0]['services']) }} {{ $copy['panel']['services_suffix'] }}</span>
                        </span>
                        <span class="stack-state">{{ $copy['panel']['rows'][0]['state'] }}</span>
                    </div>

                    <div class="stack-row stack-row--performance">
                        <span class="stack-icon" aria-hidden="true">
                            <x-icon name="chart-line" :size="18" />
                        </span>
                        <span class="stack-body">
                            <span class="stack-label">{{ $copy['panel']['rows'][1]['label'] }}</span>
                            <span class="stack-count">{{ count($pillars[1]['services']) }} {{ $copy['panel']['services_suffix'] }}</span>
                        </span>
                        <span class="stack-state">{{ $copy['panel']['rows'][1]['state'] }}</span>
                    </div>

                    <div class="stack-row stack-row--policy">
                        <span class="stack-icon" aria-hidden="true">
                            <x-icon name="file-check" :size="18" />
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
                    <div class="hero-stat"><span>{{ $copy['panel']['stats']['tools'] }}</span><b data-count="6">6</b></div>
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

    <section class="section section--tight home-shortcuts" id="shortcuts">
        <div class="shell">
            <div class="section-head section-head--split reveal">
                <div>
                    <p class="kicker">{{ $copy['shortcuts']['kicker'] }}</p>
                    <h2>{{ $copy['shortcuts']['heading'] }}</h2>
                </div>
                <p class="lede">{{ $copy['shortcuts']['lede'] }}</p>
            </div>

            <div class="shortcut-grid">
                @foreach ($copy['shortcuts']['items'] as $index => $item)
                    <a class="shortcut reveal" href="{{ route($item['route']) }}{{ isset($item['hash']) ? '#'.$item['hash'] : '' }}" data-delay="{{ $index + 1 }}">
                        <span class="shortcut-icon" aria-hidden="true">
                            <x-icon :name="$item['icon']" :size="20" />
                        </span>
                        <span class="shortcut-copy">
                            <span class="shortcut-label">{{ $item['label'] }}</span>
                            <span class="shortcut-desc">{{ $item['desc'] }}</span>
                        </span>
                        <span class="shortcut-arrow" aria-hidden="true">
                            <x-icon name="arrow-right" :size="16" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

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
                                            <x-icon name="card" :size="22" />
                                        @elseif ($pillar['slug'] === 'performance')
                                            <x-icon name="chart-line" :size="22" />
                                        @else
                                            <x-icon name="file-check" :size="22" />
                                        @endif
                                    </span>
                                    <span class="pillar-num">{{ $pillar['number'] }}</span>
                                </span>
                                <span class="pillar-arrow" aria-hidden="true">
                                    <x-icon name="arrow-right" :size="16" />
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

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>{{ $copy['cta_band']['heading'] }}</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">{{ $copy['cta_band']['primary'] }}</a>
                <a href="{{ route('why-emplowise') }}" class="btn btn--outline-light">{{ $copy['cta_band']['secondary'] }}</a>
            </div>
        </div>
    </section>

@push('scripts')
    <script src="{{ asset_v('js/home.js') }}" defer></script>
@endpush
@endsection
