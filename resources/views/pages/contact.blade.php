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
            <div class="contact-grid">
                <section class="contact-card contact-card--wide" aria-labelledby="contact-ai-title">
                    <h2 id="contact-ai-title">{{ $copy['ai_heading'] }}</h2>
                    <p>{{ $copy['ai_body'] }}</p>
                    <div class="chip-row contact-pills">
                        @foreach ($copy['ai_links'] as $link)
                            <a class="chip contact-ai-pill" href="{{ $link['href'].rawurlencode($copy['ai_query']) }}" target="_blank" rel="noopener noreferrer">
                                {{ $link['label'] }}
                                <x-icon name="external" :size="14" />
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="contact-card" aria-labelledby="contact-email-title">
                    <h2 id="contact-email-title">{{ $copy['email_heading'] }}</h2>
                    <p>{{ $copy['email_body'] }}</p>
                    {{-- TODO: replace hello@emplowise.com with the live inbox before launch. --}}
                    <a class="contact-email" href="mailto:{{ $copy['email'] }}">{{ $copy['email'] }}</a>
                    <div class="contact-email-actions">
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
                </section>

                <nav class="contact-card contact-card--wide" aria-labelledby="contact-resources-title">
                    <h2 id="contact-resources-title">{{ $copy['resources_heading'] }}</h2>
                    <div class="chip-row contact-pills">
                        @foreach ($copy['resources'] as $resource)
                            <a class="chip" href="{{ route($resource['route']) }}">{{ $resource['label'] }}</a>
                        @endforeach
                    </div>
                </nav>
            </div>

            <p class="contact-micro">
                <span>{{ $copy['micro_copyright'] }}</span>
                <span class="contact-micro-sep" aria-hidden="true">|</span>
                <span>{{ $copy['micro_status'] }}</span>
            </p>
        </div>
    </section>
@endsection
