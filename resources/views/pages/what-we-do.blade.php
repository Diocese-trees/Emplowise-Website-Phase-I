@extends('layouts.app')

@section('title', 'What We Do | Emplowise')
@section('meta_description', 'Payroll operations, performance management, and policy making run remotely by Emplowise, with workflow checks, templates, and a four-stage diagnostic method.')

@section('content')
    <section class="page-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <span>What We Do</span>
            </nav>
            <p class="kicker">Operations, systems, and outcomes</p>
            <h1>We run the work, not the workshop.</h1>
            <p class="lede">
                Three operational pillars, fourteen confirmed services, and a diagnostic method that ends in
                running processes. Workflow checks and templates support the service and are not sold as standalone software.
            </p>
        </div>
    </section>

    <section class="section section--tight">
        <div class="shell">
            <div class="chip-row">
                <span class="chip chip--teal">Remote delivery</span>
                <span class="chip chip--teal">Dedicated HR representative</span>
                <span class="chip">Specialist consultant backup</span>
                <span class="chip">Monthly reporting</span>
                <span class="chip">Free diagnostic audit</span>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="shell">
            @foreach ($pillars as $pillar)
                @php($pillarServices = $servicesByPillar->flatten(1)->whereIn('name', $pillar['services']))
                <article class="pillar-deep" id="{{ $pillar['slug'] }}">
                    <div>
                        <span class="pillar-deep-idx">Pillar {{ $pillar['number'] }}</span>
                        <h2>{{ $pillar['name'] }}</h2>
                        <p class="small muted s36">{{ $pillar['kicker'] }}</p>
                        <p class="s37">{{ $pillar['summary'] }}</p>
                        <p class="s38">
                            <span class="badge badge--pending">{{ $pillar['status'] }}</span>
                        </p>
                        <a href="{{ route('free-hr-audit') }}" class="link-arrow s1">Audit this pillar</a>
                    </div>

                    <div>
                        <h3 class="s39">
                            Workflow checks and templates
                        </h3>
                        <div class="auto-list">
                            @foreach ($pillar['automation'] as $item)
                                <div class="auto-item"><span>{{ $item }}</span></div>
                            @endforeach
                        </div>

                        <h3 class="s40">
                            Confirmed services
                        </h3>
                        <div class="table-wrap">
                            <table class="data">
                                <thead>
                                    <tr>
                                        <th scope="col">Service</th>
                                        <th scope="col">Scope note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pillarServices as $service)
                                        <tr>
                                            <td>{{ $service['name'] }}</td>
                                            <td>{{ $service['note'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section section--alt">
        <div class="shell">
            <div class="section-head">
                <p class="kicker">The full service catalogue</p>
                <h2>Fourteen services, grouped by operating family.</h2>
                <p class="lede">
                    The pillar showcase is how clients think about the work. These are the actual service
                    families used to scope an engagement.
                </p>
            </div>

            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th scope="col">Family</th>
                            <th scope="col">Service</th>
                            <th scope="col">Status and scope</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servicesByPillar as $family => $items)
                            @foreach ($items as $service)
                                <tr>
                                    <td>{{ $family }}</td>
                                    <td>{{ $service['name'] }}</td>
                                    <td>{{ $service['note'] }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="service-table-note">
                Every service above is confirmed in the 2026 brief. Package inclusion per tier is confirmed in writing after your Free HR Audit.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="section-head">
                <p class="kicker">Diagnostic method</p>
                <h2>A repeatable path from audit to review.</h2>
                <p class="lede">
                    This is the working method behind every pillar. It is also the structure of the free HR
                    audit, minus the implementation.
                </p>
            </div>

            <div class="method-grid">
                @foreach ($methodology as $step)
                    <div class="method-step">
                        <span class="method-num">{{ $step['step'] }}</span>
                        <h3>{{ $step['name'] }}</h3>
                        <p>{{ $step['summary'] }}</p>
                        <span class="method-output">{{ $step['output'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--navy">
        <div class="shell split">
            <div>
                <p class="kicker">Measurable outcomes</p>
                <h2>What changes when the structure lands.</h2>
                <p class="lede">
                    These are operating outcomes Emplowise works toward. Baseline metrics and verified client
                    results are confirmed in writing after your Free HR Audit, so no percentages are published here.
                </p>
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal s1">Start with the audit</a>
            </div>
            <div>
                <ul class="index-list">
                    @foreach ($outcomes as $outcome)
                        <li><span class="s41">{{ $outcome }}</span></li>
                    @endforeach
                </ul>
                <p class="small s42">{{ $outcomesNote }}</p>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>See where your HR stack actually breaks.</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">Book the free audit</a>
                <a href="{{ route('pricing') }}" class="btn btn--outline-light">Compare packages</a>
            </div>
        </div>
    </section>
@endsection
