@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@push('head')
    <meta name="robots" content="{{ $page['robots'] }}">
@endpush

@section('content')
    <section class="page-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ config('emplowise.copy.layout.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $page['kicker'] }}</span>
            </nav>
            <p class="kicker">{{ $page['kicker'] }}</p>
            <h1>{{ $seo['h1'] }}</h1>
            <p class="lede">{{ $seo['intro'] }}</p>
            <div class="hero-actions">
                <a class="btn btn--teal" href="{{ asset_v($page['download']['path']) }}" download>
                    {{ $page['download']['label'] }}
                </a>
                <a class="btn btn--ghost" href="{{ route('contact') }}">Talk to the team</a>
            </div>
            <p class="lede">{{ $page['download']['meta'] }}</p>
        </div>
    </section>

    <section class="section section--tight">
        <div class="shell shell--narrow">
            <h2>{{ $seo['h2'] }}</h2>
            <ul>
                <li>The Phase I platform, with the route, test and export proof behind it.</li>
                <li>EmploStart and the twenty-two parity fixtures that keep the tools honest.</li>
                <li>The Free HR Audit wizard, the 2026 brand system, and the evidence standard.</li>
            </ul>
        </div>
    </section>
@endsection
