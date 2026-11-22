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
            <p class="lede">{{ $seo['intro'] }}</p>
        </div>
    </section>

    <section class="section section--tight contact-section">
        <div class="shell">
            {{-- Direct channel first: one panel, two actions, and the answers
                 people look for before writing (hours, reply time, base). --}}
            <section class="contact-panel" aria-labelledby="contact-panel-title">
                <div class="contact-panel-copy">
                    <p class="contact-panel-kicker">{{ $copy['panel_kicker'] }}</p>
                    <h2 id="contact-panel-title">{{ $copy['panel_heading'] }}</h2>
                    <p>{{ $copy['panel_body'] }}</p>
                    <div class="contact-panel-actions">
                        <a class="btn btn--light" href="mailto:{{ $copy['email'] }}">
                            <x-icon name="mail" :size="18" />
                            {{ $copy['panel_email_cta'] }}
                        </a>
                        <a class="btn btn--outline-light" href="{{ route('free-hr-audit') }}">
                            {{ $copy['panel_audit_cta'] }}
                            <x-icon name="arrow-right" :size="16" />
                        </a>
                    </div>
                </div>

                <dl class="contact-facts">
                    <div class="contact-fact">
                        <dt><x-icon name="clock" :size="15" />{{ $copy['fact_hours_label'] }}</dt>
                        <dd>{{ $copy['fact_hours'] }}</dd>
                    </div>
                    <div class="contact-fact">
                        <dt><x-icon name="mail" :size="15" />{{ $copy['fact_reply_label'] }}</dt>
                        <dd>{{ $copy['fact_reply'] }}</dd>
                    </div>
                    <div class="contact-fact">
                        <dt><x-icon name="shield-check" :size="15" />{{ $copy['fact_market_label'] }}</dt>
                        <dd>{{ $copy['fact_market'] }}</dd>
                    </div>
                </dl>
            </section>

            <div class="contact-grid">
                <section class="contact-card contact-steps contact-card--wide" aria-labelledby="contact-next-title">
                    <h2 id="contact-next-title">{{ $copy['next_heading'] }}</h2>
                    <p>{{ $copy['next_body'] }}</p>
                    <ol class="contact-step-list">
                        @foreach ($copy['next_steps'] as $index => $step)
                            <li class="contact-step">
                                <span class="contact-step-num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <h3>{{ $step['title'] }}</h3>
                                <p>{{ $step['body'] }}</p>
                            </li>
                        @endforeach
                    </ol>
                </section>

                <section class="contact-card" aria-labelledby="contact-email-title">
                    <h2 id="contact-email-title">{{ $copy['email_heading'] }}</h2>
                    <p>{{ $copy['email_body'] }}</p>
                    {{-- TODO: replace hello@emplowise.com with the live inbox before launch. --}}
                    <a class="contact-email" href="mailto:{{ $copy['email'] }}">{{ $copy['email'] }}</a>
                    <div class="contact-email-actions">
                        <a class="btn btn--primary btn--sm" href="mailto:{{ $copy['email'] }}">
                            {{ $copy['email_write'] }}
                            <x-icon name="arrow-right" :size="16" />
                        </a>
                        <button class="btn btn--ghost btn--sm" type="button" id="copy-email"
                            data-email="{{ $copy['email'] }}"
                            data-done="{{ $copy['copy_done'] }}"
                            data-fail="{{ $copy['copy_fail'] }}">
                            <x-icon name="copy" :size="16" />
                            <span class="copy-label">{{ $copy['copy_label'] }}</span>
                        </button>
                    </div>
                    <p class="contact-copy-status" id="copy-feedback" role="status" aria-live="polite"></p>
                </section>

                <section class="contact-card" aria-labelledby="contact-social-title">
                    <h2 id="contact-social-title">{{ $copy['social_heading'] }}</h2>
                    <p>{{ $copy['social_body'] }}</p>
                    @include('partials.social-links')
                    <p class="contact-card-foot">{{ $copy['social_note'] }}</p>
                </section>

                <nav class="contact-card contact-card--resources" aria-labelledby="contact-resources-title">
                    <h2 id="contact-resources-title">{{ $copy['resources_heading'] }}</h2>
                    <div class="chip-row contact-pills">
                        @foreach ($copy['resources'] as $resource)
                            <a class="chip" href="{{ route($resource['route']) }}">{{ $resource['label'] }}</a>
                        @endforeach
                    </div>
                    <p class="contact-card-foot">{{ $copy['resources_note'] }}</p>
                </nav>

                <section class="contact-card contact-card--ai contact-card--wide" aria-labelledby="contact-ai-title">
                    <div class="contact-card--ai-copy">
                        <h2 id="contact-ai-title">{{ $copy['ai_heading'] }}</h2>
                        <p>{{ $copy['ai_body'] }}</p>
                    </div>
                    <div class="chip-row contact-pills">
                        @foreach ($copy['ai_links'] as $link)
                            <a class="chip contact-ai-pill" href="{{ $link['href'].rawurlencode($copy['ai_query']) }}" target="_blank" rel="noopener noreferrer">
                                {{ $link['label'] }}
                                <x-icon name="external" :size="14" />
                            </a>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection
