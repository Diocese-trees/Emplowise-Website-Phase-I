@php
    $nav = config('emplowise.nav');
    $home = $nav[0];
    $emplostart = $nav[4];
    $contact = $nav[6];
    $company = array_values(array_filter(config('emplowise.footer_company'), function ($item) {
        return in_array($item['route'], ['what-we-are', 'what-we-do', 'why-emplowise', 'what-we-are-building'], true);
    }));
@endphp
<header class="site-header" id="site-header">
    <div class="shell header-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="{{ $ui['logo_alt'] }}">
            {{-- Right-sized derivatives: the wordmark renders at 125x32, so a
                 1056px PNG only cost bytes. WebP first, PNG fallback. Width and
                 height are the display size, which keeps the aspect ratio and
                 stops layout shift. The dark variant is the reverse mark. --}}
            <picture class="brand-logo brand-logo--light">
                <source type="image/webp" srcset="{{ asset_v('img/logo-primary-125.webp') }} 1x, {{ asset_v('img/logo-primary-250.webp') }} 2x">
                <img src="{{ asset_v('img/logo-primary-375.png') }}" alt="Emplowise" width="125" height="32" decoding="async">
            </picture>
            <picture class="brand-logo brand-logo--dark">
                <source type="image/webp" srcset="{{ asset_v('img/logo-reverse-125.webp') }} 1x, {{ asset_v('img/logo-reverse-204.webp') }} 2x, {{ asset_v('img/logo-reverse-306.webp') }} 3x">
                <img src="{{ asset_v('img/logo-reverse-306.png') }}" alt="Emplowise" width="125" height="32" decoding="async">
            </picture>
        </a>

        <nav class="main-nav" id="main-nav" aria-label="Primary">
            <a href="{{ route($home['route']) }}"
               class="nav-link {{ request()->routeIs($home['route']) ? 'is-current' : '' }}"
               @if (request()->routeIs($home['route'])) aria-current="page" @endif>
                {{ $home['label'] }}
            </a>

            <details class="nav-group">
                <summary class="nav-link nav-group-toggle">
                    <span>Company</span>
                    <span class="nav-group-caret" aria-hidden="true"></span>
                </summary>
                <div class="nav-group-panel">
                    @foreach ($company as $item)
                        @php($hasHash = isset($item['hash']))
                        <a href="{{ route($item['route']) }}{{ $hasHash ? '#'.$item['hash'] : '' }}"
                           class="nav-link nav-link--sub {{ ! $hasHash && request()->routeIs($item['route']) ? 'is-current' : '' }}"
                           @if (! $hasHash && request()->routeIs($item['route'])) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </details>

            <a href="{{ route($emplostart['route']) }}"
               class="nav-link nav-link--student {{ request()->routeIs($emplostart['route']) ? 'is-current' : '' }}"
               @if (request()->routeIs($emplostart['route'])) aria-current="page" @endif>
                {{ $emplostart['label'] }}
            </a>

            <a href="{{ route($contact['route']) }}"
               class="nav-link {{ request()->routeIs($contact['route']) ? 'is-current' : '' }}"
               @if (request()->routeIs($contact['route'])) aria-current="page" @endif>
                {{ $contact['label'] }}
            </a>

            {{-- The header toggle is desktop-only; in the drawer this row keeps
                 the control reachable without crowding the 320px header. --}}
            <button class="nav-theme" type="button" data-theme-toggle aria-pressed="false"
                    data-label-dark="{{ $ui['theme_toggle_dark'] }}"
                    data-label-light="{{ $ui['theme_toggle_light'] }}"
                    data-text-dark="{{ $ui['theme_footer_dark'] }}"
                    data-text-light="{{ $ui['theme_footer_light'] }}">
                <x-icon name="moon" class="theme-glyph icon-moon" :size="17" />
                <x-icon name="sun" class="theme-glyph icon-sun" :size="17" />
                <span data-theme-text>{{ $ui['theme_footer_dark'] }}</span>
            </button>
        </nav>

        <div class="header-actions">
            <button class="theme-toggle" type="button" data-theme-toggle aria-pressed="false"
                    data-label-dark="{{ $ui['theme_toggle_dark'] }}"
                    data-label-light="{{ $ui['theme_toggle_light'] }}">
                <span class="theme-glyph icon-moon"><x-icon name="moon" :size="18" /></span>
                <span class="theme-glyph icon-sun"><x-icon name="sun" :size="18" /></span>
            </button>
            <a href="{{ route('free-hr-audit') }}" class="btn btn--primary btn--sm">{{ $ui['nav_audit_label'] }}</a>
            <button class="nav-toggle" type="button" id="nav-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="{{ $ui['menu_open'] }}">
                <span></span>
            </button>
        </div>
    </div>
</header>
