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
                <a class="btn btn--ghost" href="{{ route('free-hr-audit') }}">Book a free HR audit</a>
            </div>
            <p class="lede">{{ $page['download']['meta'] }}</p>
        </div>
    </section>

    <section class="section section--tight">
        <div class="shell shell--narrow">
            <h2>{{ $seo['h2'] }}</h2>
            <ul>
                <li>Product, problem and method, in the order a buyer raises them.</li>
                <li>Pricing, the market wedge and the go-to-market path.</li>
                <li>What is shipped, and what the roadmap still owes.</li>
            </ul>
        </div>
    </section>
@endsection
