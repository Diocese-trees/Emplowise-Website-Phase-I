# Website breakdown, 2026

A structural map of the Emplowise site as it is built: every public route, the
view and controller method behind it, the audience it serves, its SEO role and
its call to action. It also inventories the layout, the interactive modules and
the delivery plumbing. Copy lives in `config/emplowise.php`; this document
describes the mechanism, not new marketing text.

## 1. Sitemap and SEO route matrix

Fourteen public routes. Eleven shipped in Phase I; the three showcase routes are
added in this release and are marked **new**. Every route renders through
`App\Http\Controllers\SiteController` and is pre-rendered by
`emplowise:export-static`.

| URI | View | Controller method | Buyer situation | SEO role | Primary CTA |
| --- | --- | --- | --- | --- | --- |
| `/` | `pages/home` | `home` | Founder or SME owner who has outgrown informal HR | Brand head term: remote HR services in Pakistan | Free HR audit |
| `/what-we-are` | `pages/what-we-are` | `whatWeAre` | Buyer checking legitimacy, mission and who is accountable | Trust and entity page: HR outsourcing company in Pakistan | Meet the team, book audit |
| `/what-we-do` | `pages/what-we-do` | `whatWeDo` | Buyer comparing service scope and depth | Service catalogue: HR operations outsourcing | Book audit |
| `/why-emplowise` | `pages/why-emplowise` | `whyEmplowise` | Buyer comparing price and scope with an agency or a hire | Commercial decision page: why Emplowise, published pricing | Book audit |
| `/what-we-are-building` | `pages/what-we-are-building` | `whatWeAreBuilding` | Buyer or partner testing whether the AI claims are real | Category page: AI automation for HR, pre-launch | Contact, book audit |
| `/emplostart` | `pages/emplostart` | `emploStart` | Student or early career candidate, free entry point | Tool page: free ATS resume checker and four more tools | Use a tool |
| `/faq` | `pages/faq` | `faq` | Buyer with a specific objection before a call | Long-tail question coverage, FAQPage schema | Book audit |
| `/contact` | `pages/contact` | `contact` | Buyer ready to talk, or a candidate with a question | Contact entity page | Email, free audit |
| `/free-hr-audit` | `pages/audit` | `audit` | Buyer willing to share headcount and pain points | Conversion page: free HR audit in Pakistan | Submit the wizard |
| `/terms` | `pages/legal` | `terms` | Any visitor checking terms before an engagement | Legal boilerplate, low crawl priority | Read the terms |
| `/privacy` | `pages/legal` | `privacy` | Any visitor checking data handling and consent | Legal boilerplate, low crawl priority | Read the policy |
| `/company-profile` | `pages/company-profile` | `companyProfile` | Buyer who needs a document to circulate internally | **new** Editorial entity page, DOCX download | Download profile, book audit |
| `/portfolio` | `pages/portfolio` | `portfolio` | Buyer who wants evidence of shipped work | **new** Proof page, repo-evidenced artifacts | Download portfolio, book audit |
| `/pitch-deck` | `pages/pitch-deck` | `pitchDeck` | Partner or investor reviewing the business model | **new** Presentation surface, `noindex, follow` by default | Play the deck, download PPTX |

The three showcase routes are deliberately non-cannibalising: their primary
keywords are `Emplowise company profile`, `Emplowise portfolio` and
`Emplowise pitch deck`, none of which overlap the nine Phase I targets above.
The SEO audit asserts uniqueness across titles, descriptions, primary and
secondary keywords.

### Legacy URLs

A static host cannot issue a 301, so each legacy URL becomes a tiny redirect
document written by the exporter. All five keep a `noindex, follow` robots meta
and a canonical pointing at the destination.

| Legacy URI | Destination | Laravel | Static export |
| --- | --- | --- | --- |
| `/pricing` | `/why-emplowise` | 301 | `pricing/index.html` meta refresh |
| `/career-hub` | `/emplostart` | 301 | `career-hub/index.html` meta refresh |
| `/student-hub` | `/emplostart` | 301 | `student-hub/index.html` meta refresh |
| `/ai-automation` | `/what-we-are-building` | 301 | `ai-automation/index.html` meta refresh |
| `/team` | `/what-we-are#team` | 301 | `team/index.html` meta refresh |

