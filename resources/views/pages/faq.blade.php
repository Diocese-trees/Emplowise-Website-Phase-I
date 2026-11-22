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
            <p class="kicker">{{ str_replace([':count', ':groups'], [$total, count($groups)], $copy['kicker']) }}</p>
            @php($h1Text = $seo['h1'] ?? '')
            @php($h1Accent = $seo['h1_accent'] ?? null)
            <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
            <p class="lede">
                {{ $seo['intro'] }}
            </p>
        </div>
    </section>

    <section class="section section--tight">
        <div class="shell shell--narrow">
            <div class="faq-toolbar">
                <div class="search-box" id="faq-search-box">
                    <x-icon name="search" :size="16" />
                    <label class="sr-only" for="faq-search">{{ $copy['search_label'] }}</label>
                    <input id="faq-search" type="search" placeholder="{{ $copy['search_placeholder'] }}" autocomplete="off">
                    <button type="button" class="search-clear" id="faq-clear" aria-label="{{ $copy['clear_search'] }}" hidden>&times;</button>
                </div>
                <div class="filter-row" id="faq-filters">
                    <button type="button" class="filter-btn is-active" data-faq-filter="all">{{ $copy['filter_all'] }}</button>
                    @foreach ($groups as $group)
                        <button type="button" class="filter-btn" data-faq-filter="{{ $group['category'] }}">{{ $group['category'] }}</button>
                    @endforeach
                </div>
            </div>

            <div id="faq-list">
                @foreach ($groups as $groupIndex => $group)
                    <section class="faq-group" data-faq-group data-cat="{{ $group['category'] }}">
                        <div class="faq-group-head">
                            <h2>{{ $group['category'] }}</h2>
                            <span>{{ count($group['items']) }} {{ $copy['answers_suffix'] }}</span>
                        </div>

                        <div class="accordion">
                            @foreach ($group['items'] as $itemIndex => $item)
                                @php($panelId = 'faq-panel-'.$groupIndex.'-'.$itemIndex)
                                <div class="acc-item" data-cat="{{ $group['category'] }}">
                                    <button type="button"
                                            class="acc-trigger"
                                            aria-expanded="false"
                                            aria-controls="{{ $panelId }}">
                                        <span class="acc-q">{{ $item['q'] }}</span>
                                        <span class="acc-icon" aria-hidden="true"></span>
                                    </button>
                                    <div class="acc-panel" id="{{ $panelId }}">
                                        <p class="acc-a">{{ $item['a'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <p class="faq-empty" id="faq-empty">{{ $copy['empty'] }}</p>

            <div class="card card--flat s3">
                <div class="panel-head s4">
                    <div>
                        <p class="kicker s5">{{ $copy['assistant']['kicker'] }}</p>
                        <h2 class="s6">{{ $copy['assistant']['heading'] }}</h2>
                    </div>
                    <span class="badge">{{ $copy['assistant']['badge'] }}</span>
                </div>
                <p class="small s7">
                    {{ $copy['assistant']['body'] }}
                </p>
                <button type="button" class="btn btn--primary btn--sm" data-open-chat>{{ $copy['assistant']['button'] }}</button>
            </div>

            <p class="small muted s1">{{ $note }}</p>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>{{ $copy['cta_band']['heading'] }}</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">{{ $copy['cta_band']['primary'] }}</a>
                <a href="{{ route('emplostart') }}" class="btn btn--outline-light">{{ $copy['cta_band']['secondary'] }}</a>
            </div>
        </div>
    </section>

@push('scripts')
    <script src="{{ asset_v('js/faq.js') }}" defer></script>
@endpush
@endsection
