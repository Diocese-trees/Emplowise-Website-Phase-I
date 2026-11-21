@extends('layouts.app')

@section('title', 'What We Are | Emplowise')
@section('meta_description', 'Emplowise is a remote HR service for startups, SMEs, and emerging talent in Pakistan. Clarity, structure, substance, and trust, built for growing teams.')

@section('content')
    <section class="page-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <span>What We Are</span>
            </nav>
            <p class="kicker">{{ $brand['essence'] }}</p>
            <h1>An HR team that sits between informal help and enterprise consultancy.</h1>
            <p class="lede">
                Emplowise is remote HR support for growing Pakistani businesses. We install the structure
                behind people operations, then run it, so founders stop being the HR helpdesk.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell split">
            <div>
                <p class="kicker">Why we exist</p>
                <h2>Empower people. Simplify work. Build momentum.</h2>
                <p class="lede">{{ $mission['body'] }}</p>
                <p class="small muted s23">
                    Category: {{ $brand['category'] }}. Market: {{ $brand['market'] }}.
                    {{ $brand['expansion'] }}.
                </p>
            </div>
            <div>
                <blockquote class="pull-quote">
                    {{ $brand['essence'] }}
                    <cite>The Emplowise brand essence, 2026 Brand Kit</cite>
                </blockquote>
                <p class="small s29">
                    {{ $mission['origin'] }}
                </p>
            </div>
        </div>
    </section>

    <section class="section section--alt">
        <div class="shell">
            <div class="section-head">
                <p class="kicker">Core values</p>
                <h2>Four rules that decide how we work.</h2>
                <p class="lede">
                    These are operating commitments, not wall posters. Each one changes what we deliver and what
                    we refuse to promise.
                </p>
            </div>

            <div class="grid-4">
                @foreach ($values as $index => $value)
                    <article class="value-card">
                        <span class="value-index">0{{ $index + 1 }}</span>
                        <h3>{{ $value['title'] }}</h3>
                        <p>{{ $value['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="section-head">
                <p class="kicker">Who we sit with</p>
                <h2>Three audiences. One standard of clarity.</h2>
                <p class="lede">
                    Startups, SMEs, and emerging talent get the same structured thinking. Two are commercial
                    engagements. The third is free, on purpose.
                </p>
            </div>

            <div class="grid-3">
                @foreach ($positioning as $key => $position)
                    <article class="pos-card">
                        <span class="pos-icon" aria-hidden="true">
                            {{ $key === 'startups' ? 'S' : ($key === 'smes' ? 'M' : 'T') }}
                        </span>
                        <h3>{{ $position['title'] }}</h3>
                        <p>{{ $position['text'] }}</p>
                        @if ($key === 'talent')
                            <a href="{{ route('student-hub') }}" class="link-arrow s8">Open the Student Hub</a>
                        @else
                            <a href="{{ route('pricing') }}" class="link-arrow s8">See the packages</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--ice">
        <div class="shell split">
            <div>
                <p class="kicker">How we deliver</p>
                <h2>A remote model with named people and specialist backup.</h2>
                <p class="lede">
                    Delivery is remote. A dedicated HR representative gives the client CEO or founder direct
                    access, backed by specialist HR consultants on defined problems.
                </p>
            </div>
            <div>
                <ul class="index-list">
                    @foreach ($brand['delivery'] as $line)
                        <li><span>{{ $line }}</span></li>
                    @endforeach
                </ul>
                <p class="small muted s8">
                    Full time versus shared allocation of the dedicated representative is confirmed in writing after your Free HR Audit.
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="card card--flat">
                <div class="panel-head s30">
                    <div>
                        <p class="kicker s24">Open items</p>
                        <h2 class="s31">What we have not published yet</h2>
                    </div>
                    <span class="badge badge--pending">Confirmed after audit</span>
                </div>
                <p class="small muted s30">
                    These details are unresolved in the source material. We list them rather than fill the gaps
                    with invented facts.
                </p>
                <ul class="checklist">
                    @foreach ($pending as $item)
                        <li class="is-fail">{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>Bring us your current HR reality.</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">Book the free audit</a>
                <a href="{{ route('what-we-do') }}" class="btn btn--outline-light">What we do</a>
            </div>
        </div>
    </section>
@endsection
