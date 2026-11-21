@extends('layouts.app')

@section('title', 'Pricing | Emplowise')
@section('meta_description', 'Emplowise monthly retainers: Starter PKR 40,000 for up to 20 employees, Business PKR 75,000 for up to 50, and Enterprise custom for 50 plus. Confirmed 2026 pricing.')

@section('content')
    <section class="page-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <span>Pricing</span>
            </nav>
            <p class="kicker">Monthly retainers</p>
            <h1>Three packages. Prices published. Inclusions scoped honestly.</h1>
            <p class="lede">
                These are the confirmed 2026 monthly retainers. Package by package deliverables are confirmed
                in writing after your Free HR Audit, so we do not pretend a tier contains services the source material never fixed.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="price-grid">
                @foreach ($packages as $package)
                    <article class="price-card {{ $package['featured'] ? 'price-card--featured' : '' }}">
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
                            <li>Services drawn from the confirmed catalogue.</li>
                            <li>Deliverables per tier: confirmed in writing after your Free HR Audit.</li>
                        </ul>
                        <p class="price-note">{{ $package['inclusions'] }}</p>
                        <a href="{{ route('free-hr-audit') }}" class="btn {{ $package['featured'] ? 'btn--light' : 'btn--ghost' }} btn--sm s8">
                            {{ $package['price_pkr'] === null ? 'Request a quote' : 'Start with the free audit' }}
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="grid-2 s9">
                <div>
                    <h2 class="s10">What the price includes, and what it does not</h2>
                    <ul class="notes-list">
                        @foreach ($notes as $note)
                            <li><span>{{ $note }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="card card--flat">
                    <p class="kicker s11">Scope guardrail</p>
                    <h3 class="s12">No invented deliverables</h3>
                    <p class="small s13">
                        A pricing page that invents inclusions is worse than one that admits the gap. The audit
                        conversation fixes which confirmed services apply to your headcount, risk profile, and
                        current documentation.
                    </p>
                    <a href="{{ route('free-hr-audit') }}" class="link-arrow">Book the free audit</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--alt">
        <div class="shell">
            <div class="section-head">
                <p class="kicker">Side by side</p>
                <h2>The comparison, without invented rows.</h2>
                <p class="lede">
                    Headcount capacity and price are confirmed. Inclusion rows are confirmed in writing after your
                    Free HR Audit.
                </p>
            </div>

            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th scope="col">Package</th>
                            <th scope="col">Headcount</th>
                            <th scope="col">Monthly price</th>
                            <th scope="col">Best for</th>
                            <th scope="col">Inclusions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            <tr>
                                <td>{{ $package['name'] }}</td>
                                <td>{{ $package['capacity'] }}</td>
                                <td>{{ $package['price_pkr'] === null ? $package['price_display'].' (scoped monthly)' : $package['price_display'].' / month' }}</td>
                                <td>{{ $package['best_for'] }}</td>
                                <td class="is-pending">Confirmed in writing after your Free HR Audit.</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="service-table-note">
                Recruitment fee inclusion, payroll tax scope, and legal advice qualifications are confirmed in
                writing after your Free HR Audit.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="section-head">
                <p class="kicker">Confirmed catalogue</p>
                <h2>Every service that can sit inside a retainer.</h2>
                <p class="lede">
                    Packages are priced by headcount. Which of these fourteen services are active in your
                    retainer is confirmed in scope.
                </p>
            </div>

            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th scope="col">Service</th>
                            <th scope="col">Operating family</th>
                            <th scope="col">Scope note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td>{{ $service['name'] }}</td>
                                <td>{{ $service['pillar'] }}</td>
                                <td>{{ $service['note'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="legacy s14">
                <strong>Note on older pricing</strong>
                {{ $legacy['note'] }}
                <div class="legacy-row">
                    @foreach ($legacy['tiers'] as $tier)
                        <span>{{ $tier['name'] }} · {{ $tier['price'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>Pick a package after the audit, not before.</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">Book the free audit</a>
                <a href="{{ route('faq') }}" class="btn btn--outline-light">Read pricing FAQs</a>
            </div>
        </div>
    </section>
@endsection
