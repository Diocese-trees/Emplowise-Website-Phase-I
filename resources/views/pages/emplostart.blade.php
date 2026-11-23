@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@push('styles')
    <link rel="stylesheet" href="{{ asset_v('css/hub.css') }}">
@endpush

@section('content')
    <div id="emplostart-config"
         data-interview-questions="{{ route('api.student.interview.questions') }}"
         data-interview-feedback="{{ route('api.student.interview.feedback') }}"
         data-ats="{{ route('api.student.ats') }}"
         data-profile="{{ route('api.student.profile') }}"
         hidden></div>

    <section class="hub-banner">
        <div class="shell">
            <p class="kicker">{{ $copy['kicker'] }}</p>
            @php($h1Text = $seo['h1'] ?? '')
            @php($h1Accent = $seo['h1_accent'] ?? null)
            <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
            <p class="lede">{{ $seo['intro'] }}</p>
            <div class="hub-stats">
                <div class="hub-stat"><b>{{ count($modules) }}</b><span>{{ $copy['stats'][0] }}</span></div>
                <div class="hub-stat"><b>0</b><span>{{ $copy['stats'][1] }}</span></div>
                <div class="hub-stat"><b>0</b><span>{{ $copy['stats'][2] }}</span></div>
                <div class="hub-stat"><b>{{ count($student['prompts']) }}</b><span>{{ $copy['stats'][3] }}</span></div>
            </div>
        </div>
    </section>

    <section class="section section--tight module-map-section" id="modules">
        <div class="shell">
            <p class="kicker">{{ $copy['modules_kicker'] }}</p>
            <h2>{{ $copy['modules_heading'] }}</h2>
            <p class="lede module-map-lede">{{ $copy['modules_lede'] }}</p>
            <ol class="module-map">
                @foreach ($modules as $module)
                    <li class="module-card {{ $module['status'] === 'Live' ? 'is-live' : '' }}">
                        <span class="module-num">{{ $module['num'] }}</span>
                        <h3>{{ $module['title'] }}</h3>
                        <p>{{ $module['desc'] }}</p>
                        @if ($module['status'] === 'Live')
                            <a class="module-status" href="#{{ $module['id'] }}">{{ $copy['modules_live'] }}</a>
                        @else
                            <span class="module-status module-status--build">{{ $copy['modules_next'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <div class="shell hub-intro">
        <p class="kicker">{{ $copy['modules_live'] }}</p>
        <h2>{{ $copy['tools_heading'] }}</h2>
    </div>

    <div class="shell hub-shell">
        <div class="hub-rail" role="tablist" aria-label="EmploStart tools">
            @php($tabs = $copy['tabs'])
            @foreach ($tabs as $tab)
                <button type="button"
                        class="hub-tab {{ $loop->first ? 'is-active' : '' }}"
                        id="tab-{{ $tab['id'] }}"
                        data-hub-tab="{{ $tab['id'] }}"
                        role="tab"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        aria-controls="panel-{{ $tab['id'] }}"
                        tabindex="{{ $loop->first ? '0' : '-1' }}">
                    <span class="hub-tab-num">{{ $tab['num'] }}</span>
                    <span>
                        <span class="hub-tab-title">{{ $tab['title'] }}</span>
                        <span class="hub-tab-desc">{{ $tab['desc'] }}</span>
                    </span>
                </button>
            @endforeach
        </div>

        <div>
            {{-- 01 Interview ------------------------------------------------- --}}
            @include('partials.emplostart.interview', ['copy' => $copy, 'student' => $student])

            {{-- 02 ATS ------------------------------------------------------- --}}
            @include('partials.emplostart.ats', ['copy' => $copy, 'seo' => $seo])

            {{-- 03 Resume builder ------------------------------------------- --}}
            <section class="hub-panel" id="panel-resume" data-hub-panel="resume" role="tabpanel" aria-labelledby="tab-resume">
                <div class="panel-head">
                    <div>
                        <h2>{{ $copy['resume']['heading'] }}</h2>
                        <p>{{ $copy['resume']['body'] }}</p>
                    </div>
                    <span class="tool-status">{{ $copy['resume']['status'] }}</span>
                </div>

                <div class="resume-layout">
                    <form class="resume-form panel-card" id="resume-form">
                        <div class="form-grid">
                            <div class="field">
                                <label for="res-name">{{ $copy['resume']['form']['name'] }}</label>
                                <input id="res-name" data-rp="name" type="text" autocomplete="name" enterkeyhint="next" placeholder="Ayesha Khan" maxlength="80">
                            </div>
                            <div class="field">
                                <label for="res-title">{{ $copy['resume']['form']['title'] }}</label>
                                <input id="res-title" data-rp="title" type="text" autocomplete="organization-title" enterkeyhint="next" placeholder="Data Analyst" maxlength="80">
                            </div>
                            <div class="field">
                                <label for="res-email">{{ $copy['resume']['form']['email'] }}</label>
                                <input id="res-email" data-rp="email" type="email" inputmode="email" autocomplete="email" enterkeyhint="next" placeholder="you@example.com" maxlength="120">
                            </div>
                            <div class="field">
                                <label for="res-phone">{{ $copy['resume']['form']['phone'] }}</label>
                                <input id="res-phone" data-rp="phone" type="text" inputmode="tel" autocomplete="tel" enterkeyhint="next" placeholder="+92 300 1234567" maxlength="40">
                            </div>
                            <div class="field">
                                <label for="res-location">{{ $copy['resume']['form']['location'] }}</label>
                                <input id="res-location" data-rp="location" type="text" autocomplete="address-level2" enterkeyhint="next" placeholder="Karachi, Pakistan" maxlength="80">
                            </div>
                            <div class="field">
                                <label for="res-linkedin">{{ $copy['resume']['form']['linkedin'] }}</label>
                                <input id="res-linkedin" data-rp="linkedin" type="text" inputmode="url" autocomplete="url" enterkeyhint="done" placeholder="linkedin.com/in/yourname" maxlength="120">
                            </div>
                        </div>

                        <div class="field">
                            <label for="res-summary">{{ $copy['resume']['form']['summary'] }}</label>
                            <textarea id="res-summary" data-rp="summary" autocomplete="off" enterkeyhint="enter" placeholder="Three lines: what you do, strongest proof, what you want." maxlength="700"></textarea>
                        </div>

                        <div>
                            <div class="panel-head s24">
                                <h3 class="s25">{{ $copy['resume']['form']['experience'] }}</h3>
                                <button type="button" class="btn btn--ghost btn--sm" id="add-exp">{{ $copy['resume']['add_role'] }}</button>
                            </div>
                            <div class="repeat-list" id="exp-list"></div>
                        </div>

                        <div>
                            <div class="panel-head s24">
                                <h3 class="s25">{{ $copy['resume']['form']['education'] }}</h3>
                                <button type="button" class="btn btn--ghost btn--sm" id="add-edu">{{ $copy['resume']['add_education'] }}</button>
                            </div>
                            <div class="repeat-list" id="edu-list"></div>
                        </div>

                        <div class="field">
                            <label for="res-skills">{{ $copy['resume']['form']['skills'] }}</label>
                            <textarea class="s26" id="res-skills" data-rp="skills" autocomplete="off" enterkeyhint="done" placeholder="Excel, SQL, Python, stakeholder communication" maxlength="400"></textarea>
                            <span class="hint">{{ $copy['resume']['skills_hint'] }}</span>
                        </div>

                        <div class="resume-actions">
                            <button type="button" class="btn btn--ghost btn--sm" id="resume-example">{{ $copy['resume']['example'] }}</button>
                            <button type="button" class="btn btn--ghost btn--sm" id="resume-clear">{{ $copy['resume']['clear'] }}</button>
                            <span class="small muted" id="resume-save" role="status"></span>
                        </div>
                        <p class="hint">{{ $copy['resume']['save_note'] }}</p>
                    </form>

                    <div class="resume-preview-wrap">
                        <div class="resume-toolbar">
                            <button type="button" class="btn btn--primary btn--sm" id="resume-print">{{ $copy['resume']['print'] }}</button>
                            <button type="button" class="btn btn--ghost btn--sm" id="resume-download">{{ $copy['resume']['download'] }}</button>
                            <button type="button" class="btn btn--ghost btn--sm" id="resume-copy">{{ $copy['resume']['copy'] }}</button>
                        </div>

                        <div class="resume-paper" id="resume-preview" aria-label="Resume preview">
                            <p class="rp-name" id="rp-name">{{ $copy['resume']['preview']['name'] }}</p>
                            <p class="rp-title" id="rp-title">{{ $copy['resume']['preview']['title'] }}</p>
                            <p class="rp-contact" id="rp-contact">{{ $copy['resume']['preview']['contact'] }}</p>

                            <div class="rp-section" id="rp-summary-section">
                                <h3>{{ $copy['resume']['preview']['summary_section'] }}</h3>
                                <p id="rp-summary" class="rp-empty">{{ $copy['resume']['preview']['summary_empty'] }}</p>
                            </div>

                            <div class="rp-section">
                                <h3>{{ $copy['resume']['preview']['experience_section'] }}</h3>
                                <div id="rp-experience"><p class="rp-empty">{{ $copy['resume']['preview']['experience_empty'] }}</p></div>
                            </div>

                            <div class="rp-section">
                                <h3>{{ $copy['resume']['preview']['education_section'] }}</h3>
                                <div id="rp-education"><p class="rp-empty">{{ $copy['resume']['preview']['education_empty'] }}</p></div>
                            </div>

                            <div class="rp-section">
                                <h3>{{ $copy['resume']['preview']['skills_section'] }}</h3>
                                <p id="rp-skills" class="rp-empty">{{ $copy['resume']['preview']['skills_empty'] }}</p>
                            </div>
                        </div>
                        <p class="rp-print-note">{{ $copy['resume']['preview_note'] }}</p>
                    </div>
                </div>
            </section>

            {{-- 04 Profile optimizer ---------------------------------------- --}}
            @include('partials.emplostart.profile', ['copy' => $copy])

            {{-- 05 Prompt library ------------------------------------------ --}}
            <section class="hub-panel" id="panel-prompts" data-hub-panel="prompts" role="tabpanel" aria-labelledby="tab-prompts">
                <div class="panel-head">
                    <div>
                        <h2>{{ $copy['prompts']['heading'] }}</h2>
                        <p>{{ str_replace(':count', count($student['prompts']), $copy['prompts']['body']) }}</p>
                    </div>
                    <span class="tool-status">{{ $copy['prompts']['status'] }}</span>
                </div>

                <div class="prompt-toolbar">
                    <div class="search-box" id="prompt-search-box">
                        <x-icon name="search" :size="16" />
                        <label class="sr-only" for="prompt-search">{{ $copy['prompts']['search_label'] }}</label>
                        <input id="prompt-search" type="search" autocomplete="off" enterkeyhint="search" placeholder="{{ $copy['prompts']['search_placeholder'] }}">
                        <button type="button" class="search-clear" id="prompt-clear" aria-label="{{ $copy['prompts']['clear_search'] }}" hidden>&times;</button>
                    </div>
                    <div class="filter-row" id="prompt-filters">
                        <button type="button" class="filter-btn is-active" data-prompt-filter="all">{{ $copy['filter_all'] ?? 'All' }} <span class="filter-count">{{ count($student['prompts']) }}</span></button>
                        @foreach (array_unique(array_column($student['prompts'], 'category')) as $category)
                            <button type="button" class="filter-btn" data-prompt-filter="{{ $category }}">{{ $category }} <span class="filter-count">{{ collect($student['prompts'])->where('category', $category)->count() }}</span></button>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn--ghost btn--sm" id="prompt-copy-visible">{{ $copy['prompts']['copy_visible'] }}</button>
                </div>

                <div class="prompt-grid" id="prompt-grid">
                    @foreach ($student['prompts'] as $prompt)
                        <article class="prompt-card" data-category="{{ $prompt['category'] }}" data-prompt>
                            <span class="prompt-cat">{{ $prompt['category'] }}</span>
                            <h3>{{ $prompt['title'] }}</h3>
                            <p class="prompt-use">{{ $prompt['use'] }}</p>
                            <p class="prompt-text">{{ $prompt['text'] }}</p>
                            <div class="prompt-actions">
                                <button type="button" class="copy-btn" data-copy-prompt>{{ $copy['prompts']['copy'] }}</button>
                                <button type="button" class="copy-btn prompt-expand">{{ $copy['prompts']['expand'] }}</button>
                            </div>
                        </article>
                    @endforeach
                </div>
                <p class="prompt-empty" id="prompt-empty">{{ $copy['prompts']['empty'] }}</p>
            </section>
        </div>
    </div>

    <section class="pledge-band" id="pledge" aria-labelledby="pledge-heading">
        <div class="shell">
            <p class="kicker">{{ $pledge['kicker'] }}</p>
            <h2 id="pledge-heading">{{ $pledge['heading'] }}</h2>
            <p class="pledge-body">{{ $pledge['body'] }}</p>
            <ul class="pledge-points">
                @foreach ($pledge['points'] as $point)
                    <li><b>{{ $point['label'] }}</b><span>{{ $point['text'] }}</span></li>
                @endforeach
            </ul>
            <p class="pledge-note">{{ $pledge['disclaimer'] }}</p>
        </div>
    </section>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>{{ $copy['cta_band']['heading'] }}</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">{{ $copy['cta_band']['primary'] }}</a>
                <a href="{{ route('faq') }}" class="btn btn--outline-light">{{ $copy['cta_band']['secondary'] }}</a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset_v('js/toolkit/interview.js') }}" defer></script>
    <script src="{{ asset_v('js/toolkit/ats.js') }}" defer></script>
    <script src="{{ asset_v('js/toolkit/profile.js') }}" defer></script>
    <script src="{{ asset_v('js/student.js') }}" defer></script>
@endpush
