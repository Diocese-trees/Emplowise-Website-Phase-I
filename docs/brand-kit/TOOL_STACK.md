# Tool stack, 2026

Verified from the working tree on 2026-11-23. Every version below is either
pinned in a manifest or observed from the installed tool; nothing is aspirational.
The upgrade ledger at the end lists work that is *not* in this build so it cannot
be mistaken for a shipped capability.

## 1. Backend

| Piece | Version | Evidence |
| --- | --- | --- |
| PHP | 8.3.x, tested on 8.3.33 | `composer.json` requires `^8.3`; runtime reports 8.3.33 |
| Laravel | 13.43.0 | `php artisan --version`; `composer.json` requires `^13.17` |
| laravel/tinker | ^3.0 | `composer.json` |
| Database | SQLite by default, any Laravel driver supported | `.env.example` sets `DB_CONNECTION=sqlite` |
| Sessions and cache | Database driver by default; array driver in tests and in the static export | `.env.example`, `phpunit.xml`, `ExportStatic` |
| Required PHP extensions | `mbstring`, `openssl`, `curl`, `json`, `pdo_sqlite` (plus `dom`, `fileinfo`, `tokenizer`, `xml`, `ctype` in CI) | `.github/workflows/ci.yml` |

Application layers:

- `app/Http/Controllers/SiteController.php` renders every page with config data.
- `app/Http/Controllers/AuditRequestController.php` validates and persists the
  audit intake.
- `app/Http/Controllers/Api/{ChatController,StudentController,CspReportController}.php`
  serve the assistant, the three server-backed tools and CSP reports.
- `app/Services/{EmplowiseAssistant,StudentToolkit,AlibabaCloudClient}.php` hold
  the business rules: intent scoring and grounded replies, the interview/ATS/
  profile algorithms, and the OpenAI-compatible Model Studio client.
- `app/Support/{CssBundles,ThemeBootstrap,helpers}.php` own the CSS bundles, the
  hashed inline theme script and `asset_v()`.
- `app/Http/Middleware/{SecurityHeaders,ForcePublicUrl}.php` emit the security
  policy and keep URLs valid behind a proxy.

## 2. Front end

| Piece | Choice | Evidence |
| --- | --- | --- |
| Templating | Blade | `resources/views` |
| CSS | Plain CSS with custom properties, no framework, no preprocessor | `public/css/tokens.css` |
| Bundling | `php artisan emplowise:bundle-css` (PHP concatenate + minify) | `App\Support\CssBundles` |
| JavaScript | Vanilla ES2017+, no build step, no npm dependency for the site | `public/js` |
| Fonts | Self-hosted Montserrat 700/800 and Plus Jakarta Sans 400/500/600, latin subset only | `public/css/fonts.css`, `public/fonts` |
| Icons | Inline SVG component | `resources/views/components/icon.blade.php` |
| Images | Committed PNG/WebP derivatives, `<picture>` with `srcset` | `public/img` |

The site has **no** Node build step, no Tailwind, no React or Vue and no runtime
npm dependency. Node is a test and tooling runtime only.

Client scripts, all loaded with `defer`:

| File | Loaded on | Role |
| --- | --- | --- |
| `theme.js` | every page | theme toggle and persistence |
| `app.js` | every page | shell behaviour, header, to top, scroll progress |
| `consent.js` | every page | consent banner and preferences |
| `chat.js` | every page | assistant panel and local knowledge base |
| `home.js` | `/` | home-only enhancements |
| `faq.js` | `/faq` | accordion |
| `team.js` | `/what-we-are` | team tabs |
| `audit.js` | `/free-hr-audit` | four-step wizard, drafts, inline validation |
| `student.js` | `/emplostart` | tabs, resume builder, prompt library |
| `js/toolkit/{interview,ats,profile}.js` | `/emplostart` | browser ports of the PHP engines |
| `deck.js` | `/pitch-deck` only (new) | slide navigation and counter |

## 3. AI, forms and parity

| Piece | Detail |
| --- | --- |
| Model provider | Alibaba Cloud Model Studio, OpenAI-compatible `/chat/completions` |
| Primary and fallback models | `qwen-max`, then `kimi` (both configurable) |
| Client | `App\Services\AlibabaCloudClient`, timeout 45 s, connect timeout 10 s |
| No-key behaviour | deterministic local engines, `model: local-rules`, `source: fallback` |
| Booking form, Laravel | `POST /api/audit`, throttled 15/min, SQLite `audit_requests` |
| Booking form, static | Web3Forms with a repository secret; keyless builds are inert |
| Parity fixtures | `tools/parity/generate.php` writes `fixtures.json`; three Node harnesses compare browser output with PHP |
| Chat routing harness | `tools/parity/check-chat-routing.mjs`, needs a served export and headless Chromium |

