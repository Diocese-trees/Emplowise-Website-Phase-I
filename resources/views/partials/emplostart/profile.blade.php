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
                            <input id="profile-headline" type="text" autocomplete="off" enterkeyhint="next" maxlength="200" placeholder="Operations lead moving into product management">
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
