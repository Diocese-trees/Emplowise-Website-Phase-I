@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@section('content')
    <div class="ai-banner" role="note">
        <div class="shell ai-banner-inner">
            <span class="ai-banner-dot" aria-hidden="true"></span>
            <span>{{ $copy['banner'] }}</span>
        </div>
    </div>

    <section class="page-head ai-hero">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ config('emplowise.copy.layout.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $copy['breadcrumb'] }}</span>
            </nav>
            <p class="kicker">{{ $copy['kicker'] }}</p>
            @php($h1Text = $seo['h1'] ?? '')
            @php($h1Accent = $seo['h1_accent'] ?? null)
            <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
            <p class="lede">{{ $seo['intro'] }}</p>
            <p class="ai-hero-sub">{{ $copy['hero_sub'] }}</p>
            <div class="hero-actions ai-actions">
                <button class="btn btn--teal" type="button" disabled aria-disabled="true">
                    {{ $copy['cta'] }}
                    <x-icon name="clock" :size="16" />
                </button>
                <a class="btn btn--ghost" href="{{ route('what-we-do') }}">{{ $copy['back_label'] }}</a>
            </div>
            <p class="ai-cta-note">{{ $copy['cta_note'] }}</p>
        </div>
    </section>

    <section class="section section--tight">
        <div class="shell">
            <div class="section-head">
                <h2>{{ $seo['h2'] }}</h2>
                <p class="lede">{{ $copy['modules_heading'] }} {{ $copy['modules_lede'] }}</p>
            </div>

            <div class="ai-modules">
                @foreach ($copy['modules'] as $module)
                    <article class="ai-module">
                        <span class="ai-module-icon" aria-hidden="true">
                            @switch($module['icon'])
                                @case('payroll')
                                    <x-icon name="card" :size="22" />
                                    @break
                                @case('performance')
                                    <x-icon name="chart-bars" :size="22" />
                                    @break
                                @default
                                    <x-icon name="shield-check" :size="22" />
                            @endswitch
                        </span>
                        <p class="ai-module-status">{{ $copy['module_status'] }}</p>
                        <h3>{{ $module['name'] }}</h3>
                        <p>{{ $module['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--alt ai-dashboard-section">
        <div class="shell">
            <figure class="ai-dashboard" aria-labelledby="ai-dashboard-title">
                <div class="ai-window">
                    <div class="ai-window-bar">
                        <span class="ai-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                        <span class="ai-window-title" id="ai-dashboard-title">
                            {{ $copy['dashboard']['window'] }}
                            <b>{{ $copy['dashboard']['tab'] }}</b>
                        </span>
                        <span class="ai-window-status">{{ $copy['dashboard']['status'] }}</span>
                    </div>

                    <div class="ai-desk">
                        <aside class="ai-desk-side" aria-hidden="true">
                            <span class="ai-desk-logo"></span>
                            <span class="ai-side-line is-on"></span>
                            <span class="ai-side-line"></span>
                            <span class="ai-side-line"></span>
                            <span class="ai-side-line is-short"></span>
                        </aside>

                        <div class="ai-desk-main">
                            <div class="ai-kpis">
                                @foreach ($copy['dashboard']['kpis'] as $kpi)
                                    <div class="ai-kpi">
                                        <span>{{ $kpi['label'] }}</span>
                                        <b>{{ $kpi['value'] }}</b>
                                    </div>
                                @endforeach
                            </div>

                            <div class="ai-chart" aria-hidden="true">
                                <span class="ai-chart-label"></span>
                                <div class="ai-bars">
                                    <i class="ai-bar b1"></i>
                                    <i class="ai-bar b2"></i>
                                    <i class="ai-bar b3"></i>
                                    <i class="ai-bar b4"></i>
                                    <i class="ai-bar b5"></i>
                                    <i class="ai-bar b6"></i>
                                    <i class="ai-bar b7"></i>
                                </div>
                            </div>

                            <ul class="ai-rows">
                                @foreach ($copy['dashboard']['rows'] as $row)
                                    <li>
                                        <span class="ai-row-name">{{ $row['name'] }}</span>
                                        <span class="ai-row-meta">{{ $row['meta'] }}</span>
                                        <span class="ai-row-state">{{ $row['state'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <figcaption class="ai-dashboard-caption">{{ $copy['dashboard']['caption'] }}</figcaption>
            </figure>

            <p class="ai-back">
                <a class="link-arrow" href="{{ route('what-we-do') }}">{{ $copy['back_label'] }}</a>
            </p>
        </div>
    </section>
@endsection