### 404

`resources/views/errors/404.blade.php` renders the dynamic 404; the exporter
writes a matching root `404.html` with the same CSP meta and the pre-paint theme
bootstrap, so GitHub Pages serves a branded page for any unknown path.

## 2. Layout and partial inventory

| File | Role |
| --- | --- |
| `resources/views/layouts/app.blade.php` | Document head (title, description, canonical, OG/Twitter, JSON-LD `Organization` + `ProfessionalService` + offer catalog), theme bootstrap, `site.css`, body shell, `@stack('styles')` and `@stack('scripts')` for page-level assets |
| `resources/views/partials/header.blade.php` | Brand lockup, primary navigation, theme toggle, audit CTA |
| `resources/views/partials/footer.blade.php` | Footer brand column, company links, resource links, legal links, consent opener |
| `resources/views/partials/chat.blade.php` | Assistant launcher and panel; ships the knowledge base to the browser |
| `resources/views/partials/consent.blade.php` | Consent banner and preferences dialog |
| `resources/views/partials/social-links.blade.php` | Shared social row, filtered by context |
| `resources/views/partials/team-showcase.blade.php` | Tabbed team profiles on `/what-we-are` |
| `resources/views/partials/emplostart/{ats,interview,profile}.blade.php` | The three server-backed tool panels inside `/emplostart` |
| `resources/views/components/icon.blade.php` | Inline SVG icon component |
| `resources/views/pages/*.blade.php` | One file per route, all data injected from config |

Each page sets `@section('title')`, `@section('meta_description')` and, where it
differs, `@section('og_image')`. The layout computes the canonical from
`url()->current()` and keeps it absolute; `ForcePublicUrl` later collapses
same-origin absolute URLs to root-relative ones while preserving canonicals, OG
images and JSON-LD.

## 3. Interactive modules

### 3.1 Free HR Audit wizard (`/free-hr-audit`)

Four steps: contact, company and headcount, pain points, scheduling. The form is
`novalidate` by design and validates in JavaScript so the error text sits next to
the field; the server repeats every rule in `AuditRequestController`.

- Server rules: name 2 to 120 characters, company 2 to 160, RFC email, phone
  `[0-9+\-\s()]{7,40}`, headcount band in five values, 1 to 6 pain points from a
  fixed list, notes up to 1500, date from today to 60 days ahead, one of three
  slots, consent accepted.
- Honeypot: a hidden `website` field. A filled value returns `202` and stores
  nothing.
- Success returns `201` with `message`, `reference` (`EMP-YYMMDD-XXXX`),
  `preferred_date`, `preferred_slot` and `next_step`.
- Progress is announced through `#wizard-label` (`aria-live="polite"`,
  `aria-atomic="true"`); the form carries `aria-busy` while submitting.
- Drafts: `sessionStorage` key `emplowise_audit_draft` for the in-progress
  wizard, `localStorage` key `emplowise_resume_draft_v1` for the resume builder.
- Static build: the exporter rewrites the endpoint to Web3Forms and injects the
  hidden `access_key` when one is configured. With no key the form is marked
  `data-audit-nokey="1"`, never posts, and offers a `mailto:` fallback.

### 3.2 EmploStart toolkit (`/emplostart`)

Five tools, one page, one tab rail: mock interview, ATS score, resume builder,
profile optimiser and the 14-prompt library.

| Tool | Server | Browser | Parity |
| --- | --- | --- | --- |
| Interview questions and feedback | `POST /api/student/interview/questions`, `/feedback` | `public/js/toolkit/interview.js` | `tools/parity/check-interview.mjs`, 11 fixtures |
| ATS resume and JD score | `POST /api/student/ats` | `public/js/toolkit/ats.js` | `tools/parity/check-ats.mjs`, 6 fixtures |
| Profile optimiser | `POST /api/student/profile` | `public/js/toolkit/profile.js` | `tools/parity/check-profile.mjs`, 5 fixtures |
| Resume builder | none | `public/js/student.js` | client only, `localStorage` drafts |
| Prompt library | none | `public/js/student.js` | client only |

