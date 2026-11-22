@php($ui = config('emplowise.copy.layout'))

<div class="consent" id="consent" role="dialog" aria-labelledby="consent-title" aria-describedby="consent-body" data-saved="{{ $ui['consent_saved'] }}" hidden>
    <div class="consent-head">
        <span class="consent-dot" aria-hidden="true"></span>
        <h2 id="consent-title">{{ $ui['consent_title'] }}</h2>
    </div>
    <p class="consent-body" id="consent-body">{{ $ui['consent_body'] }}</p>

    <div class="consent-actions">
        <button class="btn btn--teal btn--sm" type="button" data-consent-all>{{ $ui['consent_accept'] }}</button>
        <button class="btn btn--ghost btn--sm" type="button" data-consent-essential>{{ $ui['consent_essential'] }}</button>
        <button class="consent-manage" type="button" data-consent-manage aria-expanded="false" aria-controls="consent-prefs">{{ $ui['consent_manage'] }}</button>
    </div>

    <div class="consent-prefs" id="consent-prefs" hidden>
        <label class="consent-opt">
            <span class="consent-opt-copy">
                <span class="consent-opt-label">{{ $ui['consent_essential_label'] }}</span>
                <span class="consent-opt-desc">{{ $ui['consent_essential_desc'] }}</span>
            </span>
            <input class="switch" type="checkbox" checked disabled aria-label="{{ $ui['consent_essential_label'] }}">
        </label>

        <label class="consent-opt">
            <span class="consent-opt-copy">
                <span class="consent-opt-label">{{ $ui['consent_analytics_label'] }}</span>
                <span class="consent-opt-desc">{{ $ui['consent_analytics_desc'] }}</span>
            </span>
            <input class="switch" type="checkbox" data-consent-analytics aria-label="{{ $ui['consent_analytics_label'] }}">
        </label>

        <label class="consent-opt">
            <span class="consent-opt-copy">
                <span class="consent-opt-label">{{ $ui['consent_marketing_label'] }}</span>
                <span class="consent-opt-desc">{{ $ui['consent_marketing_desc'] }}</span>
            </span>
            <input class="switch" type="checkbox" data-consent-marketing aria-label="{{ $ui['consent_marketing_label'] }}">
        </label>

        <p class="consent-note">{{ $ui['consent_note'] }}</p>
        <button class="btn btn--primary btn--sm consent-save" type="button" data-consent-save>{{ $ui['consent_save'] }}</button>
    </div>
</div>
