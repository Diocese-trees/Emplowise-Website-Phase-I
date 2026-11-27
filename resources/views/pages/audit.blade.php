@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@section('content')
    <section class="page-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ config('emplowise.copy.layout.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $copy['breadcrumb'] }}</span>
            </nav>
            <p class="kicker">{{ $copy['kicker'] }}</p>
            @php($h1Text = $seo['h1'] ?? '')
            @php($h1Accent = $seo['h1_accent'] ?? null)
            <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
            <p class="lede">
                {{ $seo['intro'] }}
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell audit-layout">
            <div>
                <form class="wizard" id="audit-form" data-endpoint="{{ route('api.audit.store') }}" data-fallback-email="{{ config('emplowise.copy.contact.email') }}" novalidate>
                    <div class="wizard-head">
                        <span class="wizard-step-label" id="wizard-label" aria-live="polite" aria-atomic="true">Step 1 of 4 · Contact</span>
                        <span class="wizard-dots" id="wizard-dots" aria-hidden="true"><i class="is-current"></i><i></i><i></i><i></i></span>
                        <span class="wizard-step-label" id="wizard-count">01 / 04</span>
                    </div>
                    <div class="wizard-progress" aria-hidden="true"><span id="wizard-progress"></span></div>

                    {{-- Step 1 ------------------------------------------------- --}}
                    <div class="wizard-step is-active" data-step="1">
                        <h2>{{ $copy['steps'][0]['heading'] }}</h2>
                        <p class="step-note">{{ $copy['steps'][0]['note'] }}</p>

                        <div class="form-grid">
                            <div class="field" data-field="contact_name">
                                <label for="audit-name">{{ $copy['form']['name'] }}</label>
                                <input id="audit-name" name="contact_name" type="text" autocomplete="name" enterkeyhint="next" maxlength="120" placeholder="Your full name" required>
                                <span class="field-error" role="alert"></span>
                            </div>
                            <div class="field" data-field="company_name">
                                <label for="audit-company">{{ $copy['form']['company'] }}</label>
                                <input id="audit-company" name="company_name" type="text" autocomplete="organization" enterkeyhint="next" maxlength="160" placeholder="Company or venture name" required>
                                <span class="field-error" role="alert"></span>
                            </div>
                            <div class="field" data-field="email">
                                <label for="audit-email">{{ $copy['form']['email'] }}</label>
                                <input id="audit-email" name="email" type="email" inputmode="email" autocomplete="email" enterkeyhint="next" maxlength="160" placeholder="you@company.com" required>
                                <span class="field-error" role="alert"></span>
                            </div>
                            <div class="field" data-field="phone">
                                <label for="audit-phone">{{ $copy['form']['phone'] }}</label>
                                <input id="audit-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" enterkeyhint="next" maxlength="40" placeholder="+92 300 1234567" required>
                                <span class="field-error" role="alert"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2 ------------------------------------------------- --}}
                    <div class="wizard-step" data-step="2">
                        <h2>{{ $copy['steps'][1]['heading'] }}</h2>
                        <p class="step-note">{{ $copy['steps'][1]['note'] }}</p>

                        <div class="form-grid">
                            <div class="field field--full" data-field="headcount_band">
                                <label for="audit-headcount">{{ $copy['form']['headcount'] }}</label>
                                <select id="audit-headcount" name="headcount_band" required>
                                    <option value="">{{ $copy['form']['headcount_placeholder'] }}</option>
                                    @foreach ($headcountBands as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <span class="field-error" role="alert"></span>
                            </div>

                            <div class="field field--full" data-field="pain_points">
                                <span class="field-label">{{ $copy['form']['pain'] }}</span>
                                <div class="check-grid">
                                    @foreach ($painOptions as $value => $label)
                                        <label class="check">
                                            <input type="checkbox" name="pain_points[]" value="{{ $value }}">
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <span class="hint">{{ $copy['pain_hint'] }}</span>
                                <span class="field-error" role="alert"></span>
                            </div>

                            <div class="field field--full" data-field="notes">
                                <label for="audit-notes">{{ $copy['form']['notes'] }} <span class="muted">{{ $copy['form']['notes_optional'] }}</span></label>
                                <textarea id="audit-notes" name="notes" autocomplete="off" enterkeyhint="enter" maxlength="1500" placeholder="Current tools, recent incidents, a policy that is out of date, a review cycle that stalled."></textarea>
                                <span class="field-error" role="alert"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3 ------------------------------------------------- --}}
                    <div class="wizard-step" data-step="3">
                        <h2>{{ $copy['steps'][2]['heading'] }}</h2>
                        <p class="step-note">{{ $copy['steps'][2]['note'] }}</p>

                        <div class="form-grid">
                            <div class="field" data-field="preferred_date">
                                <label for="audit-date">{{ $copy['form']['date'] }}</label>
                                <input id="audit-date" name="preferred_date" type="date"
                                       min="{{ now()->toDateString() }}"
                                       max="{{ now()->addDays(60)->toDateString() }}"
                                       required>
                                <span class="hint">{{ str_replace(':date', now()->addDays(60)->format('d M Y'), $copy['form']['date_hint']) }}</span>
                                <span class="field-error" role="alert"></span>
                            </div>
                            <div class="field" data-field="preferred_slot">
                                <label for="audit-slot">{{ $copy['form']['window'] }}</label>
                                <select id="audit-slot" name="preferred_slot" required>
                                    <option value="">{{ $copy['form']['window_placeholder'] }}</option>
                                    @foreach ($slots as $slot)
                                        <option value="{{ $slot }}">{{ $slot }}</option>
                                    @endforeach
                                </select>
                                <span class="field-error" role="alert"></span>
                            </div>

                            <div class="field field--full" data-field="consent">
                                <label class="check">
                                    <input type="checkbox" name="consent" value="1" required>
                                    <span>{{ $copy['consent'] }}</span>
                                </label>
                                <span class="field-error" role="alert"></span>
                            </div>

                            <div class="sr-only" aria-hidden="true">
                                <label for="audit-website">{{ $copy['form']['website'] }}</label>
                                <input id="audit-website" name="website" type="text" tabindex="-1" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    {{-- Step 4 ------------------------------------------------- --}}
                    <div class="wizard-step" data-step="4">
                        <h2>{{ $copy['steps'][3]['heading'] }}</h2>
                        <p class="step-note">{{ $copy['steps'][3]['note'] }}</p>

                        <dl class="review-list" id="review-list"></dl>

                        <div class="field" data-field="submit">
                            <p class="field-error" id="submit-error" role="alert"></p>
                        </div>
                    </div>

                    <div class="wizard-actions">
                        <button type="button" class="btn btn--ghost" id="wizard-back" hidden>{{ $copy['buttons']['back'] }}</button>
                        <button type="button" class="btn btn--primary" id="wizard-next">{{ $copy['buttons']['next'] }}</button>
                        <button type="submit" class="btn btn--teal" id="wizard-submit" hidden>{{ $copy['buttons']['submit'] }}</button>
                    </div>

                    <p class="form-status s32" id="audit-status" role="status"></p>
                    <p class="wizard-trust">{{ $copy['trust_note'] }}</p>
                </form>

                <div class="success-panel" id="audit-success" role="status">
                    <span class="success-mark" aria-hidden="true">
                        <x-icon name="check" :size="26" :stroke="2.2" />
                    </span>
                    <p class="kicker s11">{{ $copy['success']['kicker'] }}</p>
                    <h2 class="s33">{{ $seo['h2'] }}</h2>
                    <p class="small s34">
                        {{ $copy['success']['body'] }}
                    </p>
                    <p class="success-ref" id="success-ref">{{ $copy['success']['reference_placeholder'] }}</p>
                    <div class="review-list">
                        <div><dt>{{ $copy['success']['date_label'] }}</dt><dd id="success-date">{{ $copy['success']['pending'] }}</dd></div>
                        <div><dt>{{ $copy['success']['slot_label'] }}</dt><dd id="success-slot">{{ $copy['success']['pending'] }}</dd></div>
                    </div>
                    <ul class="success-steps">
                        @foreach ($copy['success']['steps'] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('home') }}" class="btn btn--primary btn--sm s1">{{ $copy['success']['back'] }}</a>
                </div>
            </div>

            <div class="audit-aside">
                <div class="aside-card">
                    <h3>{{ $copy['aside']['covers_heading'] }}</h3>
                    <ul class="aside-list">
                        @foreach ($copy['aside']['covers'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="aside-card">
                    <h3>{{ $copy['aside']['prepare_heading'] }}</h3>
                    <ul class="aside-list">
                        @foreach ($copy['aside']['prepare'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="aside-card">
                    <h3>{{ $copy['aside']['not_heading'] }}</h3>
                    <p class="small s34">
                        {{ $copy['aside']['not_body'] }}
                    </p>
                </div>

                <div class="aside-card">
                    <h3>{{ $copy['aside']['packages_heading'] }}</h3>
                    <ul class="aside-list">
                        @foreach ($packages as $package)
                            <li>{{ $package['name'] }} · {{ $package['capacity'] }} · {{ $package['price_display'] }}</li>
                        @endforeach
                    </ul>
                    <p class="small muted s35">
                        {{ $copy['aside']['packages_note'] }}
                    </p>
                </div>
            </div>
        </div>
    </section>

@push('scripts')
    <script src="{{ asset_v('js/audit.js') }}" defer></script>
@endpush
@endsection
