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
            <div class="section-head">
                <p class="kicker">{{ $copy['why']['kicker'] }}</p>
                <h2>{{ $copy['why']['heading'] }}</h2>
                <p class="lede">{{ $copy['why']['lede'] }}</p>
            </div>
            <div class="grid-3">
                @foreach ($copy['why']['cards'] as $card)
                    <article class="pos-card">
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="price-grid">
                @foreach ($packages as $package)
                    <article class="price-card {{ $package['featured'] ? 'price-card--featured' : '' }}">
                        @if ($package['featured'])
                            <span class="price-tag">{{ $copy['tag'] }}</span>
                        @endif
                        <p class="price-name">{{ $package['name'] }}</p>
                        <p class="price-amount">{{ $package['price_display'] }}</p>
                        <p class="price-period">{{ $package['period'] }}</p>
                        <span class="price-capacity">{{ $package['capacity'] }}</span>
                        <ul class="price-list">
                            <li>{{ $package['summary'] }}</li>
                            <li>{{ $package['best_for'] }}</li>
                            @foreach ($copy['bullets'] as $bullet)
                                <li>{{ $bullet }}</li>
                            @endforeach
                        </ul>
                        <p class="price-note">{{ $package['inclusions'] }}</p>
                        <a href="{{ route('free-hr-audit') }}" class="btn {{ $package['featured'] ? 'btn--light' : 'btn--ghost' }} btn--sm s8">
                            {{ $package['price_pkr'] === null ? $copy['button_quote'] : $copy['button_audit'] }}
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="grid-2 s9">
                <div>
                    <h2 class="s10">{{ $copy['includes']['heading'] }}</h2>
                    <ul class="notes-list">
                        @foreach ($notes as $note)
                            <li><span>{{ $note }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="card card--flat">
                    <p class="kicker s11">{{ $copy['guardrail']['kicker'] }}</p>
                    <h3 class="s12">{{ $copy['guardrail']['heading'] }}</h3>
                    <p class="small s13">
                        {{ $copy['guardrail']['body'] }}
                    </p>
                    <a href="{{ route('free-hr-audit') }}" class="link-arrow">{{ $copy['guardrail']['link'] }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--alt">
        <div class="shell">
            <div class="section-head">
                <p class="kicker">{{ $copy['compare']['kicker'] }}</p>
                <h2>{{ $seo['h2'] }}</h2>
                <p class="lede">
                    {{ $copy['compare']['lede'] }}
                </p>
            </div>

            <div class="table-wrap" role="region" tabindex="0" aria-label="{{ $seo['h2'] }}">
                <table class="data">
                    <thead>
                        <tr>
                            <th scope="col">{{ $copy['compare']['table'][0] }}</th>
                            <th scope="col">{{ $copy['compare']['table'][1] }}</th>
                            <th scope="col">{{ $copy['compare']['table'][2] }}</th>
                            <th scope="col">{{ $copy['compare']['table'][3] }}</th>
                            <th scope="col">{{ $copy['compare']['table'][4] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            <tr>
                                <td>{{ $package['name'] }}</td>
                                <td>{{ $package['capacity'] }}</td>
                                <td>{{ $package['price_pkr'] === null ? $package['price_display'].' ('.$copy['compare']['scoped'].')' : $package['price_display'].' '.$copy['compare']['per_month'] }}</td>
                                <td>{{ $package['best_for'] }}</td>
                                <td class="is-pending">{{ $copy['compare']['pending_cell'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="service-table-note">
                {{ $copy['compare']['note'] }}
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="section-head">
                <p class="kicker">{{ $copy['catalogue']['kicker'] }}</p>
                <h2>{{ $copy['catalogue']['heading'] }}</h2>
                <p class="lede">
                    {{ $copy['catalogue']['lede'] }}
                </p>
            </div>

            <div class="table-wrap" role="region" tabindex="0" aria-label="{{ $copy['catalogue']['heading'] }}">
                <table class="data">
                    <thead>
                        <tr>
                            <th scope="col">{{ $copy['catalogue']['table'][0] }}</th>
                            <th scope="col">{{ $copy['catalogue']['table'][1] }}</th>
                            <th scope="col">{{ $copy['catalogue']['table'][2] }}</th>
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
