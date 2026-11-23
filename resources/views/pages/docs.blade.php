@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@push('styles')
    <link rel="stylesheet" href="{{ asset_v('css/showcase.css') }}">
@endpush

@section('content')
    @php($h1Text = $seo['h1'] ?? '')
    @php($h1Accent = $seo['h1_accent'] ?? null)

    <section class="page-head show-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ config('emplowise.copy.layout.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <span>Documentation</span>
            </nav>
            <p class="kicker">Documentation</p>
            <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
            <p class="lede">{{ $seo['intro'] }}</p>
        </div>
    </section>

    <nav class="show-jump" aria-label="On this page">
        <div class="shell shell--narrow show-jump-inner">
            <span class="show-jump-label">On this page</span>
            <ul>
                @foreach ($index['groups'] as $group)
                    <li><a href="#{{ $group['id'] }}">{{ $group['kicker'] }}</a></li>
                @endforeach
                <li><a href="#notes">Operational notes</a></li>
            </ul>
        </div>
    </nav>

    <section class="section section--tight">
        <div class="shell shell--narrow docs-index-head">
            <p class="kicker">Index</p>
            <h2>{{ $seo['h2'] }}</h2>
            <p class="lede">{{ $index['intro'] }}</p>
        </div>
    </section>

    @foreach ($index['groups'] as $group)
        <section class="section{{ $loop->even ? ' section--alt' : '' }}" id="{{ $group['id'] }}">
            <div class="shell shell--narrow">
                <p class="kicker">{{ $group['kicker'] }}</p>
                <h2>{{ $group['heading'] }}</h2>
                <p class="lede show-lede">{{ $group['intro'] }}</p>
                <div class="show-grid show-grid--3 docs-grid">
                    @foreach ($group['items'] as $item)
                        <article class="show-card docs-card">
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['desc'] }}</p>
                            <div class="docs-actions">
                                @isset($item['route'])
                                    <a class="btn btn--ghost btn--sm" href="{{ route($item['route']) }}">{{ $item['route_label'] }}</a>
                                @endisset
                                <a class="btn btn--teal btn--sm" href="{{ asset_v($item['path']) }}" download>{{ $item['download_label'] }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    <section class="section" id="notes">
        <div class="shell shell--narrow">
            <p class="kicker">{{ $index['notes']['kicker'] }}</p>
            <h2>{{ $index['notes']['heading'] }}</h2>
            <p class="lede show-lede">{{ $index['notes']['body'] }}</p>
            <ul class="docs-notes">
                @foreach ($index['notes']['items'] as $note)
                    <li>
                        <code>{{ $note['file'] }}</code>
                        <span>{{ $note['desc'] }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="show-note">
                <p>{{ $index['gate'] }}</p>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <div>
                <h2>{{ $index['cta']['heading'] }}</h2>
                <p>{{ $index['cta']['body'] }}</p>
            </div>
            <div class="cta-actions">
                <a href="{{ route('contact') }}" class="btn btn--teal">{{ $index['cta']['primary'] }}</a>
                <a href="{{ route('faq') }}" class="btn btn--outline-light">{{ $index['cta']['secondary'] }}</a>
            </div>
        </div>
    </section>
@endsection
