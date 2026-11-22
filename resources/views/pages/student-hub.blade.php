@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@push('styles')
    <link rel="stylesheet" href="{{ asset_v('css/student.css') }}">
    <link rel="stylesheet" href="{{ asset_v('css/print.css') }}" media="print">
@endpush

@section('content')
    <div id="student-config"
         data-interview-questions="{{ route('api.student.interview.questions') }}"
         data-interview-feedback="{{ route('api.student.interview.feedback') }}"
         data-ats="{{ route('api.student.ats') }}"
         data-profile="{{ route('api.student.profile') }}"
         hidden></div>

    <section class="hub-banner">
        <div class="shell">
            <p class="kicker">{{ $copy['kicker'] }}</p>
            @include('partials.seo-h1', ['seo' => $seo])
            <p class="lede">{{ $seo['intro'] }}</p>
            <div class="hub-stats">
                <div class="hub-stat"><b>5</b><span>{{ $copy['stats'][0] }}</span></div>
                <div class="hub-stat"><b>0</b><span>{{ $copy['stats'][1] }}</span></div>
                <div class="hub-stat"><b>0</b><span>{{ $copy['stats'][2] }}</span></div>
                <div class="hub-stat"><b>{{ count($student['prompts']) }}</b><span>{{ $copy['stats'][3] }}</span></div>
            </div>
        </div>
    </section>

    <div class="shell hub-shell">
        <div class="hub-rail" role="tablist" aria-label="Student tools">
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
            <section class="hub-panel is-active" id="panel-interview" data-hub-panel="interview" role="tabpanel" aria-labelledby="tab-interview">
                <div class="panel-head">
                    <div>
                        <h2>{{ $copy['interview']['heading'] }}</h2>
                        <p>{{ $copy['interview']['body'] }}</p>
                    </div>
                    <span class="tool-status" id="interview-source">{{ $copy['interview']['status'] }}</span>
                </div>

                <div class="panel-card">
                    <div class="interview-setup">
                        <div class="field">
                            <label for="interview-role">{{ $copy['interview']['role_label'] }}</label>
                            <select id="interview-role">
                                @foreach ($student['roles'] as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="interview-level">{{ $copy['interview']['level_label'] }}</label>
                            <select id="interview-level">
                                @foreach ($student['levels'] as $level)
                                    <option value="{{ $level }}">{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn--primary" id="interview-start">{{ $copy['interview']['start'] }}</button>
                    </div>
                    <p class="form-status s15" id="interview-status" role="status"></p>
                </div>

                <div class="panel-card how-card">
                    <h3>{{ $copy['interview']['how_heading'] }}</h3>
                    <ol class="how-steps">
                        @foreach ($copy['interview']['how_steps'] as $index => $step)
                            <li><b>0{{ $index + 1 }}</b><span>{{ $step }}</span></li>
                        @endforeach
                    </ol>
                    <p class="small muted s16">{{ $copy['interview']['how_note'] }}</p>
                </div>

                <div class="interview-stage panel-card" id="interview-stage" hidden>
                    <div class="q-progress" id="interview-progress" aria-hidden="true"></div>
                    <p class="question-text" id="interview-question"></p>

                    <label class="sr-only" for="interview-answer">{{ $copy['interview']['answer_label'] }}</label>
                    <textarea class="answer-box" id="interview-answer" autocomplete="off" enterkeyhint="enter" placeholder="Write the answer you would actually say. Situation, task, action, result."></textarea>
                    <div class="answer-meta">
                        <span id="interview-counter">{{ $copy['interview']['counter_zero'] }}</span>
                        <span>{{ $copy['interview']['answer_meta'] }}</span>
                    </div>
                    <button type="button" class="btn btn--teal" id="interview-submit">{{ $copy['interview']['submit'] }}</button>

                    <div class="s17" id="interview-feedback" hidden>
                        <div class="score-row">
                            <div class="score-ring">
                                <svg viewBox="0 0 150 150" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="scoreGrad" x1="0" y1="0" x2="1" y2="1">
                                            <stop offset="0%" stop-color="#7c3aed"></stop>
                                            <stop offset="55%" stop-color="#d946ef"></stop>
                                            <stop offset="100%" stop-color="#22d3ee"></stop>
                                        </linearGradient>
                                    </defs>
                                    <circle class="track" cx="75" cy="75" r="64"></circle>
                                    <circle class="value" id="interview-ring" cx="75" cy="75" r="64" stroke-dasharray="402" stroke-dashoffset="402"></circle>
                                </svg>
                                <div class="score-ring-label">
                                    <span class="score-number" id="interview-score">0</span>
                                    <span class="score-out">{{ $copy['interview']['out_of'] }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="verdict" id="interview-verdict"></span>
                                <h3 class="s18">{{ $copy['interview']['feedback_heading'] }}</h3>
                                <p class="small muted s16">{{ $copy['interview']['score_note'] }}</p>
                            </div>
                        </div>

                        <div class="s18">
                            <h4>{{ $copy['interview']['breakdown_heading'] }}</h4>
                            <div class="bars" id="interview-bars"></div>
                        </div>

                        <div class="feedback-cols">
                            <div class="feedback-block feedback-block--good">
                                <h4>{{ $copy['interview']['worked'] }}</h4>
                                <ul id="interview-strengths"></ul>
                            </div>
                            <div class="feedback-block feedback-block--fix">
                                <h4>{{ $copy['interview']['fix_next'] }}</h4>
                                <ul id="interview-fixes"></ul>
                            </div>
                        </div>

                        <div class="model-answer" id="interview-model">
                            <strong>{{ $copy['interview']['model_answer'] }}</strong>
                            <span></span>
                        </div>

                        <div class="model-answer" id="interview-probe">
                            <strong>{{ $copy['interview']['probe_heading'] }}</strong>
                            <span></span>
                        </div>

                        <button type="button" class="btn btn--ghost s8" id="interview-next">{{ $copy['interview']['next'] }}</button>
                    </div>
                </div>
            </section>

            {{-- 02 ATS ------------------------------------------------------- --}}
            <section class="hub-panel" id="panel-ats" data-hub-panel="ats" role="tabpanel" aria-labelledby="tab-ats">
                <div class="panel-head">
                    <div>
                        <h2>{{ $seo['h2'] }}</h2>
                        <p>{{ $copy['ats']['body'] }}</p>
                    </div>
                    <span class="tool-status">{{ $copy['ats']['status'] }}</span>
                </div>

                <form class="panel-card" id="ats-form">
                    <div class="ats-inputs">
                        <div class="field">
                            <label for="ats-resume">{{ $copy['ats']['resume_label'] }}</label>
                            <textarea id="ats-resume" autocomplete="off" enterkeyhint="enter" placeholder="Paste the full resume as plain text. Minimum 60 characters. Include headings like Experience, Education, Skills."></textarea>
                            <span class="hint">{{ $copy['ats']['resume_hint'] }}</span>
                        </div>
                        <div class="field">
                            <label for="ats-jd">{{ $copy['ats']['jd_label'] }}</label>
                            <textarea id="ats-jd" autocomplete="off" enterkeyhint="enter" placeholder="Paste the job posting. Include responsibilities and requirements."></textarea>
                            <span class="hint">{{ $copy['ats']['jd_hint'] }}</span>
                        </div>
                    </div>
                    <div class="s19">
                        <button type="submit" class="btn btn--primary" id="ats-submit">{{ $copy['ats']['submit'] }}</button>
                        <p class="form-status s16" id="ats-status" role="status"></p>
                    </div>
                </form>

                <div class="panel-card" id="ats-result" hidden>
                    <div class="score-row">
                        <div class="score-ring">
                            <svg viewBox="0 0 150 150" aria-hidden="true">
                                <defs>
                                    <linearGradient id="scoreGradB" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#0d9488"></stop>
                                        <stop offset="55%" stop-color="#22d3ee"></stop>
                                        <stop offset="100%" stop-color="#7c3aed"></stop>
                                    </linearGradient>
                                </defs>
                                <circle class="track" cx="75" cy="75" r="64"></circle>
                                <circle class="value s20" id="ats-ring" cx="75" cy="75" r="64" stroke-dasharray="402" stroke-dashoffset="402"></circle>
                            </svg>
                            <div class="score-ring-label">
                                <span class="score-number" id="ats-score">0</span>
                                <span class="score-out">{{ $copy['ats']['out_of'] }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="verdict" id="ats-verdict"></span>
                            <h3 class="s18">{{ $copy['ats']['report_heading'] }}</h3>
                            <p class="small muted s16">{{ $copy['ats']['score_note'] }}</p>
                        </div>
                    </div>

                    <div class="bars" id="ats-bars"></div>

                    <div class="s1">
                        <h4 class="mono small s21">{{ $copy['ats']['matched'] }}</h4>
                        <div class="chip-row s22" id="ats-matched"></div>
                    </div>

                    <div class="s23">
                        <h4 class="mono small s21">{{ $copy['ats']['missing'] }}</h4>
                        <div class="chip-row s22" id="ats-missing"></div>
                    </div>

                    <div class="feedback-cols s1">
                        <div class="feedback-block">
                            <h4>{{ $copy['ats']['structure'] }}</h4>
                            <ul class="checklist" id="ats-structure"></ul>
                        </div>
                        <div class="feedback-block">
                            <h4>{{ $copy['ats']['format'] }}</h4>
                            <ul class="checklist" id="ats-format"></ul>
                        </div>
                    </div>

                    <div class="result-block" id="ats-edits-block" hidden>
                        <h4>{{ $copy['ats']['edits'] }}</h4>
                        <ul class="checklist" id="ats-edits"></ul>
                    </div>

                    <div class="result-block">
                        <h3>{{ $copy['ats']['fixes_heading'] }}</h3>
                        <ul class="feedback-block--fix checklist" id="ats-fixes"></ul>
                    </div>
                </div>
            </section>

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
            <section class="hub-panel" id="panel-profile" data-hub-panel="profile" role="tabpanel" aria-labelledby="tab-profile">
                <div class="panel-head">
                    <div>
                        <h2>{{ $copy['profile']['heading'] }}</h2>
                        <p>{{ $copy['profile']['body'] }}</p>
                    </div>
                    <span class="tool-status">{{ $copy['profile']['status'] }}</span>
                </div>

                <div class="profile-grid">
                    <form class="panel-card" id="profile-form">
                        <div class="field">
                            <label for="profile-target">{{ $copy['profile']['form']['target'] }}</label>
                            <input id="profile-target" type="text" autocomplete="off" enterkeyhint="next" maxlength="80" placeholder="Data Analyst Intern">
                        </div>
                        <div class="field">
                            <label for="profile-headline">{{ $copy['profile']['form']['headline'] }}</label>
                            <input id="profile-headline" type="text" autocomplete="off" enterkeyhint="next" maxlength="200" placeholder="Final year CS student looking for internships">
                        </div>
                        <div class="field">
                            <label for="profile-summary">{{ $copy['profile']['form']['summary'] }}</label>
                            <textarea id="profile-summary" autocomplete="off" enterkeyhint="enter" maxlength="1500" placeholder="Paste your LinkedIn About or resume summary."></textarea>
                        </div>
                        <div class="field">
                            <label for="profile-bullets">{{ $copy['profile']['form']['bullets'] }}</label>
                            <textarea id="profile-bullets" autocomplete="off" enterkeyhint="enter" maxlength="2000" placeholder="One bullet per line."></textarea>
                        </div>
                        <div class="s27">
                            <button type="submit" class="btn btn--primary" id="profile-submit">{{ $copy['profile']['submit'] }}</button>
                            <p class="form-status s16" id="profile-status" role="status"></p>
                        </div>
                    </form>

                    <div id="profile-result" hidden>
                        <div class="result-block">
                            <h3>{{ $copy['profile']['headlines_heading'] }}</h3>
                            <div id="profile-headlines"></div>
                        </div>
                        <div class="result-block">
                            <div class="s28">
                                <h3 class="s16">{{ $copy['profile']['summary_heading'] }}</h3>
                                <button type="button" class="copy-btn" data-copy-target="profile-summary-out">{{ $copy['profile']['copy'] }}</button>
                            </div>
                            <div class="result-text" id="profile-summary-out"></div>
                        </div>
                        <div class="result-block">
                            <div class="s28">
                                <h3 class="s16">{{ $copy['profile']['bullets_heading'] }}</h3>
                                <button type="button" class="copy-btn" data-copy-target="profile-bullets-out">{{ $copy['profile']['copy'] }}</button>
                            </div>
                            <div class="result-text" id="profile-bullets-out"></div>
                        </div>
                        <div class="result-block">
                            <h4>{{ $copy['profile']['fixes_heading'] }}</h4>
                            <ul class="checklist" id="profile-fixes"></ul>
                        </div>
                    </div>
                </div>
            </section>

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
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
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
