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
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8"/></svg>
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
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            <span class="copy-label">{{ $copy['copy_label'] }}</span>
                        </button>
                    </div>
                    <p class="contact-copy-status" id="copy-feedback" role="status" aria-live="polite"></p>
                </section>

                <section class="contact-card" aria-labelledby="contact-social-title">
                    <h2 id="contact-social-title">{{ $copy['social_heading'] }}</h2>
                    <p>{{ $copy['social_body'] }}</p>
                    <div class="contact-socials">
                        @foreach ($copy['socials'] as $social)
                            {{-- TODO: replace href="#" with the live {{ $social['label'] }} profile URL. --}}
                            <a class="contact-social" href="#" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}">
                                @switch($social['icon'])
                                    @case('linkedin')
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-.95 1.83-1.95 3.77-1.95C20.3 8.75 21 11.1 21 14v7h-4v-6.2c0-1.48-.03-3.38-2.06-3.38-2.06 0-2.37 1.6-2.37 3.27V21H9z"/></svg>
                                        @break
                                    @case('instagram')
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none"/></svg>
                                        @break
                                    @case('x')
                                        <svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 2H22l-6.8 7.8L23 22h-6.3l-4.9-6.4L6.2 22H3.1l7.3-8.3L3 2h6.5l4.4 5.8z"/></svg>
                                        @break
                                    @default
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .5C5.73.5.5 5.73.5 12c0 5.08 3.29 9.39 7.86 10.91.58.11.79-.25.79-.56v-2.17c-3.2.7-3.87-1.37-3.87-1.37-.53-1.34-1.3-1.7-1.3-1.7-1.06-.72.08-.71.08-.71 1.17.08 1.79 1.2 1.79 1.2 1.04 1.78 2.73 1.27 3.4.97.11-.75.41-1.27.74-1.56-2.55-.29-5.23-1.28-5.23-5.7 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.18 1.18a11 11 0 0 1 5.8 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.84 1.19 3.1 0 4.43-2.69 5.41-5.25 5.69.42.36.79 1.08.79 2.18v3.23c0 .31.21.68.8.56A11.5 11.5 0 0 0 23.5 12C23.5 5.73 18.27.5 12 .5z"/></svg>
                                @endswitch
                            </a>
                        @endforeach
                    </div>
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
