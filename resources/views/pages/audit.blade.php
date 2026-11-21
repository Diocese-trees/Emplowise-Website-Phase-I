@extends('layouts.app')

@section('title', 'Free HR Audit | Emplowise')
@section('meta_description', 'Book a free HR audit. Share your headcount, payroll, performance, and policy pain points, pick a preferred window, and get a prioritised gap list.')

@section('content')
    <section class="page-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <span>Free HR Audit</span>
            </nav>
            <p class="kicker">Free diagnostic · about four minutes</p>
            <h1>Tell us where HR hurts. We will map the gaps.</h1>
            <p class="lede">
                A structured review of payroll inputs, performance practice, and policy coverage. You leave with
                a prioritised gap list. Implementation is quoted separately, only if you want it.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell audit-layout">
            <div>
                <form class="wizard" id="audit-form" data-endpoint="{{ route('api.audit.store') }}" novalidate>
                    <div class="wizard-head">
                        <span class="wizard-step-label" id="wizard-label">Step 1 of 4 · Contact</span>
                        <span class="wizard-dots" id="wizard-dots" aria-hidden="true"><i class="is-current"></i><i></i><i></i><i></i></span>
                        <span class="wizard-step-label" id="wizard-count">01 / 04</span>
                    </div>
                    <div class="wizard-progress" aria-hidden="true"><span id="wizard-progress"></span></div>

                    {{-- Step 1 ------------------------------------------------- --}}
                    <div class="wizard-step is-active" data-step="1">
                        <h2>Who are we speaking with?</h2>
                        <p class="step-note">Work details only. No payment information is collected at any point.</p>

                        <div class="form-grid">
                            <div class="field" data-field="contact_name">
                                <label for="audit-name">Contact name</label>
                                <input id="audit-name" name="contact_name" type="text" autocomplete="name" maxlength="120" placeholder="Your full name" required>
                                <span class="field-error"></span>
                            </div>
                            <div class="field" data-field="company_name">
                                <label for="audit-company">Company</label>
                                <input id="audit-company" name="company_name" type="text" autocomplete="organization" maxlength="160" placeholder="Company or venture name" required>
                                <span class="field-error"></span>
                            </div>
                            <div class="field" data-field="email">
                                <label for="audit-email">Work email</label>
                                <input id="audit-email" name="email" type="email" autocomplete="email" maxlength="160" placeholder="you@company.com" required>
                                <span class="field-error"></span>
                            </div>
                            <div class="field" data-field="phone">
                                <label for="audit-phone">Phone</label>
                                <input id="audit-phone" name="phone" type="tel" autocomplete="tel" maxlength="40" placeholder="+92 300 1234567" required>
                                <span class="field-error"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2 ------------------------------------------------- --}}
                    <div class="wizard-step" data-step="2">
                        <h2>What does the team look like?</h2>
                        <p class="step-note">Select every pain point that is true today. There is no wrong answer, only a diagnosis.</p>

                        <div class="form-grid">
                            <div class="field field--full" data-field="headcount_band">
                                <label for="audit-headcount">Current headcount</label>
                                <select id="audit-headcount" name="headcount_band" required>
                                    <option value="">Select a band</option>
                                    @foreach ($headcountBands as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <span class="field-error"></span>
                            </div>

                            <div class="field field--full" data-field="pain_points">
                                <span class="field-label">Where does it hurt most?</span>
                                <div class="check-grid">
                                    @foreach ($painOptions as $value => $label)
                                        <label class="check">
                                            <input type="checkbox" name="pain_points[]" value="{{ $value }}">
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <span class="hint">Pick at least one. Up to six.</span>
                                <span class="field-error"></span>
                            </div>

                            <div class="field field--full" data-field="notes">
                                <label for="audit-notes">Anything else we should know? <span class="muted">(optional)</span></label>
                                <textarea id="audit-notes" name="notes" maxlength="1500" placeholder="Current tools, recent incidents, a policy that is out of date, a review cycle that stalled."></textarea>
                                <span class="field-error"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3 ------------------------------------------------- --}}
                    <div class="wizard-step" data-step="3">
                        <h2>When should we run it?</h2>
                        <p class="step-note">Pick a preferred date within the next 60 days and a time window. We confirm the exact slot after reviewing the intake.</p>

                        <div class="form-grid">
                            <div class="field" data-field="preferred_date">
                                <label for="audit-date">Preferred date</label>
                                <input id="audit-date" name="preferred_date" type="date"
                                       min="{{ now()->toDateString() }}"
                                       max="{{ now()->addDays(60)->toDateString() }}"
                                       required>
                                <span class="hint">Today through {{ now()->addDays(60)->format('d M Y') }}.</span>
                                <span class="field-error"></span>
                            </div>
                            <div class="field" data-field="preferred_slot">
                                <label for="audit-slot">Preferred window</label>
                                <select id="audit-slot" name="preferred_slot" required>
                                    <option value="">Select a window</option>
                                    @foreach ($slots as $slot)
                                        <option value="{{ $slot }}">{{ $slot }}</option>
                                    @endforeach
                                </select>
                                <span class="field-error"></span>
                            </div>

                            <div class="field field--full" data-field="consent">
                                <label class="check">
                                    <input type="checkbox" name="consent" value="1" required>
                                    <span>I agree to be contacted about this audit. Submissions are stored in the Emplowise database and used only to prepare the review.</span>
                                </label>
                                <span class="field-error"></span>
                            </div>

                            <div class="sr-only" aria-hidden="true">
                                <label for="audit-website">Website</label>
                                <input id="audit-website" name="website" type="text" tabindex="-1" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    {{-- Step 4 ------------------------------------------------- --}}
                    <div class="wizard-step" data-step="4">
                        <h2>Review and submit.</h2>
                        <p class="step-note">Check the details, then submit. You will get a reference number on the next screen.</p>

                        <dl class="review-list" id="review-list"></dl>

                        <div class="field" data-field="submit">
                            <p class="field-error" id="submit-error"></p>
                        </div>
                    </div>

                    <div class="wizard-actions">
                        <button type="button" class="btn btn--ghost" id="wizard-back" hidden>Back</button>
                        <button type="button" class="btn btn--primary" id="wizard-next">Continue</button>
                        <button type="submit" class="btn btn--teal" id="wizard-submit" hidden>Submit audit request</button>
                    </div>

                    <p class="form-status s32" id="audit-status" role="status"></p>
                </form>

                <div class="success-panel" id="audit-success" role="status">
                    <span class="success-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                    </span>
                    <p class="kicker s11">Request received</p>
                    <h2 class="s33">Your audit is in the queue.</h2>
                    <p class="small s34">
                        Keep the reference below. We review your intake, prepare a gap map, and confirm the window.
                    </p>
                    <p class="success-ref" id="success-ref">EMP-000000-XXXX</p>
                    <div class="review-list">
                        <div><dt>Preferred date</dt><dd id="success-date">Pending</dd></div>
                        <div><dt>Preferred window</dt><dd id="success-slot">Pending</dd></div>
                    </div>
                    <ul class="success-steps">
                        <li>We read the intake and flag missing records, payroll gaps, and policy exposure.</li>
                        <li>We confirm the audit window you selected, or propose the nearest available slot.</li>
                        <li>The audit conversation ends with a prioritised gap list and a short action plan.</li>
                    </ul>
                    <a href="{{ route('home') }}" class="btn btn--primary btn--sm s1">Back to the site</a>
                </div>
            </div>

            <aside class="audit-aside">
                <div class="aside-card">
                    <h3>What the audit covers</h3>
                    <ul class="aside-list">
                        <li>Payroll inputs, attendance, leave, and employee records.</li>
                        <li>Review cadence, manager capability, and unresolved people issues.</li>
                        <li>Policy coverage, SOPs, and documentation gaps.</li>
                        <li>A prioritised gap list and a short action plan.</li>
                    </ul>
                </div>

                <div class="aside-card">
                    <h3>What to prepare</h3>
                    <ul class="aside-list">
                        <li>Current headcount and how the team is structured.</li>
                        <li>How payroll is run today, and by whom.</li>
                        <li>Where performance reviews live, if anywhere.</li>
                        <li>Which policies exist and when they were last reviewed.</li>
                    </ul>
                </div>

                <div class="aside-card">
                    <h3>What it is not</h3>
                    <p class="small s34">
                        The audit is free and diagnostic. It is not legal advice, not a compliance guarantee, and
                        not a commitment to a package. Implementation is quoted separately.
                    </p>
                </div>

                <div class="aside-card">
                    <h3>Package context</h3>
                    <ul class="aside-list">
                        @foreach ($packages as $package)
                            <li>{{ $package['name'] }} · {{ $package['capacity'] }} · {{ $package['price_display'] }}</li>
                        @endforeach
                    </ul>
                    <p class="small muted s35">
                        Inclusions are confirmed in writing after your Free HR Audit.
                    </p>
                </div>
            </aside>
        </div>
    </section>
@endsection
