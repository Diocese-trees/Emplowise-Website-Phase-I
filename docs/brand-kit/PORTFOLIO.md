# Emplowise portfolio, 2026

Proof of work that exists in the repository, described in the same language a
buyer would use. This is not a client list. Emplowise has no published client
case studies yet, and this document does not invent any.

## Evidence policy

Four rules govern every entry below.

1. **Repo proof or nothing.** A shipped artifact is listed only if the code,
   command or document that produces it is in the working tree. Each entry names
   the file or command that proves it.
2. **Behaviour over assertion.** Where a number appears, it is the count of items
   in the repository (routes, tests, fixtures), not a business result.
3. **No borrowed proof.** No client logo, testimonial, revenue figure, award or
   market size is used. Tier 5 and tier 6 material from
   [`BRANDING_GUIDELINES.md`](BRANDING_GUIDELINES.md) never appears as evidence.
4. **Honest status.** Something pre-launch is labelled pre-launch. Something
   planned is labelled planned. The AI automation division is explicitly
   pre-launch on the public site, and it is not presented here as shipped.

## Section A: Verified shipped artifacts

### A1. Phase I web platform

**Problem.** A remote HR service with published prices needs a credible public
surface that works on a free host, without a server bill and without inventing
proof.

**Strategy.** Build the site twice from one codebase: a Laravel application for
the hosted environment, and a static export for GitHub Pages. Keep every public
sentence in one config file so the two builds cannot drift.

**Capabilities.**

- 14 public routes rendered by `SiteController`, all data from
  `config/emplowise.php`.
- A strict CSP with a single hashed inline script, self-hosted fonts, and no
  third-party origins.
- A four-step Free HR Audit wizard that validates inline, persists to SQLite and
  degrades to Web3Forms or a mailto fallback on the static build.
- A grounded assistant with 18 intents that answers from the same config facts,
  with an optional hosted model behind it.
- EmploStart: five free career tools, three of them mirrored in the browser and
  checked against PHP fixtures.
- Dark mode, reduced-motion support, and WCAG AA contrast on the documented
  pairings.

**Repo proof.** `routes/web.php`, `resources/views/`, `public/css/`,
`public/js/`, `tests/Feature/` (62 tests, 922 assertions),
`.github/workflows/publish-static.yml`, `docs/STATIC_HOSTING.md`.

### A2. EmploStart toolkit and parity engine

**Problem.** Career tools that only work with a live API are useless on a static
host, and a browser port that drifts from the server engine is worse than no
port at all.

**Strategy.** Implement the algorithms once in PHP (`StudentToolkit`), port them
to vanilla JavaScript, generate fixtures from PHP, and fail CI when the two
disagree.

**Capabilities.**

- Interview practice: question bank by role family and seniority, STAR scoring,
  per-dimension breakdown, next-practice probe, model answer.
- ATS score: keyword extraction with synonyms and stems, weighted score,
  target-title check, structure and formatting checks.
- Profile optimiser: headline variants, summary rewrite, bullet rewrites, quick
  fixes.
- Resume builder and prompt library, entirely client-side with `localStorage`
  drafts.
- 22 parity fixtures: 11 interview, 6 ATS, 5 profile.

**Repo proof.** `app/Services/StudentToolkit.php`, `public/js/toolkit/`,
`tools/parity/generate.php`, `tools/parity/check-{interview,ats,profile}.mjs`,
`tools/parity/fixtures.json`.

### A3. Free HR Audit diagnostic

**Problem.** A free audit that is really a sales form wastes the buyer's time and
produces nothing usable.

**Strategy.** Ask only what the first useful conversation needs: who you are,
how many people you have, which people problems are live, when you are
available. Then hand it to a named lead with a gap map.

**Capabilities.**

- Four steps, about four minutes, inline validation tied to the offending step,
  `aria-live` progress and `aria-busy` during submit.
- Honeypot, throttling at 15 per minute, server-side validation mirroring the
  client rules.
- Reference number `EMP-YYMMDD-XXXX`, preferred date and slot echoed back, a
  written next step.
- Static build: Web3Forms when a key is configured, an inert form with an email
  fallback when it is not.

**Repo proof.** `app/Http/Controllers/AuditRequestController.php`,
`resources/views/pages/audit.blade.php`, `public/js/audit.js`,
`tests/Feature/SiteAndApiTest.php`, `app/Console/Commands/ExportStatic.php`.

