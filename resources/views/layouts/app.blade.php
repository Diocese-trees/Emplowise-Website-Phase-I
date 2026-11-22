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
        $ogImage = trim($__env->yieldContent('og_image', ''));
        $ogImage = $ogImage !== '' ? $ogImage : asset_v('img/logo-primary.png');
        if (! str_starts_with($ogImage, 'http://') && ! str_starts_with($ogImage, 'https://')) {
            $ogRoot = rtrim((string) \Illuminate\Support\Facades\URL::to('/'), '/');
            if (! str_starts_with($ogRoot, 'http://') && ! str_starts_with($ogRoot, 'https://')) {
                $ogScheme = parse_url($pageUrl, PHP_URL_SCHEME) ?: 'https';
                $ogHost = parse_url($pageUrl, PHP_URL_HOST) ?: '';
                $ogPort = parse_url($pageUrl, PHP_URL_PORT);
                $ogRoot = $ogScheme.'://'.$ogHost.($ogPort ? ':'.$ogPort : '');
            }
            $ogImage = $ogRoot.'/'.ltrim($ogImage, '/');
        }
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
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="Emplowise logo">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="Emplowise logo">

    <link rel="icon" href="{{ asset_v('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset_v('favicon.png') }}">
    <link rel="preload" href="/fonts/montserrat-e56e84e5-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/jakarta-fdb05bf0-latin.woff2" as="font" type="font/woff2" crossorigin>
    {{-- Inline so data-theme is on <html> before the first paint. Its exact
         bytes are whitelisted by a SHA-256 hash in the CSP; see
         App\Support\ThemeBootstrap. Toggles and storage live in theme.js. --}}
    {!! \App\Support\ThemeBootstrap::tag() !!}
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

<div class="scroll-progress" aria-hidden="true"><span id="scroll-progress-bar"></span></div>

@include('partials.header')

<main id="main" tabindex="-1">
    @yield('content')
</main>

@include('partials.footer')

@include('partials.chat')
@include('partials.consent')

<button class="to-top" id="to-top" type="button" aria-label="{{ $ui['back_to_top'] }}">
    <x-icon name="arrow-up" :size="18" />
</button>


<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script src="{{ asset_v('js/theme.js') }}" defer></script>
<script src="{{ asset_v('js/app.js') }}" defer></script>
<script src="{{ asset_v('js/consent.js') }}" defer></script>
<script src="{{ asset_v('js/chat.js') }}" defer></script>
@stack('scripts')
</body>
</html>