Fixtures are generated from the PHP engines with `php tools/parity/generate.php`
and committed. The browser ports must match them exactly; the three Node
harnesses run in CI. On the static build the exporter hides
`#emplostart-config`, so the browser engines answer immediately instead of
calling endpoints that cannot exist.

### 3.3 Assistant (`public/js/chat.js`)

Eighteen intents scored by phrase weight, with a follow-up prior, near-tie
clarification, suggestion chips and links limited to known routes. The server
half is `App\Services\EmplowiseAssistant`; when `ALIBABA_CLOUD_API_KEY` is set it
upgrades to Model Studio (`qwen-max`, then `kimi`), and otherwise answers from
the same config-derived rules and reports `model: local-rules`.

`window.EmplowiseChat` exposes `intent()` and `reply()` as soon as the file
evaluates, so the routing harness can test pure functions; DOM wiring is
deferred to the first interaction, focus, pointer entry or idle callback. That
keeps the widget off the critical path without changing its behaviour.

## 4. Assets, bundles and subpath hosting

- `App\Support\CssBundles` concatenates and minifies `public/css/*.css` into two
  checked-in bundles: `site.css` (loaded everywhere) and `hub.css` (loaded only
  on `/emplostart`). Edit sources, then run `php artisan emplowise:bundle-css`.
- The showcase pages add `public/css/showcase.css` through `@push('styles')`,
  so `/`, `/audit` and the other Phase I pages load zero bytes of it. The deck
  adds `public/js/deck.js` through `@push('scripts')`.
- `asset_v()` appends `?v=<filemtime>` to CSS, JS and image URLs. Because the
  value is the file's own mtime, a regenerated bundle busts the cache without a
  deploy step.
- `App\Http\Middleware\ForcePublicUrl` keeps generated URLs usable behind a
  proxy and collapses same-origin absolute URLs to root-relative ones, with a
  keep-list for canonicals, OG images, Twitter images and JSON-LD.
- `emplowise:export-static` rewrites `href`/`src`/`srcset` prefixes to the
  project base (`/Emplowise-Website-Phase-I/`), appends a trailing slash to
  directory links, quotes the canonical and `og:url` at the served URL, and
  copies `css/`, `js/`, `img/`, `fonts/` and now `downloads/`.
- Download links in the showcase views are built with `asset_v()` so the same
  markup works at `/downloads/...` in Laravel and at
  `https://<user>.github.io/<repo>/downloads/...` on the static build.

## 5. Security posture

| Control | Laravel | Static export |
| --- | --- | --- |
| CSP | `SecurityHeaders` header, `SECURITY_CSP_MODE=report_only` or `enforce` | `<meta http-equiv="Content-Security-Policy">` injected by the exporter |
| `script-src` | `'self'` plus the SHA-256 hash of the single inline theme bootstrap | Same hash, same bytes |
| Inline styles | Forbidden; utilities live in `inline.css` | Forbidden; asserted by `ExportStaticTest` |
| HSTS | Only on HTTPS requests, `includeSubDomains` and `preload` | GitHub's own header; documented limitation |
| Frame denial | `X-Frame-Options: DENY` plus `frame-ancestors 'none'` | `frame-ancestors` cannot be delivered by a meta CSP |
| Other headers | nosniff, referrer policy, permissions policy, COOP, CORP | Provided by GitHub Pages defaults |
| Reporting | `POST /api/csp-report` (throttled 120/min) plus `report-uri` | No collector; the meta policy is report-free |

`SecurityHeadersTest` asserts the header contract. `ExportStaticTest` asserts
that no exported page keeps an inline script other than the hashed bootstrap,
that the CSP meta carries the hash, and that no page still calls `/api/*`.

## 6. What this document deliberately omits

Pricing, service names, team biographies and FAQ answers are in
`config/emplowise.php` and repeated verbatim in
[`COMPANY_PROFILE.md`](COMPANY_PROFILE.md). Keeping one copy avoids the drift
that fills stale brochures with last quarter's prices. Unresolved commercial
details stay in the Open Evidence Ledger in [`README.md`](README.md).