## 4. Delivery

| Target | Mechanism |
| --- | --- |
| GitHub Pages | `publish-static.yml` exports and force-pushes `build/static` to `gh-pages`; Pages serves that branch |
| Docker | Multi-stage `Dockerfile` (composer builder, then `php:8.3-apache`), `docker-compose.yml` with SQLite, storage and DB volumes, `deploy/entrypoint.sh` migrates and caches at start-up |
| Nginx | `deploy/nginx.conf.example`, `deploy/apache-php.ini` for PHP-FPM tuning |
| Apache | `a2enmod rewrite headers expires`, document root forced to `public/` |
| Static export | `php artisan emplowise:export-static <path> --base= --audit-key=` |

GitHub Pages cannot send custom headers. Everything the PHP middleware emits is
therefore a *Laravel* guarantee; on the static build only the `<meta>` CSP
travels with the page. The limitation is documented in `README.md` and in
[`WEBSITE_BREAKDOWN.md`](WEBSITE_BREAKDOWN.md).

## 5. Tests and quality gates

| Gate | Command | Count |
| --- | --- | --- |
| Feature suite | `php artisan test` | 60 tests, 827 assertions before this release; the showcase release adds route, export and download assertions |
| SEO copy audit | `php artisan emplowise:seo-audit` | every public route: title 45 to 60 characters, description 135 to 155, unique keywords, primary keyword present in title, h1, intro and h2 |
| Security headers | `php artisan test --filter=SecurityHeadersTest` | CSP mode, HSTS over HTTPS, nosniff, frame denial, permissions policy |
| Static export | `php artisan test --filter=ExportStaticTest` | 11 pages plus redirects, subpath assets, CSP meta, no inline script or style |
| Browser parity | `node tools/parity/check-{interview,ats,profile}.mjs` | 11 + 6 + 5 fixtures |
| JS syntax | `node --check` on every script | all files in `public/js` |
| Bundle freshness | `emplowise:bundle-css` then `git diff --exit-code` | `site.css`, `hub.css` |
| Office files | `python tools/brand-kit/verify-office.py` | 6 DOCX + 1 PPTX, size, OOXML, logo, no raw Markdown or verification notes |

`composer.json` also ships `laravel/pint` for style, `phpunit/phpunit` 12.5,
`mockery/mockery`, `nunomaduro/collision` and `fakerphp/faker` as dev
dependencies. None of them run at request time.

## 6. Brand-kit tooling

| Piece | Detail |
| --- | --- |
| Runtime | Python 3.11 or newer, isolated in `tools/brand-kit/.venv` |
| Libraries | `python-docx`, `python-pptx`, `Pillow` (pinned in `tools/brand-kit/requirements.txt`) |
| Builders | `build-docx.py`, `build-pptx.py` |
| Verifier | `verify-office.py` |
| Output | `public/downloads/*.docx`, `public/downloads/*.pptx` |
| Why Python | the site has no Node build step; keeping the generator in Python prevents a second JavaScript toolchain and keeps `package.json` out of the repository root |

## 7. Upgrade ledger for Phase II

These are known gaps, not hidden debt. Each one names the trigger that would
justify the work.

| Item | Current state | Trigger to upgrade |
| --- | --- | --- |
| Logos | PNG/WebP derivatives only | A vector master arrives; ship SVG plus a PNG fallback |
| Asset pipeline | Hand-rolled PHP minifier, no cache-busting manifest beyond `filemtime` | A second product surface or a designer who needs Sass |
| Database | SQLite file, migrations only for `audit_requests` and `chat_messages` | Multiple writers, reporting queries, or a hosted MySQL/Postgres instance |
| Admin UI | None; submissions are read from the database | More than a handful of audits a week |
| Authentication | None on the public site | Any client-facing portal or per-client reporting |
| Assistant evaluation | Fixture parity and routing tests only | A model upgrade that changes tone or intent boundaries |
| Market sizing | Qualitative wedge only | A fundraising process that requires a sourced TAM/SAM/SOM |
| Office branding | Paragraph and table styling in generated DOCX/PPTX, no `.dotx`/`.potx` template files | Brand team wants locked templates with protected styles |
| Print | Browser print via `print.css` | A designed PDF that must match the DOCX exactly |

## 8. Deliberately absent

No TypeScript, no bundler, no CSS framework, no component library, no
authentication scaffold, no queue worker in the request path, and no analytics
that fire before consent. The repository's own rule is that a tool earns its
place in the request path or in CI; everything else is optional tooling.
