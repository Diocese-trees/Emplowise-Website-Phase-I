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
