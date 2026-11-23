@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])

@push('head')
    <meta name="robots" content="{{ $page['robots'] }}">
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset_v('css/showcase.css') }}">
@endpush

@section('content')
    @php($h1Text = $seo['h1'] ?? '')
    @php($h1Accent = $seo['h1_accent'] ?? null)

    <section class="page-head show-head">
        <div class="shell shell--narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ config('emplowise.copy.layout.breadcrumb_home') }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $page['kicker'] }}</span>
            </nav>
            <p class="kicker">{{ $page['kicker'] }}</p>
            <h1>@if ($h1Accent && str_contains($h1Text, $h1Accent)){!! str_replace(e($h1Accent), '<em>'.e($h1Accent).'</em>', e($h1Text)) !!}@else{{ $h1Text }}@endif</h1>
            <p class="lede">{{ $seo['intro'] }}</p>
            <div class="hero-actions show-download">
                <a class="btn btn--teal" href="{{ asset_v($page['download']['path']) }}" download>
                    {{ $page['download']['label'] }}
                </a>
                <a class="btn btn--ghost" href="{{ route('contact') }}">Ask about the work</a>
                <span class="show-download-meta">{{ $page['download']['meta'] }}</span>
            </div>
        </div>
    </section>

    <nav class="show-jump" aria-label="On this page">
        <div class="shell shell--narrow show-jump-inner">
            <span class="show-jump-label">On this page</span>
            <ul>
                <li><a href="#evidence-policy">Evidence policy</a></li>
                <li><a href="#shipped">Shipped artifacts</a></li>
                <li><a href="#systems">Supporting systems</a></li>
                <li><a href="#case-standard">Case study standard</a></li>
            </ul>
        </div>
    </nav>

    <section class="section" id="evidence-policy">
        <div class="shell shell--narrow">
            <p class="kicker">Evidence policy</p>
            <h2>{{ $seo['h2'] }}</h2>
            <p class="lede show-lede">
                Proof of work that exists in the repository, described in the language a buyer would use. This is not a client list. Emplowise has no published client case studies yet, and this page does not invent any.
            </p>
            <ol class="show-rules">
                <li>
                    <div>
                        <strong>Repo proof or nothing.</strong>
                        <p>A shipped artifact is listed only if the code, command, or document that produces it is in the working tree. Each entry names the file or command that proves it.</p>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Behaviour over assertion.</strong>
                        <p>Where a number appears, it is the count of items in the repository, such as routes, tests, or fixtures. It is never a business result.</p>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>No borrowed proof.</strong>
                        <p>No client logo, testimonial, revenue figure, award, or market size is used. Lower-tier material from the brand guidelines never appears as evidence.</p>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Honest status.</strong>
                        <p>Something pre-launch is labelled pre-launch. Something planned is labelled planned. The AI automation division is explicitly pre-launch on the public site, and it is not presented here as shipped.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <section class="section show-band" id="shipped">
        <div class="shell shell--narrow">
            <p class="kicker">Section A</p>
            <h2>Four shipped artifacts, each with repository proof.</h2>
            <p class="lede show-lede">
                These are the things a buyer can inspect today. Each entry names the problem it solves, the strategy behind it, what it does, and where the proof lives.
            </p>

            <article class="show-entry">
                <div class="show-entry-head">
                    <h3>A1. Phase I web platform</h3>
                    <span class="show-entry-tag">Shipped</span>
                </div>
                <dl class="show-fields">
                    <dt>Problem</dt>
                    <dd>A remote HR service with published prices needs a credible public surface that works on a free host, without a server bill and without inventing proof.</dd>
                    <dt>Strategy</dt>
                    <dd>Build the site twice from one codebase: a Laravel application for the hosted environment, and a static export for GitHub Pages. Keep every public sentence in one config file so the two builds cannot drift.</dd>
                    <dt>Capabilities</dt>
                    <dd>
                        <ul>
                            <li>14 public routes rendered by one controller, with all copy in the configuration file.</li>
                            <li>A strict content security policy with a single hashed inline script, self-hosted fonts, and no third-party origins.</li>
                            <li>A four-step Free HR Audit wizard that validates inline, persists to SQLite, and degrades to Web3Forms or an email fallback on the static build.</li>
                            <li>A grounded assistant with 18 intents that answers from the same config facts, with an optional hosted model behind it.</li>
                            <li>EmploStart: five free career tools, three of them mirrored in the browser and checked against PHP fixtures.</li>
                            <li>Dark mode, reduced-motion support, and WCAG AA contrast on the documented pairings.</li>
                        </ul>
                    </dd>
                    <dt>Repo proof</dt>
                    <dd class="show-proof">
                        <code>routes/web.php</code>
                        <code>resources/views/</code>
                        <code>public/css/</code>
                        <code>public/js/</code>
                        <code>tests/Feature/</code>
                        <code>.github/workflows/publish-static.yml</code>
                        <code>docs/STATIC_HOSTING.md</code>
                    </dd>
                </dl>
            </article>

            <article class="show-entry">
                <div class="show-entry-head">
                    <h3>A2. EmploStart toolkit and parity engine</h3>
                    <span class="show-entry-tag">Shipped</span>
                </div>
                <dl class="show-fields">
                    <dt>Problem</dt>
                    <dd>Career tools that only work with a live API are useless on a static host, and a browser port that drifts from the server engine is worse than no port at all.</dd>
                    <dt>Strategy</dt>
                    <dd>Implement the algorithms once in PHP, port them to vanilla JavaScript, generate fixtures from PHP, and fail continuous integration when the two disagree.</dd>
                    <dt>Capabilities</dt>
                    <dd>
                        <ul>
                            <li>Interview practice: question bank by role family and seniority, STAR scoring, per-dimension breakdown, next-practice probe, and a model answer.</li>
                            <li>ATS score: keyword extraction with synonyms and stems, weighted score, target-title check, and structure and formatting checks.</li>
                            <li>Profile optimiser: headline variants, summary rewrite, bullet rewrites, and quick fixes.</li>
                            <li>Resume builder and prompt library, entirely client-side with local drafts.</li>
                            <li>22 parity fixtures: 11 interview, 6 ATS, and 5 profile.</li>
                        </ul>
                    </dd>
                    <dt>Repo proof</dt>
                    <dd class="show-proof">
                        <code>app/Services/StudentToolkit.php</code>
                        <code>public/js/toolkit/</code>
                        <code>tools/parity/generate.php</code>
                        <code>tools/parity/check-{interview,ats,profile}.mjs</code>
                        <code>tools/parity/fixtures.json</code>
                    </dd>
                </dl>
            </article>

            <article class="show-entry">
                <div class="show-entry-head">
                    <h3>A3. Free HR Audit diagnostic</h3>
                    <span class="show-entry-tag">Shipped</span>
                </div>
                <dl class="show-fields">
                    <dt>Problem</dt>
                    <dd>A free audit that is really a sales form wastes the buyer's time and produces nothing usable.</dd>
                    <dt>Strategy</dt>
                    <dd>Ask only what the first useful conversation needs: who you are, how many people you have, which people problems are live, and when you are available. Then hand it to a named lead with a gap map.</dd>
                    <dt>Capabilities</dt>
                    <dd>
                        <ul>
                            <li>Four steps, about four minutes, with inline validation tied to the offending step, live progress announcements, and a busy state during submit.</li>
                            <li>Honeypot, throttling at 15 per minute, and server-side validation mirroring the client rules.</li>
                            <li>A reference number, the preferred date and slot echoed back, and a written next step.</li>
                            <li>Static build: Web3Forms when a key is configured, an inert form with an email fallback when it is not.</li>
                        </ul>
                    </dd>
                    <dt>Repo proof</dt>
                    <dd class="show-proof">
                        <code>app/Http/Controllers/AuditRequestController.php</code>
                        <code>resources/views/pages/audit.blade.php</code>
                        <code>public/js/audit.js</code>
                        <code>tests/Feature/SiteAndApiTest.php</code>
                        <code>app/Console/Commands/ExportStatic.php</code>
                    </dd>
                </dl>
            </article>

            <article class="show-entry">
                <div class="show-entry-head">
                    <h3>A4. 2026 brand system</h3>
                    <span class="show-entry-tag">Shipped</span>
                </div>
                <dl class="show-fields">
                    <dt>Problem</dt>
                    <dd>The site had grown a visual language faster than it had a written one: gradients, extra accents, and glow shadows that no document governed.</dd>
                    <dt>Strategy</dt>
                    <dd>Write the system down, reconcile it against the live CSS, and name each drift instead of pretending the CSS already matched.</dd>
                    <dt>Capabilities</dt>
                    <dd>
                        <ul>
                            <li>An eight-swatch palette, three lockups, and clear-space and minimum-size rules.</li>
                            <li>WCAG pairings computed for every approved combination, including the ones that fail and why.</li>
                            <li>A token-by-token reconciliation that names the slate drift, the dark-only navy extension, the extra accents, the gradients, and the glow.</li>
                            <li>Seven Markdown sources and seven editable Office files, generated by a verifier-checked pipeline.</li>
                        </ul>
                    </dd>
                    <dt>Repo proof</dt>
                    <dd class="show-proof">
                        <code>docs/brand-kit/</code>
                        <code>public/css/tokens.css</code>
                        <code>public/css/theme.css</code>
                        <code>tools/brand-kit/</code>
                        <code>public/downloads/</code>
                    </dd>
                </dl>
            </article>
        </div>
    </section>

    <section class="section" id="systems">
        <div class="shell shell--narrow">
            <p class="kicker">Section B</p>
            <h2>Supporting systems, also in the repository.</h2>
            <p class="lede show-lede">
                These are engineering artifacts, not client work. They are listed because they are real, inspectable, and relevant to how the platform is maintained.
            </p>
            <div class="table-scroll table-scroll--systems" role="region" tabindex="0" aria-label="Supporting systems and their repository proof">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">System</th>
                            <th scope="col">What it does</th>
                            <th scope="col">Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Static export pipeline</td>
                            <td>Renders 14 pages, five redirect documents, a 404, a sitemap, robots, and a CSP meta tag; rewrites every asset, canonical, and download URL for a project subpath.</td>
                            <td class="show-proof"><code>ExportStatic.php</code> <code>ExportStaticTest.php</code></td>
                        </tr>
                        <tr>
                            <td>CSS bundle pipeline</td>
                            <td>Concatenates and minifies two bundles from 12 sources, checked in because the static host ships the public directory as is.</td>
                            <td class="show-proof"><code>CssBundles.php</code> <code>emplowise:bundle-css</code></td>
                        </tr>
                        <tr>
                            <td>SEO copy audit</td>
                            <td>Enforces title and description length, unique keywords, keyword placement, banned words, dashes, and the confirmed pricing facts.</td>
                            <td class="show-proof"><code>EmplowiseSeoAudit.php</code></td>
                        </tr>
                        <tr>
                            <td>CI gate</td>
                            <td>Bundle freshness, JavaScript syntax, SEO audit, the full test suite, three parity harnesses, and a keyless export smoke test.</td>
                            <td class="show-proof"><code>.github/workflows/ci.yml</code></td>
                        </tr>
                        <tr>
                            <td>Dual deployment</td>
                            <td>A public original and a private mirror with a documented one-way sync and backup tags.</td>
                            <td class="show-proof"><code>docs/REPO_TOPOLOGY.md</code></td>
                        </tr>
                        <tr>
                            <td>Security headers</td>
                            <td>A content security policy with a hashed inline bootstrap, HSTS on HTTPS only, COOP and CORP, nosniff, frame denial, and CSP reporting.</td>
                            <td class="show-proof"><code>SecurityHeaders.php</code> <code>SecurityHeadersTest.php</code></td>
                        </tr>
                        <tr>
                            <td>Theme bootstrap</td>
                            <td>One inline script, hashed into the CSP, so dark mode paints before first paint without allowing inline scripts.</td>
                            <td class="show-proof"><code>ThemeBootstrap.php</code></td>
                        </tr>
                        <tr>
                            <td>Office generator</td>
                            <td>Python builders and a verifier that asserts OOXML validity, a size ceiling, an embedded logo, and no raw markers.</td>
                            <td class="show-proof"><code>tools/brand-kit/</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="section show-band" id="case-standard">
        <div class="shell shell--narrow">
            <p class="kicker">Section C</p>
            <h2>The case study standard, written down.</h2>
            <p class="lede show-lede">
                No client case study appears in this portfolio because none has been cleared for publication. A case study will be added only when all five conditions below are met. This is the standard, written down so a future case study cannot be assembled from memory.
            </p>
            <ol class="show-rules">
                <li>
                    <div>
                        <strong>Written permission.</strong>
                        <p>The client has agreed in writing to be named, and to the specific numbers and quotes used.</p>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Baseline.</strong>
                        <p>The starting state is measured and dated before the engagement: headcount, payroll cycle time, open people issues, documentation coverage, or whichever metric the case study claims to move.</p>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Method.</strong>
                        <p>The four stages are described concretely enough that a reader can see what was installed, not just that HR was transformed.</p>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Outcome with a source.</strong>
                        <p>Any percentage or saving traces to a client record, not an estimate. If the client will not share the number, the case study uses a qualitative outcome in the client's own words.</p>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Limits.</strong>
                        <p>The case study states what did not change, how long the result held, and what the engagement did not cover.</p>
                    </div>
                </li>
            </ol>
            <div class="show-note">
                <p>Until then, the honest proof is the shipped platform: the code, the tests, the documents, and the published prices. Anything else would be fiction with a logo attached.</p>
            </div>
        </div>
    </section>

    <section class="show-cta">
        <div class="shell show-cta-inner">
            <div>
                <h2>Inspect the work, then start with the audit.</h2>
                <p>The portfolio is also a Word document, with the same entries and the same repository references.</p>
            </div>
            <div class="show-cta-actions">
                <a class="btn btn--teal" href="{{ asset_v($page['download']['path']) }}" download>{{ $page['download']['label'] }}</a>
                <a class="btn btn--outline-light" href="{{ route('free-hr-audit') }}">Start the Free HR Audit</a>
            </div>
        </div>
    </section>
@endsection
