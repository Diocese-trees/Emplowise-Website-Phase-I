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

    <nav class="show-jump" aria-label="On this page">
        <div class="shell shell--narrow show-jump-inner">
            <span class="show-jump-label">On this page</span>
            <ul>
                <li><a href="#at-a-glance">At a glance</a></li>
                <li><a href="#market-gap">Market gap</a></li>
                <li><a href="#buying-situations">Who it is for</a></li>
                <li><a href="#scope">Scope</a></li>
                <li><a href="#method">Method</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#leadership">Leadership</a></li>
                <li><a href="#measurement">Measurement</a></li>
            </ul>
        </div>
    </nav>

    <section class="section" id="at-a-glance">
        <div class="shell shell--narrow">
            <p class="kicker">01 At a glance</p>
            <h2>{{ $seo['h2'] }}</h2>
            <p class="lede show-lede">
                One read for a co-founder, a board, or a bank. The short version: Emplowise installs and then runs the HR structure a growing company needs, so the founder stops being the HR helpdesk.
            </p>
            <dl class="show-facts">
                <div class="show-fact">
                    <dt>Category</dt>
                    <dd>{{ $brand['category'] }}</dd>
                </div>
                <div class="show-fact">
                    <dt>Delivery</dt>
                    <dd>Fully remote, supported by specialist consultants</dd>
                </div>
                <div class="show-fact">
                    <dt>Market</dt>
                    <dd>{{ $brand['market'] }}, remote team</dd>
                </div>
                <div class="show-fact">
                    <dt>Clients</dt>
                    <dd>Startups and SMEs, 5 to 100 employees</dd>
                </div>
                <div class="show-fact">
                    <dt>Free entry point</dt>
                    <dd>The Free HR Audit, a four-step intake reviewed by a named lead</dd>
                </div>
                <div class="show-fact">
                    <dt>Published retainers</dt>
                    <dd>PKR 40,000, PKR 75,000, and a custom tier above 50 employees</dd>
                </div>
                <div class="show-fact">
                    <dt>Second audience</dt>
                    <dd>Early career candidates, through EmploStart, free</dd>
                </div>
                <div class="show-fact">
                    <dt>Contact</dt>
                    <dd>hello@emplowise.com</dd>
                </div>
            </dl>
            <div class="show-note">
                <p>Every service, price, and name in this profile is drawn from the same configuration the website publishes. Nothing here is drafted separately for the document.</p>
            </div>
        </div>
    </section>

    <section class="section show-band" id="market-gap">
        <div class="shell shell--narrow">
            <p class="kicker">02 Market gap</p>
            <h2>The gap Emplowise was built to fill.</h2>
            <p class="lede show-lede">
                A Pakistani company with 10 to 100 staff has two established options, and both leave a hole in the middle.
            </p>
            <div class="show-grid show-grid--2">
                <article class="show-card">
                    <span class="show-label">Option one</span>
                    <h3>Informal HR, run on memory.</h3>
                    <p>Cheap in cash, costly in attention. Contracts live in a folder, payroll inputs arrive by WhatsApp, leave is tracked in memory, and the first real test is a resignation or a payroll error. Nobody owns the process because owning HR was never anyone's job.</p>
                </article>
                <article class="show-card">
                    <span class="show-label">Option two</span>
                    <h3>Enterprise HR, built for a larger company.</h3>
                    <p>A senior HR hire is a fixed salary plus benefits for a role that is not full time at 30 staff. A consultancy engagement often ends with a report rather than a running process.</p>
                </article>
            </div>
            <div class="show-note">
                <p>Emplowise sits between them: a monthly retainer, a named team, written scope, and execution rather than recommendations. The price is published. The method is the same for every client. The audit is free because the first useful conversation should start with the buyer's own gaps, not with a sales deck.</p>
            </div>
        </div>
    </section>

    <section class="section" id="buying-situations">
        <div class="shell shell--narrow">
            <p class="kicker">03 Buying situations</p>
            <h2>Four buying situations, not four demographics.</h2>
            <p class="lede show-lede">
                Emplowise sells to a moment, not to a job title. Each situation has a different first fix.
            </p>
            <div class="table-scroll" role="region" tabindex="0" aria-label="Buying situations and first fixes">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Situation</th>
                            <th scope="col">What the buyer says</th>
                            <th scope="col">What Emplowise installs first</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Growth outran the paperwork</td>
                            <td>"We are hiring faster than we can document."</td>
                            <td>Contracts, onboarding, leave, and payroll inputs on one calendar</td>
                        </tr>
                        <tr>
                            <td>A process failed once</td>
                            <td>"We had a payroll error and no one could explain it."</td>
                            <td>Records, approvals, and a written payroll rhythm</td>
                        </tr>
                        <tr>
                            <td>The founder is the helpdesk</td>
                            <td>"Every people question ends up with me."</td>
                            <td>A named HR owner, policies, and manager scripts</td>
                        </tr>
                        <tr>
                            <td>A document is needed</td>
                            <td>"I need something credible to show my board or my bank."</td>
                            <td>Policies, structure, and a monthly reporting pack</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h3>A second audience: early career candidates.</h3>
            <p class="show-lede">
                <a href="{{ route('emplostart') }}">EmploStart</a> gives students and first-job candidates the same structured thinking for free: interview practice, ATS feedback, a resume builder, a profile optimiser, and a prompt library. No account, no cost. It is a public good and an honest demonstration of how the team thinks about people operations.
            </p>
        </div>
    </section>

    <section class="section show-band" id="scope">
        <div class="shell shell--narrow">
            <p class="kicker">04 Scope</p>
            <h2>What is in scope, and what is not.</h2>
            <p class="lede show-lede">
                The work starts from the client's yearly goals and works backwards into HR structure, not from a policy template. If a process cannot be explained on one page, it is not finished.
            </p>
            <div class="show-grid show-grid--2">
                <article class="show-card">
                    <span class="show-label">In scope</span>
                    <ul class="show-list">
                        <li>Assessment and a written gap map</li>
                        <li>Policies, SOPs, and handbooks</li>
                        <li>Employee records and documentation</li>
                        <li>Payroll inputs and the payroll calendar</li>
                        <li>Leave and attendance</li>
                        <li>Onboarding</li>
                        <li>Performance support and manager coaching</li>
                        <li>Employee relations and conflict support</li>
                        <li>Organisational assessments</li>
                        <li>Monthly HR reporting and ongoing consultation</li>
                        <li>Recruitment execution</li>
                        <li>Specialist consultant input and a dedicated HR representative</li>
                    </ul>
                </article>
                <article class="show-card">
                    <span class="show-label">Out of scope, stated plainly</span>
                    <p>Emplowise does not act as a law firm, does not guarantee a legal or compliance outcome, does not guarantee hiring results, does not take board authority, and does not replace the client's management.</p>
                    <p>Payroll tax and disbursement scope, recruitment fee treatment, legal qualifications, representative allocation, and contract terms are confirmed in writing after the Free HR Audit. Those boundaries are part of the offer, not small print.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="method">
        <div class="shell shell--narrow">
            <p class="kicker">05 Method</p>
            <h2>Four stages, one sequence, four documents.</h2>
            <p class="lede show-lede">
                Every engagement follows the same sequence, and the output of each stage is a document the client keeps.
            </p>
            <ol class="show-steps">
                @foreach ($methodology as $step)
                    <li class="show-step">
                        <span class="show-step-num">Stage {{ $step['step'] }}</span>
                        <h3>{{ $step['name'] }}</h3>
                        <p>{{ $step['summary'] }}</p>
                        <span class="show-step-output">{{ $step['output'] }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="section show-band" id="services">
        <div class="shell shell--narrow">
            <p class="kicker">06 Services</p>
            <h2>Fourteen services across three pillars.</h2>
            <p class="lede show-lede">
                The automation described on the website is packaging and roadmap, not a sold software product. The services below are the confirmed work.
            </p>
            <div class="show-grid show-grid--3">
                @foreach ($pillars as $pillar)
                    <article class="show-card">
                        <span class="show-label">Pillar {{ $pillar['number'] }}</span>
                        <h3>{{ $pillar['name'] }}</h3>
                        <p>{{ $pillar['summary'] }}</p>
                        <ul class="show-list">
                            @foreach ($pillar['services'] as $service)
                                <li>{{ $service }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
            <div class="show-note">
                <p><strong>Executive HR partnership.</strong> Two further capabilities sit across all three pillars: specialist HR consultant support on defined people problems, and a dedicated HR representative with direct access to the client's CEO or founder.</p>
            </div>
        </div>
    </section>

    <section class="section" id="pricing">
        <div class="shell shell--narrow">
            <p class="kicker">07 Pricing</p>
            <h2>Published retainers, billed monthly.</h2>
            <p class="lede show-lede">
                Three retainers, priced by headcount. Prices are confirmed; inclusions per tier are confirmed in writing after the audit.
            </p>
            <div class="show-grid show-grid--3">
                @foreach ($packages as $package)
                    <article class="show-price {{ $package['featured'] ? 'is-featured' : '' }}">
                        <h3>{{ $package['name'] }}</h3>
                        <p class="show-price-capacity">{{ $package['capacity'] }}</p>
                        <p class="show-price-amount">{{ $package['price_display'] }}</p>
                        <p class="show-price-period">{{ $package['period'] }}</p>
                        <p class="show-price-best">{{ $package['best_for'] }}</p>
                        <p class="show-price-note">{{ $package['inclusions'] }}</p>
                    </article>
                @endforeach
            </div>
            <h3>Pricing notes</h3>
            <ul class="show-list">
                @foreach ($notes as $note)
                    <li>{{ $note }}</li>
                @endforeach
            </ul>
            <div class="show-note">
                <p><strong>The Free HR Audit</strong> is a four-step intake of about four minutes: contact details, company and headcount, pain points across payroll, performance, policy, compliance, recruitment, and reporting, then a preferred date and slot. A named lead reviews it, prepares a gap map, and confirms the window.</p>
                <p><a class="link-arrow" href="{{ route('free-hr-audit') }}">Start the Free HR Audit</a></p>
            </div>
        </div>
    </section>

    <section class="section show-band" id="leadership">
        <div class="shell shell--narrow">
            <p class="kicker">08 Leadership</p>
            <h2>Three people own the work.</h2>
            <p class="lede show-lede">
                Strategy, marketing, and people operations each have one named owner. They answer directly.
            </p>
            <div class="show-team">
                @foreach ($team['profiles'] as $profile)
                    <article class="show-person">
                        <span class="show-person-num">{{ $profile['num'] }}</span>
                        <h3>{{ $profile['name'] }}</h3>
                        <p class="show-person-role">{{ $profile['role'] }}</p>
                        <p>{{ $profile['focus'] }}.</p>
                    </article>
                @endforeach
            </div>
            <p class="show-lede">
                Samarah Nabil brings 9+ years in strategic HR, talent acquisition, and performance management, having engaged and supported 2,000+ employees, and leads Emplowise's core people operations, analytics, and organisational frameworks. Abdul Hadi works across brand narrative, growth strategy, and partnerships, alongside founding roles at Emplowise and The Creative Nexus. Muhammad Ali advises on long-term vision and governance.
            </p>
            <p class="show-disclaimer">Portraits on the website are placeholders until the official shoot. Names, roles, and profiles are confirmed.</p>
        </div>
    </section>

    <section class="section" id="measurement">
        <div class="shell shell--narrow">
            <p class="kicker">09 Measurement</p>
            <h2>Three kinds of measurement, never merged.</h2>
            <p class="lede show-lede">
                These are different things, measured differently. Only the first is quoted as a number today.
            </p>
            <div class="show-grid show-grid--3">
                <article class="show-card">
                    <span class="show-label">Inputs and outputs</span>
                    <h3>What Emplowise does and produces.</h3>
                    <p>Audits completed, gap maps delivered, policies written, payroll cycles closed on the published calendar, onboarding flows installed, review packs sent. Directly observable and the only things quoted as numbers.</p>
                </article>
                <article class="show-card">
                    <span class="show-label">Audience effects</span>
                    <h3>What changes for the client's team.</h3>
                    <p>Fewer people questions routed to the founder, managers with a script for difficult conversations, every employee with a record and a contract that matches reality. Stated in the client's words, not in invented percentages.</p>
                </article>
                <article class="show-card">
                    <span class="show-label">Commercial outcomes</span>
                    <h3>What belongs to the client.</h3>
                    <p>Revenue, retention, cost per employee, time to hire. Reported only when the client supplies a baseline and agrees to its use. Emplowise does not publish savings it cannot evidence.</p>
                </article>
            </div>
            <div class="show-note">
                <p>No invented percentages, savings, or client names appear anywhere in this profile. Baseline metrics and verified client results are confirmed in writing after the Free HR Audit.</p>
            </div>
        </div>
    </section>

    <section class="show-cta">
        <div class="shell show-cta-inner">
            <div>
                <h2>Take the profile, or start with the audit.</h2>
                <p>The document is the same content on this page, editable and ready to circulate.</p>
            </div>
            <div class="show-cta-actions">
                <a class="btn btn--teal" href="{{ asset_v($page['download']['path']) }}" download>{{ $page['download']['label'] }}</a>
                <a class="btn btn--outline-light" href="{{ route('free-hr-audit') }}">Start the Free HR Audit</a>
            </div>
        </div>
    </section>
@endsection
