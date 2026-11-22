<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#081B3E">
    @php
        $ui = config('emplowise.copy.layout');
        $pageTitle = trim($__env->yieldContent('title', 'Emplowise | Remote HR Solutions for Growing Businesses'));
        $pageDescription = trim($__env->yieldContent('meta_description', 'Emplowise runs remote HR for startups and SMEs in Pakistan. Payroll, performance management, and policy making, plus a free career toolkit for early career candidates.'));
        $pageUrl = url()->current();
        $offers = collect(config('emplowise.packages'))->map(fn ($package) => [
            '@type' => 'Offer',
            'name' => $package['name'],
            'description' => $package['capacity'].'. '.$package['summary'],
            'priceCurrency' => 'PKR',
            'price' => $package['price_pkr'] ?? 'Custom',
            'url' => route('why-emplowise'),
        ])->values()->all();
        $schema = [
            '@type' => ['Organization', 'ProfessionalService'],
            'name' => config('emplowise.brand.legal_name'),
            'url' => url('/'),
            'description' => config('emplowise.brand.description'),
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
    <link rel="stylesheet" href="{{ asset_v('css/site.css') }}">
    @stack('styles')
    <script type="application/ld+json">
        {!! json_encode($document, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
</head>
@php
    $bodyClass = match (true) {
        request()->routeIs('home') => 'page-home',
        request()->routeIs('contact') => 'page-contact',
        request()->routeIs('what-we-are') => 'page-team',
        request()->routeIs('what-we-are-building') => 'page-ai',
        default => '',
    };
@endphp
<body class="{{ $bodyClass }}">
<a class="skip-link" href="#main">{{ $ui['skip_link'] }}</a>

<div class="page-bg" aria-hidden="true">
    <span class="aurora-1"></span>
    <span class="aurora-2"></span>
    <span class="aurora-3"></span>
</div>
<div class="grain" aria-hidden="true"></div>

<div class="scroll-progress" aria-hidden="true"><span id="scroll-progress-bar"></span></div>

<header class="site-header" id="site-header">
    <div class="shell header-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="{{ $ui['logo_alt'] }}">
            <img src="{{ asset_v('img/logo-primary.png') }}" alt="Emplowise" width="1056" height="270" fetchpriority="high">
        </a>

        <nav class="main-nav" id="main-nav" aria-label="Primary">
            @foreach (config('emplowise.nav') as $item)
                @php($hasHash = isset($item['hash']))
                <a href="{{ route($item['route']) }}{{ $hasHash ? '#'.$item['hash'] : '' }}"
                   class="nav-link {{ ! $hasHash && request()->routeIs($item['route']) ? 'is-current' : '' }}"
                   @if (! $hasHash && request()->routeIs($item['route'])) aria-current="page" @endif>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="header-actions">
            <a href="{{ route('free-hr-audit') }}" class="btn btn--primary btn--sm">{{ $ui['nav_audit_label'] }}</a>
            <button class="nav-toggle" type="button" id="nav-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="{{ $ui['menu_open'] }}">
                <span></span>
            </button>
        </div>
    </div>
</header>

<main id="main" tabindex="-1">
    @yield('content')
</main>

<footer class="site-footer">
    <div class="shell">
        <div class="footer-top">
            <div class="footer-brand">
                <img src="{{ asset_v('img/logo-reverse.png') }}" alt="Emplowise" width="1056" height="270" loading="lazy">
                <p class="footer-note">{{ $ui['footer_note'] }}</p>
                <a href="{{ route('free-hr-audit') }}" class="footer-cta">{{ $ui['nav_audit_label'] }}</a>
            </div>

            <nav class="footer-col" aria-label="{{ $ui['footer_company_title'] }}">
                <p class="footer-title">{{ $ui['footer_company_title'] }}</p>
                <div class="footer-links">
                    @foreach (config('emplowise.footer_company') as $item)
                        <a href="{{ route($item['route']) }}{{ isset($item['hash']) ? '#'.$item['hash'] : '' }}">{{ $item['label'] }}</a>
                    @endforeach
                </div>
            </nav>

            <nav class="footer-col" aria-label="{{ $ui['footer_resources_title'] }}">
                <p class="footer-title">{{ $ui['footer_resources_title'] }}</p>
                <div class="footer-links">
                    @foreach (config('emplowise.footer_resources') as $item)
                        <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                    @endforeach
                </div>
            </nav>

            <div class="footer-col footer-col--meta">
                <p class="footer-title">{{ $ui['footer_start_title'] }}</p>
                <p class="footer-meta">{{ $ui['footer_market'] }} {{ config('emplowise.brand.market') }}</p>
                <p class="footer-meta">{{ config('emplowise.brand.expansion') }}</p>
                <a class="footer-meta footer-mail" href="mailto:{{ config('emplowise.copy.contact.email') }}">{{ config('emplowise.copy.contact.email') }}</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ config('emplowise.brand.legal_name') }}. {{ $ui['footer_rights'] }}</span>
            <span class="footer-legal">{{ $ui['footer_legal'] }}</span>
        </div>
    </div>
</footer>

@include('partials.chat')

<button class="to-top" id="to-top" type="button" aria-label="{{ $ui['back_to_top'] }}">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script src="{{ asset_v('js/app.js') }}" defer></script>
<script src="{{ asset_v('js/chat.js') }}" defer></script>
@stack('scripts')
</body>
</html>
