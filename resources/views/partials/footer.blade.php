<footer class="site-footer">
    <div class="shell">
        <div class="footer-top">
            <div class="footer-brand">
                <picture>
                    <source type="image/webp" srcset="{{ asset_v('img/logo-reverse-102.webp') }} 1x, {{ asset_v('img/logo-reverse-204.webp') }} 2x, {{ asset_v('img/logo-reverse-306.webp') }} 3x">
                    <img src="{{ asset_v('img/logo-reverse-306.png') }}" alt="Emplowise" width="306" height="78" loading="lazy" decoding="async">
                </picture>
                <p class="footer-note">{{ $ui['footer_note'] }}</p>
                <a href="{{ route('free-hr-audit') }}" class="footer-cta">{{ $ui['nav_audit_label'] }}</a>
                <a class="footer-meta footer-mail" href="mailto:{{ config('emplowise.copy.contact.email') }}">{{ config('emplowise.copy.contact.email') }}</a>
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
                <a class="footer-meta footer-mail" href="mailto:{{ config('emplowise.copy.contact.email') }}">{{ config('emplowise.copy.contact.email') }}</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-fine">
                <span>&copy; {{ date('Y') }} {{ config('emplowise.brand.legal_name') }}. {{ $ui['footer_rights'] }}</span>
                <span class="footer-legal">{{ $ui['footer_tagline'] }}</span>
            </p>
            <nav class="footer-legal-links" aria-label="{{ $ui['footer_legal_label'] }}">
                <a href="{{ route('terms') }}">{{ $ui['footer_terms'] }}</a>
                <a href="{{ route('privacy') }}">{{ $ui['footer_privacy'] }}</a>
                <button type="button" data-consent-open>{{ $ui['consent_footer'] }}</button>
            </nav>
        </div>
    </div>
</footer>
