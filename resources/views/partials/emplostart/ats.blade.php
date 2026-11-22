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
