@extends('layouts.app')

@section('title', 'Free Student Hub | Emplowise')
@section('meta_description', 'Five free career tools for students: mock interviews, ATS resume scoring, an ATS resume builder, a profile optimizer, and a copy-paste prompt library. No account required.')

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
            <p class="kicker">Free student resources</p>
            <h1>Career tools that tell you <em>the truth.</em></h1>
            <p class="lede">{{ $student['intro'] }}</p>
            <div class="hub-stats">
                <div class="hub-stat"><b>5</b><span>Working tools</span></div>
                <div class="hub-stat"><b>0</b><span>Cost, forever</span></div>
                <div class="hub-stat"><b>0</b><span>Accounts required</span></div>
                <div class="hub-stat"><b>{{ count($student['prompts']) }}</b><span>Ready prompts</span></div>
            </div>
        </div>
    </section>

    <div class="shell hub-shell">
        <aside class="hub-rail" aria-label="Student tools">
            @php($tabs = [
                ['id' => 'interview', 'num' => '01', 'title' => 'Mock interview', 'desc' => 'Three questions, structured feedback'],
                ['id' => 'ats', 'num' => '02', 'title' => 'ATS score', 'desc' => 'Match a resume to a job description'],
                ['id' => 'resume', 'num' => '03', 'title' => 'Resume builder', 'desc' => 'Single column, parser safe, printable'],
                ['id' => 'profile', 'num' => '04', 'title' => 'Profile optimizer', 'desc' => 'Headline, summary, and bullet rewrites'],
                ['id' => 'prompts', 'num' => '05', 'title' => 'Prompt library', 'desc' => 'Copy, paste, adapt, use'],
            ])
            @foreach ($tabs as $tab)
                <button type="button"
                        class="hub-tab {{ $loop->first ? 'is-active' : '' }}"
                        data-hub-tab="{{ $tab['id'] }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        role="tab">
                    <span class="hub-tab-num">{{ $tab['num'] }}</span>
                    <span>
                        <span class="hub-tab-title">{{ $tab['title'] }}</span>
                        <span class="hub-tab-desc">{{ $tab['desc'] }}</span>
                    </span>
                </button>
            @endforeach
        </aside>

        <div>
            {{-- 01 Interview ------------------------------------------------- --}}
            <section class="hub-panel is-active" id="panel-interview" data-hub-panel="interview" role="tabpanel">
                <div class="panel-head">
                    <div>
                        <h2>Mock interview simulator</h2>
                        <p>Pick a role and a level. Answer one question at a time. Get a score, three strengths, three fixes, and a model answer built without invented facts.</p>
                    </div>
                    <span class="tool-status" id="interview-source">Local engine ready</span>
                </div>

                <div class="panel-card">
                    <div class="interview-setup">
                        <div class="field">
                            <label for="interview-role">Role</label>
                            <select id="interview-role">
                                @foreach ($student['roles'] as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="interview-level">Level</label>
                            <select id="interview-level">
                                @foreach ($student['levels'] as $level)
                                    <option value="{{ $level }}">{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn--primary" id="interview-start">Start set</button>
                    </div>
                    <p class="form-status s15" id="interview-status" role="status"></p>
                </div>

                <div class="panel-card how-card">
                    <h3>What happens after you press start</h3>
                    <ol class="how-steps">
                        <li><b>01</b><span>Three questions for your role and level, served one at a time.</span></li>
                        <li><b>02</b><span>Each answer is scored out of 100 on structure, evidence, and clarity.</span></li>
                        <li><b>03</b><span>You get three strengths, three fixes, and a model answer to adapt.</span></li>
                    </ol>
                    <p class="small muted s16">Nothing is uploaded to an account. Answers stay in this browser session, and the local scoring engine runs even when no AI key is configured.</p>
                </div>

                <div class="interview-stage panel-card" id="interview-stage" hidden>
                    <div class="q-progress" id="interview-progress" aria-hidden="true"></div>
                    <p class="question-text" id="interview-question"></p>

                    <label class="sr-only" for="interview-answer">Your answer</label>
                    <textarea class="answer-box" id="interview-answer" placeholder="Write the answer you would actually say. Situation, task, action, result."></textarea>
                    <div class="answer-meta">
                        <span id="interview-counter">0 characters</span>
                        <span>Use one concrete example. Numbers help.</span>
                    </div>
                    <button type="button" class="btn btn--teal" id="interview-submit">Score this answer</button>

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
                                    <span class="score-out">Out of 100</span>
                                </div>
                            </div>
                            <div>
                                <span class="verdict" id="interview-verdict"></span>
                                <h3 class="s18">Structured feedback</h3>
                                <p class="small muted s16">Scored on structure, evidence, and clarity. Local heuristics run first. AI refines when a key is configured.</p>
                            </div>
                        </div>

                        <div class="feedback-cols">
                            <div class="feedback-block feedback-block--good">
                                <h4>What worked</h4>
                                <ul id="interview-strengths"></ul>
                            </div>
                            <div class="feedback-block feedback-block--fix">
                                <h4>Fix next time</h4>
                                <ul id="interview-fixes"></ul>
                            </div>
                        </div>

                        <div class="model-answer" id="interview-model">
                            <strong>Model answer shape</strong>
                            <span></span>
                        </div>

                        <button type="button" class="btn btn--ghost s8" id="interview-next">Next question</button>
                    </div>
                </div>
            </section>

            {{-- 02 ATS ------------------------------------------------------- --}}
            <section class="hub-panel" id="panel-ats" data-hub-panel="ats" role="tabpanel">
                <div class="panel-head">
                    <div>
                        <h2>ATS resume and JD analyzer</h2>
                        <p>Paste a resume and a job description. Get a 0 to 100 score, the keywords you are missing, and the structure and formatting checks a parser cares about.</p>
                    </div>
                    <span class="tool-status">Explainable scoring</span>
                </div>

                <form class="panel-card" id="ats-form">
                    <div class="ats-inputs">
                        <div class="field">
                            <label for="ats-resume">Resume text</label>
                            <textarea id="ats-resume" placeholder="Paste the full resume as plain text. Minimum 60 characters. Include headings like Experience, Education, Skills."></textarea>
                            <span class="hint">Text is processed for this request and not stored.</span>
                        </div>
                        <div class="field">
                            <label for="ats-jd">Job description</label>
                            <textarea id="ats-jd" placeholder="Paste the job posting. Include responsibilities and requirements."></textarea>
                            <span class="hint">The keyword list is built from this posting only.</span>
                        </div>
                    </div>
                    <div class="s19">
                        <button type="submit" class="btn btn--primary" id="ats-submit">Score the match</button>
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
                                <span class="score-out">Out of 100</span>
                            </div>
                        </div>
                        <div>
                            <span class="verdict" id="ats-verdict"></span>
                            <h3 class="s18">Match report</h3>
                            <p class="small muted s16">Weighting: keywords 55, structure 25, formatting 20. The score is deterministic, so re-running it stays honest.</p>
                        </div>
                    </div>

                    <div class="bars" id="ats-bars"></div>

                    <div class="s1">
                        <h4 class="mono small s21">Matched keywords</h4>
                        <div class="chip-row s22" id="ats-matched"></div>
                    </div>

                    <div class="s23">
                        <h4 class="mono small s21">Missing keywords</h4>
                        <div class="chip-row s22" id="ats-missing"></div>
                    </div>

                    <div class="feedback-cols s1">
                        <div class="feedback-block">
                            <h4>Structure checks</h4>
                            <ul class="checklist" id="ats-structure"></ul>
                        </div>
                        <div class="feedback-block">
                            <h4>Formatting checks</h4>
                            <ul class="checklist" id="ats-format"></ul>
                        </div>
                    </div>

                    <div class="result-block" id="ats-edits-block" hidden>
                        <h4>AI suggested edits</h4>
                        <ul class="checklist" id="ats-edits"></ul>
                    </div>

                    <div class="result-block">
                        <h3>Fix list</h3>
                        <ul class="feedback-block--fix checklist" id="ats-fixes"></ul>
                    </div>
                </div>
            </section>

            {{-- 03 Resume builder ------------------------------------------- --}}
            <section class="hub-panel" id="panel-resume" data-hub-panel="resume" role="tabpanel">
                <div class="panel-head">
                    <div>
                        <h2>ATS resume builder</h2>
                        <p>One column. Standard headings. Plain text friendly. Fill the form, watch the preview, then print to PDF or copy the text. No account, no watermark.</p>
                    </div>
                    <span class="tool-status">Print ready</span>
                </div>

                <div class="resume-layout">
                    <form class="resume-form panel-card" id="resume-form">
                        <div class="form-grid">
                            <div class="field">
                                <label for="res-name">Full name</label>
                                <input id="res-name" data-rp="name" type="text" placeholder="Ayesha Khan" maxlength="80">
                            </div>
                            <div class="field">
                                <label for="res-title">Target title</label>
                                <input id="res-title" data-rp="title" type="text" placeholder="Data Analyst" maxlength="80">
                            </div>
                            <div class="field">
                                <label for="res-email">Email</label>
                                <input id="res-email" data-rp="email" type="email" placeholder="you@example.com" maxlength="120">
                            </div>
                            <div class="field">
                                <label for="res-phone">Phone</label>
                                <input id="res-phone" data-rp="phone" type="text" placeholder="+92 300 1234567" maxlength="40">
                            </div>
                            <div class="field">
                                <label for="res-location">Location</label>
                                <input id="res-location" data-rp="location" type="text" placeholder="Karachi, Pakistan" maxlength="80">
                            </div>
                            <div class="field">
                                <label for="res-linkedin">LinkedIn</label>
                                <input id="res-linkedin" data-rp="linkedin" type="text" placeholder="linkedin.com/in/yourname" maxlength="120">
                            </div>
                        </div>

                        <div class="field">
                            <label for="res-summary">Summary</label>
                            <textarea id="res-summary" data-rp="summary" placeholder="Three lines: what you do, strongest proof, what you want." maxlength="700"></textarea>
                        </div>

                        <div>
                            <div class="panel-head s24">
                                <h3 class="s25">Experience</h3>
                                <button type="button" class="btn btn--ghost btn--sm" id="add-exp">Add role</button>
                            </div>
                            <div class="repeat-list" id="exp-list"></div>
                        </div>

                        <div>
                            <div class="panel-head s24">
                                <h3 class="s25">Education</h3>
                                <button type="button" class="btn btn--ghost btn--sm" id="add-edu">Add education</button>
                            </div>
                            <div class="repeat-list" id="edu-list"></div>
                        </div>

                        <div class="field">
                            <label for="res-skills">Skills</label>
                            <textarea class="s26" id="res-skills" data-rp="skills" placeholder="Excel, SQL, Python, stakeholder communication" maxlength="400"></textarea>
                            <span class="hint">Comma separated. Use the exact tool names from the job posting.</span>
                        </div>
                    </form>

                    <div class="resume-preview-wrap">
                        <div class="resume-toolbar">
                            <button type="button" class="btn btn--primary btn--sm" id="resume-print">Print or save PDF</button>
                            <button type="button" class="btn btn--ghost btn--sm" id="resume-download">Download .txt</button>
                            <button type="button" class="btn btn--ghost btn--sm" id="resume-copy">Copy text</button>
                        </div>

                        <div class="resume-paper" id="resume-preview" aria-label="Resume preview">
                            <p class="rp-name" id="rp-name">Your Name</p>
                            <p class="rp-title" id="rp-title">Target title</p>
                            <p class="rp-contact" id="rp-contact">email · phone · location · LinkedIn</p>

                            <div class="rp-section" id="rp-summary-section">
                                <h3>Summary</h3>
                                <p id="rp-summary" class="rp-empty">Add a three line summary.</p>
                            </div>

                            <div class="rp-section">
                                <h3>Experience</h3>
                                <div id="rp-experience"><p class="rp-empty">Add at least one role.</p></div>
                            </div>

                            <div class="rp-section">
                                <h3>Education</h3>
                                <div id="rp-education"><p class="rp-empty">Add your degree or qualification.</p></div>
                            </div>

                            <div class="rp-section">
                                <h3>Skills</h3>
                                <p id="rp-skills" class="rp-empty">Add skills.</p>
                            </div>
                        </div>
                        <p class="rp-print-note">Print it, save it as PDF, or copy the plain text into any application form.</p>
                    </div>
                </div>
            </section>

            {{-- 04 Profile optimizer ---------------------------------------- --}}
            <section class="hub-panel" id="panel-profile" data-hub-panel="profile" role="tabpanel">
                <div class="panel-head">
                    <div>
                        <h2>Profile optimizer</h2>
                        <p>Paste your headline, summary, and bullets. Get three headline options, a tighter summary, and bullets rewritten to start with an action verb. Placeholders appear where a number is missing.</p>
                    </div>
                    <span class="tool-status">No invented metrics</span>
                </div>

                <div class="profile-grid">
                    <form class="panel-card" id="profile-form">
                        <div class="field">
                            <label for="profile-headline">Current headline</label>
                            <input id="profile-headline" type="text" maxlength="200" placeholder="Final year CS student looking for internships">
                        </div>
                        <div class="field">
                            <label for="profile-summary">Current summary</label>
                            <textarea id="profile-summary" maxlength="1500" placeholder="Paste your LinkedIn About or resume summary."></textarea>
                        </div>
                        <div class="field">
                            <label for="profile-bullets">Experience bullets</label>
                            <textarea id="profile-bullets" maxlength="2000" placeholder="One bullet per line."></textarea>
                        </div>
                        <div class="s27">
                            <button type="submit" class="btn btn--primary" id="profile-submit">Rewrite my profile</button>
                            <p class="form-status s16" id="profile-status" role="status"></p>
                        </div>
                    </form>

                    <div id="profile-result" hidden>
                        <div class="result-block">
                            <h3>Headline options</h3>
                            <div id="profile-headlines"></div>
                        </div>
                        <div class="result-block">
                            <div class="s28">
                                <h3 class="s16">Summary</h3>
                                <button type="button" class="copy-btn" data-copy-target="profile-summary-out">Copy</button>
                            </div>
                            <div class="result-text" id="profile-summary-out"></div>
                        </div>
                        <div class="result-block">
                            <div class="s28">
                                <h3 class="s16">Rewritten bullets</h3>
                                <button type="button" class="copy-btn" data-copy-target="profile-bullets-out">Copy</button>
                            </div>
                            <div class="result-text" id="profile-bullets-out"></div>
                        </div>
                        <div class="result-block">
                            <h4>Fix list</h4>
                            <ul class="checklist" id="profile-fixes"></ul>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 05 Prompt library ------------------------------------------ --}}
            <section class="hub-panel" id="panel-prompts" data-hub-panel="prompts" role="tabpanel">
                <div class="panel-head">
                    <div>
                        <h2>Prompt library</h2>
                        <p>{{ count($student['prompts']) }} copy-paste prompts for job search, resumes, interviews, LinkedIn, and career clarity. Fill the brackets with your details and run them in any assistant.</p>
                    </div>
                    <span class="tool-status">Yours to adapt</span>
                </div>

                <div class="prompt-toolbar">
                    <div class="search-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                        <label class="sr-only" for="prompt-search">Search prompts</label>
                        <input id="prompt-search" type="search" placeholder="Search prompts by task or keyword">
                    </div>
                    <div class="filter-row" id="prompt-filters">
                        <button type="button" class="filter-btn is-active" data-prompt-filter="all">All</button>
                        @foreach (array_unique(array_column($student['prompts'], 'category')) as $category)
                            <button type="button" class="filter-btn" data-prompt-filter="{{ $category }}">{{ $category }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="prompt-grid" id="prompt-grid">
                    @foreach ($student['prompts'] as $prompt)
                        <article class="prompt-card" data-category="{{ $prompt['category'] }}" data-prompt>
                            <span class="prompt-cat">{{ $prompt['category'] }}</span>
                            <h3>{{ $prompt['title'] }}</h3>
                            <p class="prompt-use">{{ $prompt['use'] }}</p>
                            <p class="prompt-text">{{ $prompt['text'] }}</p>
                            <div class="prompt-actions">
                                <button type="button" class="copy-btn" data-copy-prompt>Copy prompt</button>
                                <button type="button" class="copy-btn prompt-expand">Expand</button>
                            </div>
                        </article>
                    @endforeach
                </div>
                <p class="prompt-empty" id="prompt-empty">No prompts match that search. Try a broader word.</p>
            </section>
        </div>
    </div>

    <section class="cta-band">
        <div class="shell cta-inner">
            <h2>Hiring soon? The audit works for teams too.</h2>
            <div class="cta-actions">
                <a href="{{ route('free-hr-audit') }}" class="btn btn--teal">Book the free HR audit</a>
                <a href="{{ route('faq') }}" class="btn btn--outline-light">Student hub FAQs</a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset_v('js/student.js') }}" defer></script>
@endpush
