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
                <li>Who we serve, and the four buying situations that bring a team to Emplowise.</li>
                <li>The four-stage method, and the document each stage leaves behind.</li>
                <li>The published retainers, the fourteen services, and the people who own the work.</li>
            </ul>
        </div>
    </section>
@endsection
