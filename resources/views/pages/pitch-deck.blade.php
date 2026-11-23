@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@push('head')
    <meta name="robots" content="{{ $page['robots'] }}">
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset_v('css/showcase.css') }}">
@endpush

@section('content')
    @php($h1Text = $seo['h1'] ?? '')
    @php($h1Accent = $seo['h1_accent'] ?? null)
    @php($total = '08')

    <section class="page-head show-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ config('emplowise.copy.layout.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $page['kicker'] }}</span>
            </nav>
            <p class="kicker">{{ $page['kicker'] }}</p>
            <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
            <p class="lede">{{ $seo['intro'] }}</p>
            <div class="hero-actions show-download">
                <a class="btn btn--teal" href="{{ asset_v($page['download']['path']) }}" download>
                    {{ $page['download']['label'] }}
                </a>
                <a class="btn btn--ghost" href="{{ route('contact') }}">Talk to the team</a>
                <span class="show-download-meta">{{ $page['download']['meta'] }}</span>
            </div>
        </div>
    </section>

    <div class="deck" id="deck">
        <div class="deck-bar">
            <div class="shell shell--narrow deck-bar-inner" role="group" aria-label="Deck controls">
                <button class="deck-btn" type="button" id="deck-prev" aria-label="Previous slide">
                    <span aria-hidden="true">&larr;</span> Prev
                </button>
                <p class="deck-counter" id="deck-counter" aria-live="polite" aria-atomic="true">01 / {{ $total }}</p>
                <button class="deck-btn" type="button" id="deck-next" aria-label="Next slide">
                    Next <span aria-hidden="true">&rarr;</span>
                </button>
                <div class="deck-bar-actions">
                    <button class="deck-btn" type="button" id="deck-mode" aria-pressed="false">Present</button>
                    <a class="btn btn--teal btn--sm deck-dl" href="{{ asset_v($page['download']['path']) }}" download aria-label="Download the deck as a PowerPoint file">
                        <span class="deck-dl-full">{{ $page['download']['label'] }}</span>
                        <span class="deck-dl-short" aria-hidden="true">PPTX</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="shell shell--narrow">
            <ol class="deck-slides" id="deck-slides">
                <li class="deck-slide deck-slide--navy" id="slide-1">
                    <div class="deck-slide-head">
                        <img class="deck-logo deck-logo--primary" src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="125" height="32">
                        <img class="deck-logo deck-logo--reverse" src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="125" height="32">
                        <span class="deck-slide-count" aria-hidden="true">01 / {{ $total }}</span>
                    </div>
                    <h2>Remote HR structure for growing Pakistani businesses</h2>
                    <ul class="deck-blocks deck-blocks--3">
                        <li class="deck-block"><b>What</b><span>A remote HR team that installs and then runs payroll, performance and policy.</span></li>
                        <li class="deck-block"><b>Who</b><span>Startups and SMEs in Pakistan, five to one hundred employees, with no senior HR hire.</span></li>
                        <li class="deck-block"><b>Why</b><span>Published prices, written scope, one named team, and a free four-stage audit.</span></li>
                    </ul>
                </li>

                <li class="deck-slide deck-slide--white" id="slide-2">
                    <div class="deck-slide-head">
                        <img class="deck-logo deck-logo--primary" src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="125" height="32">
                        <img class="deck-logo deck-logo--reverse" src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="125" height="32">
                        <span class="deck-slide-count" aria-hidden="true">02 / {{ $total }}</span>
                    </div>
                    <h2>Informal HR breaks at thirty staff</h2>
                    <ul class="deck-blocks deck-blocks--2">
                        <li class="deck-block"><b>What fails</b><span>Contracts, payroll inputs and leave live in memory or a spreadsheet.</span></li>
                        <li class="deck-block"><b>Who feels it</b><span>The founder becomes the HR helpdesk and the escalation point.</span></li>
                        <li class="deck-block"><b>Where</b><span>Pakistani startups and SMEs growing faster than their documentation.</span></li>
                        <li class="deck-block"><b>Why alternatives fail</b><span>Agencies deliver reports, senior hires cost a salary, and neither runs the process.</span></li>
                    </ul>
                </li>

                <li class="deck-slide deck-slide--mist" id="slide-3">
                    <div class="deck-slide-head">
                        <img class="deck-logo deck-logo--primary" src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="125" height="32">
                        <img class="deck-logo deck-logo--reverse" src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="125" height="32">
                        <span class="deck-slide-count" aria-hidden="true">03 / {{ $total }}</span>
                    </div>
                    <h2>One method, four stages, written outputs</h2>
                    <ul class="deck-steps">
                        @foreach ($methodology as $step)
                            <li class="deck-step">
                                <b>{{ $step['step'] }} {{ $step['name'] }}</b>
                                <span>{{ $step['output'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <ul class="deck-blocks deck-blocks--3">
                        <li class="deck-block"><b>What</b><span>Assess, identify, improve and review the client's people operations.</span></li>
                        <li class="deck-block"><b>How</b><span>Audit the gap, name owners, install the process, then review it monthly.</span></li>
                        <li class="deck-block"><b>Why it wins</b><span>Every stage ends in a document the client keeps and can run.</span></li>
                    </ul>
                </li>

                <li class="deck-slide deck-slide--white" id="slide-4">
                    <div class="deck-slide-head">
                        <img class="deck-logo deck-logo--primary" src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="125" height="32">
                        <img class="deck-logo deck-logo--reverse" src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="125" height="32">
                        <span class="deck-slide-count" aria-hidden="true">04 / {{ $total }}</span>
                    </div>
                    <h2>Published retainers priced by headcount</h2>
                    <table class="deck-tiers">
                        <caption class="sr-only">Published monthly retainers by headcount</caption>
                        <thead>
                            <tr>
                                <th scope="col">Tier</th>
                                <th scope="col">Capacity</th>
                                <th scope="col">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packages as $package)
                                <tr>
                                    <td>{{ $package['name'] }}</td>
                                    <td>{{ $package['capacity'] }}</td>
                                    <td>{{ $package['price_display'] }} {{ $package['period'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <ul class="deck-blocks deck-blocks--2">
                        <li class="deck-block"><b>How we earn</b><span>Monthly retainers by headcount: 40,000, 75,000, or a custom enterprise quote.</span></li>
                        <li class="deck-block"><b>What tiers include</b><span>Fourteen services across payroll, performance and policy, delivered remotely.</span></li>
                        <li class="deck-block"><b>Who pays, who enters free</b><span>SMEs pay monthly; the audit and EmploStart are free.</span></li>
                        <li class="deck-block"><b>Why clients retain</b><span>The payroll calendar and the review cycle live with Emplowise.</span></li>
                    </ul>
                </li>

                <li class="deck-slide deck-slide--navy" id="slide-5">
                    <div class="deck-slide-head">
                        <img class="deck-logo deck-logo--primary" src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="125" height="32">
                        <img class="deck-logo deck-logo--reverse" src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="125" height="32">
                        <span class="deck-slide-count" aria-hidden="true">05 / {{ $total }}</span>
                    </div>
                    <h2>A clear wedge between DIY and enterprise</h2>
                    <ul class="deck-blocks deck-blocks--4 deck-blocks--stepped">
                        <li class="deck-block"><b>Who</b><span>Pakistani SMEs of five to one hundred staff with no HR owner.</span></li>
                        <li class="deck-block"><b>Where</b><span>Karachi, Lahore, and Islamabad, where hiring outpaces documentation.</span></li>
                        <li class="deck-block"><b>Why now</b><span>Labour expectations, payroll scrutiny, and founder bandwidth are all tightening.</span></li>
                        <li class="deck-block"><b>What wedge</b><span>Take the 10-to-50 band agencies ignore and a single hire cannot serve.</span></li>
                    </ul>
                </li>

                <li class="deck-slide deck-slide--offwhite" id="slide-6">
                    <div class="deck-slide-head">
                        <img class="deck-logo deck-logo--primary" src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="125" height="32">
                        <img class="deck-logo deck-logo--reverse" src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="125" height="32">
                        <span class="deck-slide-count" aria-hidden="true">06 / {{ $total }}</span>
                    </div>
                    <h2>Audit first, then EmploStart, then retainers</h2>
                    <ul class="deck-flow">
                        <li>Free HR Audit</li>
                        <li>Gap map</li>
                        <li>Retainer</li>
                    </ul>
                    <ul class="deck-blocks deck-blocks--2">
                        <li class="deck-block"><b>Who is the beachhead</b><span>Founders introduced through the free HR audit, met by one named lead.</span></li>
                        <li class="deck-block"><b>Where we reach them</b><span>The website, LinkedIn, referral partners, and the audit form.</span></li>
                        <li class="deck-block"><b>How it converts</b><span>The audit produces a gap map; the map names the retainer.</span></li>
                        <li class="deck-block"><b>When phases scale</b><span>Phase one is Pakistan; expansion follows proven retention.</span></li>
                    </ul>
                </li>

                <li class="deck-slide deck-slide--white" id="slide-7">
                    <div class="deck-slide-head">
                        <img class="deck-logo deck-logo--primary" src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="125" height="32">
                        <img class="deck-logo deck-logo--reverse" src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="125" height="32">
                        <span class="deck-slide-count" aria-hidden="true">07 / {{ $total }}</span>
                    </div>
                    <h2>Shipped platform, not promised platform</h2>
                    <div class="deck-split">
                        <div class="deck-zone">
                            <h3>Shipped</h3>
                            <ul class="deck-blocks deck-blocks--1">
                                <li class="deck-block"><b>What is shipped</b><span>Fourteen pages, three live career tools in a six-module EmploStart plan, an audit wizard, and a grounded assistant.</span></li>
                                <li class="deck-block"><b>Who engaged</b><span>No named client study yet; the proof is the shipped repository.</span></li>
                            </ul>
                        </div>
                        <div class="deck-zone deck-zone--tracked">
                            <h3>Tracked</h3>
                            <ul class="deck-blocks deck-blocks--1">
                                <li class="deck-block"><b>Where tracked</b><span>Outputs in the repository, effects in client words, outcomes only with baselines.</span></li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li class="deck-slide deck-slide--deepteal" id="slide-8">
                    <div class="deck-slide-head">
                        <img class="deck-logo deck-logo--primary" src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="125" height="32">
                        <img class="deck-logo deck-logo--reverse" src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="125" height="32">
                        <span class="deck-slide-count" aria-hidden="true">08 / {{ $total }}</span>
                    </div>
                    <h2>From toolkit to retained client work</h2>
                    <ul class="deck-phases">
                        <li class="deck-phase"><b>Phase I</b><span>Live site, audit, EmploStart, brand system.</span></li>
                        <li class="deck-phase"><b>Phase II</b><span>Showcase pages, editable documents, vector logo, client proof.</span></li>
                        <li class="deck-phase"><b>Phase III</b><span>AI automation division, still pre-launch.</span></li>
                    </ul>
                    <ul class="deck-blocks deck-blocks--2">
                        <li class="deck-block"><b>What ships next</b><span>Showcase pages, downloadable documents, and a vector brand system.</span></li>
                        <li class="deck-block"><b>When phases land</b><span>Phase II adds the Office suite and public proof; timing stays internal.</span></li>
                        <li class="deck-block"><b>How product ties to revenue</b><span>EmploStart feeds the audit; the audit feeds the retainer.</span></li>
                        <li class="deck-block"><b>Who owns execution</b><span>Founders own commercial and brand; engineering owns the platform.</span></li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>

    <section class="show-cta">
        <div class="shell show-cta-inner">
            <div>
                <h2>One free intake, one named lead, one gap map.</h2>
                <p>Take the deck as a PowerPoint file, or start with the audit it describes.</p>
            </div>
            <div class="show-cta-actions">
                <a class="btn btn--teal" href="{{ asset_v($page['download']['path']) }}" download>{{ $page['download']['label'] }}</a>
                <a class="btn btn--outline-light" href="{{ route('free-hr-audit') }}">Start the Free HR Audit</a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset_v('js/deck.js') }}" defer></script>
@endpush
