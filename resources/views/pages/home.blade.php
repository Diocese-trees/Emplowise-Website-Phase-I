@extends('layouts.app')

@section('title', 'Emplowise | Remote HR Solutions for Growing Businesses')
@section('meta_description', 'Remote HR for startups and SMEs in Pakistan. Payroll, performance management, and policy making with a proposed AI automation layer, plus a free student career toolkit.')

@section('content')
    @php
        $serviceCount = count(config('emplowise.services'));
        $faqCount = collect(config('emplowise.faqs'))->sum(fn ($group) => count($group['items']));
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
                <p class="kicker">Remote HR, built on structure</p>
                <h1>Remote HR solutions for <em>growing businesses</em>.</h1>
                <p class="hero-sub">
                    Emplowise runs the people operations behind your team, remote, so founders keep momentum and
                    employees get clarity. Payroll, performance, and policy on one operating rhythm.
                </p>

                <p class="hero-rotate">
                    <span>Currently running</span>
                    <span class="rotator" id="hero-rotator" aria-live="off"><b>payroll inputs</b></span>
                </p>

                <div class="hero-actions">
                    <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">
                        Book a free HR audit
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="{{ route('student-hub') }}" class="btn btn--outline-light">Open the Student Hub</a>
                </div>

                <dl class="hero-proof">
                    <div>
                        <dt>Delivery</dt>
                        <dd>Remote HR team with specialist consultant backup.</dd>
                    </div>
                    <div>
                        <dt>Focus</dt>
                        <dd>Payroll, performance management, and policy making.</dd>
                    </div>
                    <div>
                        <dt>First step</dt>
                        <dd>A free diagnostic audit and a prioritised gap list.</dd>
                    </div>
                </dl>

                <a class="scroll-cue" href="#pillars"><i aria-hidden="true"></i> Scroll to the operating model</a>
            </div>

            <aside class="hero-panel" id="hero-panel" aria-label="Emplowise operating stack">
                <span class="hero-float hero-float--a">Remote delivery</span>
                <span class="hero-float hero-float--b">Free student tools</span>

                <div class="hero-panel-head">
                    <p class="hero-panel-title">The operating stack</p>
                    <span class="live-dot">One calendar</span>
                </div>

                <div class="stack-rows">
                    <div class="stack-row stack-row--payroll">
                        <span class="stack-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>
                        </span>
                        <span class="stack-body">
                            <span class="stack-label">Payroll inputs, leave, records</span>
                            <span class="stack-bar"><i data-fill="86"></i></span>
                        </span>
                        <span class="stack-state">One source</span>
                    </div>

                    <div class="stack-row stack-row--performance">
                        <span class="stack-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
                        </span>
                        <span class="stack-body">
                            <span class="stack-label">Review cycles and manager prompts</span>
                            <span class="stack-bar"><i data-fill="72"></i></span>
                        </span>
                        <span class="stack-state">One cadence</span>
                    </div>

                    <div class="stack-row stack-row--policy">
                        <span class="stack-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 15l2 2 4-4"/></svg>
                        </span>
                        <span class="stack-body">
                            <span class="stack-label">Policies with owners and versions</span>
                            <span class="stack-bar"><i data-fill="64"></i></span>
                        </span>
                        <span class="stack-state">One register</span>
                    </div>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat"><span>Confirmed services in scope</span><b data-count="{{ $serviceCount }}">{{ $serviceCount }}</b></div>
                    <div class="hero-stat"><span>Method stages per engagement</span><b data-count="{{ count($methodology) }}">{{ count($methodology) }}</b></div>
                    <div class="hero-stat"><span>Free tools in the Student Hub</span><b data-count="5">5</b></div>
                </div>
            </aside>
        </div>
    </section>

    <div class="strip">
        <div class="shell strip-inner">
            <span class="strip-label">Confirmed services</span>
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
                    <p class="kicker">What we run</p>
                    <h2>Three pillars. One operating rhythm.</h2>
                </div>
                <p class="lede">
                    Open a pillar to see the confirmed services behind it and the automation layer we propose to
                    remove manual chase work.
                </p>
            </div>

            <div class="pillar-grid">
                @foreach ($pillars as $index => $pillar)
                    <article class="pillar pillar--{{ $pillar['slug'] }} reveal" data-delay="{{ $index + 1 }}" data-pillar>
                        <button type="button" class="pillar-head" aria-expanded="false" aria-controls="pillar-{{ $pillar['slug'] }}">
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
                                <span class="pillar-arrow" aria-hidden="true">+</span>
                            </span>
                            <h3>{{ $pillar['name'] }}</h3>
                            <p class="pillar-kicker">{{ $pillar['kicker'] }}</p>
                            <p class="pillar-summary">{{ $pillar['summary'] }}</p>
                        </button>

                        <div class="pillar-detail" id="pillar-{{ $pillar['slug'] }}">
                            <h4>AI automation · proposed</h4>
                            <ul class="pillar-list">
                                @foreach ($pillar['automation'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>

                            <h4>Confirmed services</h4>
                            <ul class="pillar-list">
                                @foreach ($pillar['services'] as $service)
                                    <li>{{ $service }}</li>
                                @endforeach
                            </ul>

                            <div class="pillar-foot">
                                <span class="badge badge--pending">{{ $pillar['status'] }}</span>
                                <a href="{{ route('what-we-do') }}#{{ $pillar['slug'] }}" class="link-arrow">How it runs</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--alt">
        <div class="shell">
            <div class="section-head reveal">
                <p class="kicker">The method</p>
                <h2>Assess. Identify. Improve. Review.</h2>
                <p class="lede">
                    The same four-stage method runs every engagement, from a first audit conversation to the
                    monthly review pack leadership actually reads.
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
                    <p class="kicker">Monthly retainers</p>
                    <h2>Pricing by headcount, not by guesswork.</h2>
                </div>
                <p class="lede">
                    Three confirmed tiers from the 2026 business brief. Package inclusions are deliberately marked
                    Pending Input and agreed in the audit conversation.
                </p>
            </div>

            <div class="price-grid">
                @foreach ($packages as $index => $package)
                    <article class="price-card {{ $package['featured'] ? 'price-card--featured' : '' }} reveal" data-delay="{{ $index + 1 }}">
                        @if ($package['featured'])
                            <span class="price-tag">Core tier</span>
                        @endif
                        <p class="price-name">{{ $package['name'] }}</p>
                        <p class="price-amount">{{ $package['price_display'] }}</p>
                        <p class="price-period">{{ $package['period'] }}</p>
                        <span class="price-capacity">{{ $package['capacity'] }}</span>
                        <ul class="price-list">
                            <li>{{ $package['summary'] }}</li>
                            <li>{{ $package['best_for'] }}</li>
                            <li>Inclusions: Pending Input.</li>
                        </ul>
                        <a href="{{ route('pricing') }}" class="btn {{ $package['featured'] ? 'btn--light' : 'btn--ghost' }} btn--sm">View the full breakdown</a>
                    </article>
                @endforeach
            </div>

            <p class="small muted s1">
                Prices are monthly retainers in PKR. Recruitment fees, payroll tax scope, and legal advice
                qualifications are Pending Input and confirmed in consultation.
            </p>
        </div>
    </section>

    <section class="section section--navy">
        <div class="shell hub-teaser">
            <div class="reveal">
                <p class="kicker">Free student resources</p>
                <h2>Same structure. Zero cost. Built for emerging talent.</h2>
                <p class="lede">
                    Five working tools for students and early career candidates. No account, no upsell, no
                    invented feedback. Powered by Alibaba Cloud AI with a local fallback that always works.
                </p>
                <div class="hero-actions s2">
                    <a href="{{ route('student-hub') }}#prompts" class="btn btn--student">Explore the {{ $promptCount }} prompts</a>
                    <a href="{{ route('student-hub') }}#ats" class="btn btn--outline-light">Score your resume</a>
                </div>
            </div>

            <div class="hub-tools reveal" data-delay="2" aria-label="Five free student tools">
                <a class="hub-tool" href="{{ route('student-hub') }}#interview">
                    <span>
                        <i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></i>
                        Mock interview simulator
                    </span>
                    <b>Free</b>
                </a>
                <a class="hub-tool" href="{{ route('student-hub') }}#ats">
                    <span>
                        <i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></i>
                        ATS resume and JD analyzer
                    </span>
                    <b>Free</b>
                </a>
                <a class="hub-tool" href="{{ route('student-hub') }}#resume">
                    <span>
                        <i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></i>
                        ATS resume builder
                    </span>
                    <b>Free</b>
                </a>
                <a class="hub-tool" href="{{ route('student-hub') }}#profile">
                    <span>
                        <i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></i>
                        Profile optimizer
                    </span>
                    <b>Free</b>
                </a>
                <a class="hub-tool" href="{{ route('student-hub') }}#prompts">
                    <span>
                        <i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></i>
                        Prompt library, {{ $promptCount }} prompts
                    </span>
                    <b>Free</b>
                </a>
            </div>
        </div>
    </section>

    <section class="section section--tight section--ice">
        <div class="shell">
            <div class="stat-row reveal">
                <div class="stat stat--light"><b data-count="{{ $serviceCount }}">{{ $serviceCount }}</b><span>Confirmed services</span></div>
                <div class="stat stat--light"><b data-count="{{ count($pillars) }}">{{ count($pillars) }}</b><span>Operating pillars</span></div>
                <div class="stat stat--light"><b data-count="{{ $faqCount }}">{{ $faqCount }}</b><span>Answered questions</span></div>
                <div class="stat stat--light"><b data-count="{{ $promptCount }}">{{ $promptCount }}</b><span>Free career prompts</span></div>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>Start with a free HR audit. Leave with a gap list.</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">Book the free audit</a>
                <a href="{{ route('what-we-do') }}" class="btn btn--outline-light">See how we work</a>
            </div>
        </div>
    </section>
@endsection
