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
            <p class="kicker">{{ $brand['essence'] }}</p>
            @include('partials.seo-h1', ['seo' => $seo])
            <p class="lede">
                {{ $seo['intro'] }}
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell split">
            <div>
                <p class="kicker">{{ $copy['why_kicker'] }}</p>
                <h2>{{ $mission['statement'] }}</h2>
                <p class="lede">{{ $mission['body'] }}</p>
                <p class="small muted s23">
                    {{ str_replace([':category', ':market', ':expansion'], [$brand['category'], $brand['market'], $brand['expansion']], $copy['category_line']) }}
                </p>
            </div>
            <div>
                <blockquote class="pull-quote">
                    {{ $brand['essence'] }}
                    <cite>{{ $copy['quote_cite'] }}</cite>
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
                <p class="kicker">{{ $copy['values']['kicker'] }}</p>
                <h2>{{ $copy['values']['heading'] }}</h2>
                <p class="lede">
                    {{ $copy['values']['lede'] }}
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
                <p class="kicker">{{ $copy['audiences']['kicker'] }}</p>
                <h2>{{ $copy['audiences']['heading'] }}</h2>
                <p class="lede">
                    {{ $copy['audiences']['lede'] }}
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
                            <a href="{{ route('student-hub') }}" class="link-arrow s8">{{ $copy['audiences']['link_talent'] }}</a>
                        @else
                            <a href="{{ route('pricing') }}" class="link-arrow s8">{{ $copy['audiences']['link_client'] }}</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--ice">
        <div class="shell split">
            <div>
                <p class="kicker">{{ $copy['delivery']['kicker'] }}</p>
                <h2>{{ $seo['h2'] }}</h2>
                <p class="lede">
                    {{ $copy['delivery']['lede'] }}
                </p>
            </div>
            <div>
                <ul class="index-list">
                    @foreach ($brand['delivery'] as $line)
                        <li><span>{{ $line }}</span></li>
                    @endforeach
                </ul>
                <p class="small muted s8">
                    {{ $copy['delivery']['note'] }}
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="shell">
            <div class="card card--flat">
                <div class="panel-head s30">
                    <div>
                        <p class="kicker s24">{{ $copy['scope']['kicker'] }}</p>
                        <h2 class="s31">{{ $copy['scope']['heading'] }}</h2>
                    </div>
                    <span class="badge badge--pending">{{ $copy['scope']['badge'] }}</span>
                </div>
                <p class="small muted s30">
                    {{ $copy['scope']['intro'] }}
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
            <h2>{{ $copy['cta_band']['heading'] }}</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">{{ $copy['cta_band']['primary'] }}</a>
                <a href="{{ route('what-we-do') }}" class="btn btn--outline-light">{{ $copy['cta_band']['secondary'] }}</a>
            </div>
        </div>
    </section>
@endsection
