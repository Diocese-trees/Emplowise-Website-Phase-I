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

    <section class="section section--tight team-section">
        <div class="shell">
            <div class="section-head">
                <h2>{{ $seo['h2'] }}</h2>
                <p class="lede">{{ $copy['showcase_lede'] }}</p>
            </div>

            <div class="team-stage">
                <div class="team-window" role="group" aria-label="{{ $copy['window_label'] }}">
                    <div class="team-window-bar">
                        <span class="team-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                        <span class="team-window-url">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            {{ $copy['window_title'] }}
                        </span>
                        <span class="team-window-meta">{{ $copy['window_meta'] }}</span>
                    </div>

                    <div class="team-tabs" role="tablist" aria-label="{{ $copy['tablist_label'] }}">
                        @foreach ($copy['profiles'] as $profile)
                            <button type="button"
                                    class="team-tab {{ $loop->first ? 'is-active' : '' }}"
                                    id="team-tab-{{ $profile['id'] }}"
                                    data-team-tab="{{ $profile['id'] }}"
                                    role="tab"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-controls="team-panel-{{ $profile['id'] }}"
                                    tabindex="{{ $loop->first ? '0' : '-1' }}">
                                <span class="team-tab-num">{{ $profile['num'] }}</span>
                                <span class="team-tab-name">{{ $profile['name'] }}</span>
                                <span class="team-tab-role">{{ $profile['role'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <div class="team-panels">
                        @foreach ($copy['profiles'] as $profile)
                            <section class="team-panel {{ $loop->first ? 'is-active' : '' }}"
                                     id="team-panel-{{ $profile['id'] }}"
                                     data-team-panel="{{ $profile['id'] }}"
                                     role="tabpanel"
                                     aria-labelledby="team-tab-{{ $profile['id'] }}"
                                     tabindex="0">
                                <figure class="team-photo">
                                    {{-- TODO: replace this placeholder portrait with the confirmed team photo at the same 4:5 ratio. --}}
                                    <img src="{{ asset_v($profile['portrait']) }}"
                                         alt="{{ $profile['alt'] }}"
                                         width="800"
                                         height="1000"
                                         loading="lazy"
                                         decoding="async">
                                    <figcaption><span>{{ $profile['num'] }}</span> {{ $profile['role'] }}</figcaption>
                                </figure>

                                <div class="team-profile">
                                    <p class="team-role">{{ $profile['role'] }}</p>
                                    <h3>{{ $profile['name'] }}</h3>
                                    <p class="team-focus">
                                        <span>{{ $copy['focus_label'] }}</span>
                                        {{ $profile['focus'] }}
                                    </p>
                                    @if ($profile['bio_todo'])
                                        {{-- {{ $profile['bio_todo'] }} --}}
                                    @endif
                                    <p class="team-bio">{{ $profile['bio'] }}</p>
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>
            </div>

            <p class="team-note">{{ $copy['panel_note'] }}</p>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <div>
                <h2>{{ $copy['cta_heading'] }}</h2>
                <p class="lede">{{ $copy['cta_body'] }}</p>
            </div>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">
                    {{ config('emplowise.copy.home.cta_primary') }}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
        </div>
    </section>
@endsection
