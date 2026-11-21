@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@section('content')
    <section class="page-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ config('emplowise.copy.layout.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $copy['breadcrumb'] }}</span>
            </nav>
            <p class="kicker">{{ $copy['kicker'] }}</p>
            @include('partials.seo-h1', ['seo' => $seo])
            <p class="lede">
                {{ $seo['intro'] }}
            </p>
        </div>
    </section>

    <section class="section section--tight">
        <div class="shell">
            <div class="chip-row">
                @foreach ($copy['chips'] as $index => $chip)
                    <span class="chip {{ $index === 0 ? 'chip--teal' : ($index === 1 ? 'chip--teal' : '') }}">{{ $chip }}</span>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="shell">
            @foreach ($pillars as $pillar)
                @php($pillarServices = $servicesByPillar->flatten(1)->whereIn('name', $pillar['services']))
                <article class="pillar-deep" id="{{ $pillar['slug'] }}">
                    <div>
                        <span class="pillar-deep-idx">{{ $copy['pillar_label'] }} {{ $pillar['number'] }}</span>
                        <h2>{{ $pillar['name'] }}</h2>
                        <p class="small muted s36">{{ $pillar['kicker'] }}</p>
                        <p class="s37">{{ $pillar['summary'] }}</p>
                        <p class="s38">
                            <span class="badge badge--pending">{{ $pillar['status'] }}</span>
                        </p>
                        <a href="{{ route('free-hr-audit') }}" class="link-arrow s1">{{ $copy['pillar_link'] }}</a>
                    </div>

                    <div>
                        <h3 class="s39">
                            {{ $copy['checks'] }}
                        </h3>
                        <div class="auto-list">
                            @foreach ($pillar['automation'] as $item)
                                <div class="auto-item"><span>{{ $item }}</span></div>
                            @endforeach
                        </div>

                        <h3 class="s40">
                            {{ $copy['services'] }}
                        </h3>
                        <div class="table-wrap">
                            <table class="data">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ $copy['pillar_table'][0] }}</th>
                                        <th scope="col">{{ $copy['pillar_table'][1] }}</th>
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
                <p class="kicker">{{ $copy['catalogue']['kicker'] }}</p>
                <h2>{{ $seo['h2'] }}</h2>
                <p class="lede">
                    {{ $copy['catalogue']['lede'] }}
                </p>
            </div>

            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th scope="col">{{ $copy['catalogue']['table'][0] }}</th>
                            <th scope="col">{{ $copy['catalogue']['table'][1] }}</th>
                            <th scope="col">{{ $copy['catalogue']['table'][2] }}</th>
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
                {{ $copy['catalogue']['note'] }}
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="section-head">
                <p class="kicker">{{ $copy['method']['kicker'] }}</p>
                <h2>{{ $copy['method']['heading'] }}</h2>
                <p class="lede">
                    {{ $copy['method']['lede'] }}
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
                <p class="kicker">{{ $copy['outcomes']['kicker'] }}</p>
                <h2>{{ $copy['outcomes']['heading'] }}</h2>
                <p class="lede">
                    {{ $copy['outcomes']['lede'] }}
                </p>
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal s1">{{ $copy['outcomes']['cta'] }}</a>
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
            <h2>{{ $copy['cta_band']['heading'] }}</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">{{ $copy['cta_band']['primary'] }}</a>
                <a href="{{ route('pricing') }}" class="btn btn--outline-light">{{ $copy['cta_band']['secondary'] }}</a>
            </div>
        </div>
    </section>
@endsection
