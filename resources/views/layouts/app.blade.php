<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#081B3E">
    @php
        $pageTitle = trim($__env->yieldContent('title', 'Emplowise | Remote HR Solutions for Growing Businesses'));
        $pageDescription = trim($__env->yieldContent('meta_description', 'Emplowise runs remote HR for startups and SMEs in Pakistan. Payroll, performance management, and policy making, plus a free student career toolkit.'));
        $pageUrl = url()->current();
        $offers = collect(config('emplowise.packages'))->map(fn ($package) => [
            '@type' => 'Offer',
            'name' => $package['name'],
            'description' => $package['capacity'].'. '.$package['summary'],
            'priceCurrency' => 'PKR',
            'price' => $package['price_pkr'] ?? 'Custom',
            'url' => route('pricing'),
        ])->values()->all();
        $schema = [
            '@type' => ['Organization', 'ProfessionalService'],
            'name' => config('emplowise.brand.legal_name'),
            'url' => url('/'),
            'description' => config('emplowise.mission.statement'),
            'areaServed' => ['@type' => 'Country', 'name' => config('emplowise.brand.market')],
            'knowsAbout' => array_column(config('emplowise.services'), 'name'),
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'Emplowise monthly retainers',
                'itemListElement' => $offers,
            ],
        ];
        $graph = [$schema];
        if (request()->routeIs('faq')) {
            $graph[] = [
                '@type' => 'FAQPage',
                'mainEntity' => collect(config('emplowise.faqs'))->flatMap(fn ($group) => $group['items'])
                    ->map(fn ($item) => [
                        '@type' => 'Question',
                        'name' => $item['q'],
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
                    ])->values()->all(),
            ];
        }
        $document = ['@context' => 'https://schema.org', '@graph' => $graph];
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $pageUrl }}">

    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:site_name" content="Emplowise">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    <link rel="icon" type="image/png" href="{{ asset_v('favicon.png') }}">
    <link rel="preload" href="/fonts/montserrat-e56e84e5-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/jakarta-fdb05bf0-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset_v('css/fonts.css') }}">

    <link rel="stylesheet" href="{{ asset_v('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset_v('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset_v('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset_v('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset_v('css/pages.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset_v('css/chat.css') }}">
    <link rel="stylesheet" href="{{ asset_v('css/inline.css') }}">
    <script type="application/ld+json">
        {!! json_encode($document, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<div class="page-bg" aria-hidden="true">
    <span class="aurora-1"></span>
    <span class="aurora-2"></span>
    <span class="aurora-3"></span>
</div>
<div class="grain" aria-hidden="true"></div>

<div class="scroll-progress" aria-hidden="true"><span id="scroll-progress-bar"></span></div>

<header class="site-header" id="site-header">
    <div class="shell header-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="Emplowise home">
            <img src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="1056" height="270" fetchpriority="high">
        </a>

        <nav class="main-nav" id="main-nav" aria-label="Primary">
            @foreach (config('emplowise.nav') as $item)
                <a href="{{ route($item['route']) }}"
                   class="nav-link {{ request()->routeIs($item['route']) ? 'is-current' : '' }}"
                   @if (request()->routeIs($item['route'])) aria-current="page" @endif>
                    {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('free-hr-audit') }}" class="nav-link nav-link--cta">Free HR Audit</a>
        </nav>

        <div class="header-actions">
            <a href="{{ route('free-hr-audit') }}" class="btn btn--primary btn--sm">Free HR Audit</a>
            <button class="nav-toggle" type="button" id="nav-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="Open menu">
                <span></span>
            </button>
        </div>
    </div>
</header>

<main id="main">
    @yield('content')
</main>

<footer class="site-footer">
    <div class="shell">
        <div class="footer-top">
            <div class="footer-brand">
                <img src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="1056" height="270" loading="lazy">
                <p class="footer-note">Remote HR solutions for growing businesses. Built on clarity, structure, and plain language.</p>
            </div>

            <div>
                <p class="footer-title">Company</p>
                <div class="footer-links">
                    <a href="{{ route('what-we-are') }}">What We Are</a>
                    <a href="{{ route('what-we-do') }}">What We Do</a>
                    <a href="{{ route('pricing') }}">Pricing</a>
                    <a href="{{ route('faq') }}">FAQ</a>
                </div>
            </div>

            <div>
                <p class="footer-title">Student Hub</p>
                <div class="footer-links">
                    <a href="{{ route('student-hub') }}#interview">Interview Prep</a>
                    <a href="{{ route('student-hub') }}#ats">ATS Score</a>
                    <a href="{{ route('student-hub') }}#resume">Resume Builder</a>
                    <a href="{{ route('student-hub') }}#profile">Profile Optimizer</a>
                    <a href="{{ route('student-hub') }}#prompts">Prompt Library</a>
                </div>
            </div>

            <div>
                <p class="footer-title">Start here</p>
                <div class="footer-links">
                    <a href="{{ route('free-hr-audit') }}">Free HR Audit</a>
                    <span>Market: {{ config('emplowise.brand.market') }}</span>
                    <span>{{ config('emplowise.brand.expansion') }}</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} Emplowise HR Services. All rights reserved.</span>
            <span>Scope details are confirmed in writing after your Free HR Audit. Nothing on this site is a legal or compliance guarantee.</span>
        </div>
    </div>
</footer>

@include('partials.chat')

<button class="to-top" id="to-top" type="button" aria-label="Back to top">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script src="{{ asset_v('js/app.js') }}" defer></script>
<script src="{{ asset_v('js/chat.js') }}" defer></script>
@stack('scripts')
</body>
</html>