### A4. 2026 brand system

**Problem.** The site had grown a visual language faster than it had a written
one: gradients, extra accents and glow shadows that no document governed.

**Strategy.** Write the system down, reconcile it against the live CSS, and name
each drift instead of pretending the CSS already matched.

**Capabilities.**

- Eight-swatch palette, three lockups, clear-space and minimum-size rules.
- WCAG pairings computed for every approved combination, including the ones that
  fail and why.
- A token-by-token reconciliation that names slate drift, the dark-only navy
  extension, the extra accents, the gradients and the glow.
- Seven Markdown sources and seven editable Office files, generated by a
  verifier-checked pipeline.

**Repo proof.** `docs/brand-kit/`, `public/css/tokens.css`,
`public/css/theme.css`, `tools/brand-kit/`, `public/downloads/`.

## Section B: Repo-verified supporting systems

These are engineering artifacts, not client work. They are listed because they
are real, inspectable and relevant to how the platform is maintained.

| System | What it does | Proof |
| --- | --- | --- |
| Static export pipeline | Renders 14 pages, five redirect documents, a 404, sitemap, robots and a CSP meta; rewrites every asset, canonical and download URL for a project subpath | `app/Console/Commands/ExportStatic.php`, `tests/Feature/ExportStaticTest.php` |
| CSS bundle pipeline | Concatenates and minifies two bundles from 12 sources, checked in because the static host ships `public/` as is | `app/Support/CssBundles.php`, `php artisan emplowise:bundle-css` |
| SEO copy audit | Enforces title and description length, unique keywords, keyword placement, banned words, em dashes and the confirmed pricing facts | `app/Console/Commands/EmplowiseSeoAudit.php` |
| CI gate | Bundle freshness, JS syntax, SEO audit, 62 tests, three parity harnesses (chat routing runs locally against Chromium), keyless export smoke test | `.github/workflows/ci.yml` |
| Dual deployment | A public original and a private mirror with a documented one-way sync and backup tags | `docs/REPO_TOPOLOGY.md` |
| Security headers | CSP with a hashed inline bootstrap, HSTS on HTTPS only, COOP/CORP, nosniff, frame denial, CSP reporting | `app/Http/Middleware/SecurityHeaders.php`, `tests/Feature/SecurityHeadersTest.php` |
| Theme bootstrap | One inline script, hashed into the CSP, so dark mode paints before first paint without `unsafe-inline` | `app/Support/ThemeBootstrap.php` |
| Office generator | Python builders and a verifier that asserts OOXML validity, size ceiling, embedded logo and no raw markers | `tools/brand-kit/` |

## Section C: Client case study proof standard

No client case study appears in this portfolio because none has been cleared for
publication. A case study will be added only when all five conditions below are
met. This is the standard, written down so a future case study cannot be
assembled from memory.

1. **Written permission.** The client has agreed in writing to be named, and to
   the specific numbers and quotes used.
2. **Baseline.** The starting state is measured and dated before the engagement:
   headcount, payroll cycle time, open people issues, documentation coverage, or
   whichever metric the case study claims to move.
3. **Method.** The four stages are described concretely enough that a reader can
   see what was installed, not just that "HR was transformed".
4. **Outcome with a source.** Any percentage or saving traces to a client record,
   not an estimate. If the client will not share the number, the case study uses
   a qualitative outcome in the client's own words.
5. **Limits.** The case study states what did not change, how long the result
   held, and what the engagement did not cover.

Until then, the honest proof is the shipped platform: the code, the tests, the
documents and the published prices. Anything else would be fiction with a logo
attached.

## Section D: Internal verification notes

Stripped from the DOCX and never rendered on the site.

- [To Verify: Whether any past or current client has agreed to a named case
  study, and under what terms.]
- [To Verify: Baseline metrics the business already records (audits per month,
  retention, payroll cycle time) that could become tier 1 evidence.]
- [To Verify: Permission to publish the 2,000+ employee engagement figure from
  Samarah Nabil's bio as a company-level credential rather than a personal one.]
- [To Verify: Whether the mirror repository can be cited publicly or must stay
  private.]
- [To Verify: Any partner or accelerator relationship that could be named
  alongside the platform.]
- [To Verify: A launch date for the AI automation division; today the public site
  says pre-launch, and this portfolio must not imply otherwise.]
