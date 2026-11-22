@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@section('content')
    <section class="page-head page-head--legal">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ config('emplowise.copy.layout.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $kind === 'terms' ? config('emplowise.copy.layout.footer_terms') : config('emplowise.copy.layout.footer_privacy') }}</span>
            </nav>
            <p class="kicker">{{ $kind === 'terms' ? 'Legal · Terms' : 'Legal · Privacy' }}</p>
            @php($h1Text = $seo['h1'] ?? '')
            @php($h1Accent = $seo['h1_accent'] ?? null)
            <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
            <p class="lede">{{ $seo['intro'] }}</p>
            <p class="legal-updated">Last updated {{ $copy['updated'] }}</p>
        </div>
    </section>

    <section class="section section--tight legal-section">
        <div class="shell legal-layout">
            <nav class="legal-toc" aria-label="{{ $copy['toc_label'] }}">
                <p class="legal-toc-title">{{ $copy['toc_label'] }}</p>
                <ol>
                    @foreach ($copy['sections'] as $index => $section)
                        <li>
                            <a href="#{{ $section['id'] }}">
                                <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                {{ $section['heading'] }}
                            </a>
                        </li>
                    @endforeach
                </ol>
                <a class="legal-toc-mail" href="mailto:{{ config('emplowise.copy.contact.email') }}">
                    {{ config('emplowise.copy.contact.email') }}
                </a>
            </nav>

            <article class="legal-body">
                @foreach ($copy['sections'] as $index => $section)
                    <section id="{{ $section['id'] }}" class="legal-block">
                        <span class="legal-num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h2>{{ $section['heading'] }}</h2>
                        @foreach ($section['paragraphs'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </section>
                @endforeach

                <div class="legal-actions">
                    <a class="btn btn--primary" href="{{ route('free-hr-audit') }}">
                        {{ config('emplowise.copy.layout.nav_audit_label') }}
                        <x-icon name="arrow-right" :size="16" />
                    </a>
                    <a class="btn btn--ghost" href="{{ route($kind === 'terms' ? 'privacy' : 'terms') }}">
                        {{ $kind === 'terms' ? config('emplowise.copy.layout.footer_privacy') : config('emplowise.copy.layout.footer_terms') }}
                    </a>
                    <a class="btn btn--ghost" href="{{ route('contact') }}">
                        Contact us
                    </a>
                </div>

                <p class="legal-note">
                    This page is a summary written for clients, not a substitute for a signed scope.
                    Questions go to
                    <a href="mailto:{{ config('emplowise.copy.contact.email') }}">{{ config('emplowise.copy.contact.email') }}</a>.
                </p>
            </article>
        </div>
    </section>
@endsection
