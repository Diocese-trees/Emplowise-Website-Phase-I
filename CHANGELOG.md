# Changelog

Notable changes, newest first. Short hashes refer to commits in
[the original repository](https://github.com/Diocese-trees/Emplowise-Website-Phase-I);
each commit body carries the detail. Dates are the commit dates.

## v2.1 — Brand kit and showcase pages (2026-11-23)

Tag: `v2.1-brand-kit-and-showcase`. The CI repair below shipped earlier in the
same release line.

- Added three long-form pages on one shared stylesheet: `/company-profile`
  (company facts, buying situations, scope, method, services, pricing,
  leadership), `/portfolio` (evidence policy, four shipped artifacts, systems
  table, case-study standard) and `/pitch-deck` (eight grounded slides). Each
  page carries its own download, a sticky jump navigation that clears the
  desktop rail and the mobile header, and no `[To Verify]` marker.
- Made the deck interactive in 3.0 KB of deferred JavaScript: a live `01 / 08`
  counter with `aria-live`, 44px prev/next buttons, an `aria-pressed`
  presentation mode, arrow and Escape keys, an IntersectionObserver scroll sync
  and reduced-motion awareness. Every slide pins its own ground colours, so
  dark mode cannot invert navy or white slides. Print is A4 landscape with one
  slide per page; the page head and ambient background are dropped.
- Extended the static export to fourteen pages, added `css/showcase.css` and
  `js/deck.js` to the copied assets, kept `/pitch-deck` on `noindex,follow` and
  out of the sitemap, and left the five legacy redirects in place.
- Linked Company Profile, Portfolio and Pitch Deck from the shared footer
  groups. The header Company dropdown keeps its four marketing routes.
- Added Feature coverage for the three routes (unique titles, one `h1` each,
  no internal markers, readable DOCX/PPTX packages) and for the export (showcase
  assets, seven downloads, base-prefixed links, sitemap exclusion). The CI smoke
  test now asserts the three pages, the showcase assets and the deck `noindex`.
- Reconciled the documents with the built site: PORTFOLIO, PITCH_DECK,
  TOOL_STACK, THEME_AND_MOTION, README, STATIC_HOSTING and REPO_TOPOLOGY now
  state fourteen pages, 62 tests and 922 assertions, twelve bundle sources and
  three CI parity harnesses. Rebuilt the six DOCX files and the eight-slide PPTX
  through the verified pipeline.
- Release hygiene. Tagged the pre-merge `main` `backup/pre-showcase-push-20261123-004151`,
  wrote a verified git bundle and a worktree archive outside the repository,
  then fast-forwarded `main` to the release commit and pushed `main`, the
  release tag and the backup tag to `origin`.

Release scorecard (local export, 2026-11-23):

| Check | Result |
| --- | --- |
| Pages | 14 rendered, 5 redirects, 404, robots, sitemap (13 entries) |
| SEO audit | 12 configured routes, all titles and descriptions in range |
| Feature suite | 62 tests, 922 assertions |
| Parity | interview 11/11, ATS 6/6, profile 5/5, chat routing 48/48 |
| CSS bundles | `site.css` 133.5 KB, `hub.css` 19.4 KB, both fresh |
| Showcase assets | `showcase.css` 25.3 KB, `deck.js` 3.0 KB deferred |
| Office files | 6 DOCX + 1 PPTX, `verify-office` pass, 55.8–80.1 KB |
| Downloads | 7 files copied with base-prefixed links |
| Accessibility | One `h1` per page, visible focus, 44px controls, AA contrast on every slide ground |

## v2.1 — CI repair (2026-11-22)

- Tracked the database tree. `database/.gitignore` ignored everything, so the
  migrations, `UserFactory` and `DatabaseSeeder` had never been committed: local
  runs passed because the files sat on disk, while the first CI run cloned
  without them and `RefreshDatabase` could not create `audit_requests` or
  `chat_messages` (4 failures, 500 instead of 201/200). The ignore file now
  covers only `*.sqlite`/`*.sqlite-journal`, and the five migrations, the
  factory and the seeder are in the repository. Verified in a detached worktree
  containing tracked files only: 60 tests pass, bundles are fresh, parity and
  the keyless export smoke test pass.

## v2.0 — Executive redesign (2026-11-22)

- Final verification pass. Lighthouse (mobile and desktop) on `/`,
  `/emplostart/` and `/free-hr-audit/` now scores 100 for accessibility, best
  practices and SEO, with performance 95–100. Two fixes came out of it: links
  inside `.form-status` are underlined (the keyless mailto link sat on the error
  red with a 1.16:1 colour difference), and footer navigation links are
  `min-height`/`min-width: 28px` so they clear the WCAG 2.2 target-size rule.
  No horizontal overflow at 375px, and the footer capture shows the five-link
  columns unchanged apart from the extra row height.
- Release hygiene. Deleted the two placeholder tests
  (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`) and the now
  empty `Unit` suite from `phpunit.xml`; the Feature suite is the contract (60
  tests, 827 assertions). `docs/screenshots/` is git-ignored and untracked
  (`git rm -r --cached`), and the README no longer embeds the 22 image binaries.
  Added `.github/workflows/ci.yml` (push to `main`, PRs, manual): bundle
  freshness (`git diff --exit-code` after `emplowise:bundle-css`), `node
  --check` on every script, SEO audit, tests, interview/ATS/profile parity, and
  a keyless static-export smoke test asserting `index.html`, `404.html`, the
  audit form and the CSP meta. README, `REPO_TOPOLOGY`, `THEME_AND_MOTION` and
  `AGENTS.md` were reconciled (Laravel 13, 60 tests, CI entry, local-only
  captures).
- Turned the EmploStart tool rail into a sticky segmented bar on small screens.
  Below 1000px it pins under the header (`position: sticky`, `--z-sticky`) in a
  five-column grid; below 760px it becomes one horizontal scroll row with
  scroll snap, a hidden scrollbar and a bottom active underline, so the active
  panel stays near the fold instead of sitting below a three-row tab grid.
  `.hub-panel` gained scroll margins that clear the pinned rail, and dark mode
  now paints inactive tab numbers with `--d-accent` (measured
  `rgb(94,234,212)` on `rgba(10,10,11,0.9)`). Resume experience/education rows
  and their empty states are now built with `createElement`/`textContent`
  rather than templated `innerHTML`, and every remaining `innerHTML` write is
  an empty-string clear. Verified at 360x640, 375x667, 1366x900 and 1440x900:
  15/15 headless checks (no page overflow, pinned rail clears the header,
  panel scroll margins, tab/hash switching, example load, hostile text rendered
  as text, reset clearing storage, blocked-storage fallback, prompt filter) with
  zero console errors; interview/ATS/profile parity 22/22 fixtures. 62 tests,
  829 assertions.
- Hardened the audit wizard and deferred the chat widget. The step label is
  now `aria-live="polite" aria-atomic="true"`, so moving between steps is
  announced; while a submission is in flight the form carries
  `aria-busy="true"` and the button is disabled and relabelled, and both clear
  in `finally`. The wizard now refers to its own `form` node throughout rather
  than the module-level lookup (seven call sites), and the new
  `test_audit_wizard_exposes_progress_and_autocomplete_hooks` locks the
  `novalidate`, `aria-live`, four-step and autofill contract. Chat wiring
  moved inside `bind()`: nothing touches the panel on first paint, and binding
  happens on the first launcher click, `pointerenter`, focus, a
  `[data-open-chat]` click, or an idle slot (`requestIdleCallback` with a
  1200 ms `setTimeout` fallback). `window.EmplowiseChat.intent/reply` is still
  assigned immediately, so the parity harness is unaffected. Verified on a
  keyless export and a send-mode copy: 21/21 headless Chromium checks
  (inline-error focus, draft restore, keyless preview never posts, busy state
  during a 700 ms delayed send, success panel, lazy chat open and local reply,
  zero console errors on `/`, `/emplostart/`, `/student-hub/` and the audit),
  plus 48/48 chat routing cases. 62 tests, 826 assertions.
- Rebuilt the team showcase as an honest card. The simulated macOS window bar
  (traffic-light dots, fake `team.emplowise` URL, meta label) and the two
  rotated stacked-card layers behind it are gone, along with their CSS and the
  three now-unused `copy.team.window_*` keys and the duplicate `kicker` key.
  Tabs, window and photo frame now use `--surface*`, `--border-subtle` and
  `--ink-*`, so tab names are readable in dark mode (measured
  `rgb(233,239,248)` on `rgb(8,24,47)`); the number and role accents switch to
  `--d-accent`/`--d-ink-dim`. The FAQ accordion gained `id`-linked triggers,
  `role="region"` panels with `aria-labelledby`, a real `hidden` state that the
  filter keeps in sync, and Escape-to-collapse. Contact keeps email as the only
  verified channel (no invented `tel:`), and `.btn--sm`, contact chips, the
  email link, `.filter-btn` and the search clear button are now 44px targets.
  Above-the-fold check on a local export: the home h1 and hero CTA sit inside
  the fold at 1366x768 (CTA bottom 545), 1440x900 (546) and 375x667 (591); the
  page-head on `/what-we-are` ends at 466/589 for those viewports. 61 tests,
  814 assertions; headless Chromium shows zero console errors at all six
  light/dark viewport runs.
- Made the theme pre-paint safe without loosening the CSP. The palette is now
  applied by a single inline script emitted from
  `App\Support\ThemeBootstrap::SCRIPT`; the CSP (`script-src 'self'`) carries
  its exact SHA-256 hash, appended by `SecurityHeaders::policy()` for Laravel
  responses and by `ExportStatic::cspMeta()` for the static `<meta>`. `theme.js`
  is `defer` and handles only toggles, labels and `localStorage`; the 404 page
  gets the same bootstrap. The latin-ext `@font-face` blocks and their two
  `.woff2` files were removed (English-only copy), so each weight is one font
  request. `SecurityHeadersTest` recomputes the hash from the rendered `<head>`;
  `ExportStaticTest` asserts the bootstrap and its hash on every page.
  Measured on a local export: `/` 178.6 KB CSS+JS (site.css 134.5 KB), `/emplostart`
  320.2 KB including `hub.css` and the toolkit; no `hub.css` or toolkit JS on any
  other page. Headless Chromium under the static CSP: zero console errors on
  `404.html`, `/`, `/emplostart/`, `/free-hr-audit/`; dark preference applies
  before the stylesheet; toggle persists across reload. 60 tests, 760 assertions.
- Locked the redesign workflow and recorded the baseline. `CLAUDE.md` was a
  byte-identical duplicate of the Laravel Boost placeholder `AGENTS.md` (both
  1,446 B) and is deleted; `AGENTS.md` is now the rulebook with the three exact
  Artisan commands, the test and parity commands, the SEO copy freeze, the
  navy/teal + Montserrat/Plus Jakarta Sans brand lock, the read denylist, and the
  edit -> bundle -> test -> docs -> commit loop. Baseline on tag
  `pre-redesign-backup` (`b08d31b`): 58 tests / 742 assertions, SEO audit 9/9,
  parity 6 ATS + 11 interview + 5 profile + 48 chat. Grep inventory for the
  planned redesign: 270 hardcoded colour declarations outside
  `tokens.css`/`theme.css`/generated bundles, 31 `z-index` rules (`300, 220,
  140, 120 x2, 100, 95, 40, 1, -1, -2`), 20 `innerHTML`, 7 `localStorage`, and 3
  `fetch(` sites, one root-relative `/api/audit` fallback in `audit.js`, and zero
  `Student Hub` strings. Work branch: `feat/emplowise-redesign`.
- Closed the static API boundary and made the keyless audit state actionable.
  Every `/api/*` route now has a client-side fallback that runs before any
  request: `chat.js` checks for `data-endpoint`, `student.js` checks each
  endpoint before `postJson()`, and `audit.js` checks
  `data-audit-mode="web3forms"`. The audit form now carries
  `data-fallback-email` (from `config/emplowise.php`); a keyless export shows
  "Online booking is not connected in this preview build" with a `mailto:` link
  on arrival and on submit instead of failing silently. New assertions in
  `ExportStaticTest` scan every exported HTML file for `/api/chat`,
  `/api/audit`, `/api/student/`, and `/api/csp-report`, and check the email
  fallback. Headless Chromium against a local export confirmed zero `fetch()`
  calls, zero `/api/` requests, and zero console errors on `/free-hr-audit/`,
  `/`, and `/emplostart/` (ATS run included). 58 tests, 742 assertions.
  `docs/STATIC_HOSTING.md` documents the endpoint matrix.
- Split the layout and the EmploStart page into partials, with no rendered change.
  `layouts/app.blade.php` now includes `partials/header.blade.php` and
  `partials/footer.blade.php`, both cut verbatim from the layout (3,429 and 2,834
  bytes). The three tool tabs on `/emplostart` moved to
  `partials/emplostart/{interview,ats,profile}.blade.php` (110/88/59 lines) and
  the page fell from 491 to 237 lines (3 insertions, 257 deletions). Resume and
  Prompts panels stay inline, since they are not among the three named tabs.
  Panel comments stay in the parent: Laravel 8's view engine `ltrim`s included
  output, so an indented comment inside a partial would shift whitespace. Both
  refactors were proven by rendering before and after and diffing with the
  per-session CSRF token and dev port normalised — identical for `/`,
  `/contact`, `/what-we-are` and `/emplostart`. 56 tests pass. README views tree
  updated. Backup branch: `backup/main-pre-partials-2026-11-22` (at `776dc65`).
- Replaced the team placeholder portraits with a CSS initials avatar. The
  showcase checks `portrait` for `placeholder-`; while it matches, each panel
  shows initials derived from the profile name (`MA`, `AH`, `SN`) in a 4:5 block
  instead of the stand-in image, and flips back to the real `<picture>` as soon
  as a confirmed headshot path is set. Styling lives in `pages.css` (new
  `.team-avatar` rules; inline CSS is blocked by the export CSP) and removes
  about 44 KB of portrait WebP from the team page along with three image
  requests. `team.js` was left alone: it is a full ARIA tab widget with roving
  `tabindex` and arrow/Home/End keys, not a minor toggle, and it loads from
  `pages/what-we-are.blade.php`, not the layout. Two assertions that encoded the
  placeholder markup were updated. 56 tests pass. `CONTENT_EDITING_GUIDE.md`
  documents the swap. Backup branch: `backup/main-pre-team-avatars-2026-11-22`
  (at `ca5636e`).
- Cleaned up favicons and social cards. The head now declares exactly two icons,
  both at the site root (`favicon.ico` any size, `favicon.png` 32×32), and the
  duplicate `public/img/favicon.png` is deleted. Open Graph gained
  `og:image` + `og:image:alt`, the Twitter card moved to
  `summary_large_image`, and `twitter:image` + `twitter:image:alt` were added,
  defaulting to `img/logo-primary.png` and overridable per page with
  `@section('og_image')`. `ForcePublicUrl` was collapsing same-origin absolute
  URLs, so `og:image` and `twitter:image` were added to its keep list next to
  `canonical` and `og:url`; image URLs stay absolute in both the app and the
  static export, including subdirectory bases. `public/robots.txt` now points at
  an absolute `Sitemap:` URL (the exported robots.txt already built one from
  `--base`). Verified over real HTTP and a static export: absolute
  `og:image`/`twitter:image`, two icon links, 56 tests pass, SEO audit 9/9.
  Backup branch: `backup/main-pre-seo-meta-2026-11-22` (at `fc8b475`).
- Added a real 404 page. `resources/views/errors/404.blade.php` extends
  `layouts.app` and shows a 404 kicker, one `<h1>`, and links to Home, the Free
  HR Audit and Contact. A missing route now returns 404 with the full site
  chrome instead of a bare error. `ExportStatic::notFound()` was aligned to the
  same three links; the command already wrote `404.html` at the build root, so
  GitHub Pages serves it for unknown paths. Verified: dynamic 404 is 404 with one
  h1 and all three links; exported `404.html` carries base-prefixed links; 56
  tests pass. Backup branch: `backup/main-pre-404-page-2026-11-22` (at
  `2401958`). README views tree updated.
- Inlined the SEO `<h1>`. `resources/views/partials/seo-h1.blade.php` is deleted;
  every template in `resources/views/pages/` now renders its own `<h1>` from
  `$seo['h1']` and `$seo['h1_accent']` with the same markup, so each page has
  exactly one visible semantic heading and the existing `.hero h1` /
  `.page-head h1` styles still apply. The rendered `<h1>` is byte-identical to
  the previous build on all 11 exported pages (1 h1 and 1 accent each, no page
  with zero or multiple). Backup branch: `backup/main-pre-h1-inline-2026-11-22`
  (at `636e8db`). `CONTENT_EDITING_GUIDE.md` notes that the copy keys drive the
  markup now; 56 tests pass and the SEO audit is 9/9.
- Removed the intro splash. `resources/views/partials/intro.blade.php`,
  `public/css/intro.css` and `public/js/intro.js` are deleted; the layout no
  longer includes the partial or loads the script, and `intro.css` is out of
  the `site.css` manifest (bundle 139.7 KB → 132.8 KB). Dead references went
  with it: the `intro-pending` / `emplowise:intro-done` branch in `consent.js`,
  the dark `.intro` rule in `theme.css`, and the stale comment in
  `consent.css`. The consent card now opens ~700ms after load on a first visit
  instead of waiting for an intro event. Backup branch:
  `backup/main-pre-intro-removal-2026-11-22` (at `bf37387`). README source
  counts and `docs/THEME_AND_MOTION.md` updated; all 56 tests pass.
- UX refresh: the landing page is now a map, the chrome is slimmer, and dark
  mode is true black.
  - **Cookie choice on arrival.** With no stored `emplowise:consent` value, the
    preferences card opens by itself shortly after load (~700ms), so every first
    visit gets a choice. The floating `.consent-fab` pill, its CSS
    and its JS timers were removed. Reopening lives in the footer's
    "Cookie settings" button.
  - **Legal pages.** `/terms` and `/privacy` are real pages driven by
    `config('emplowise.seo.terms/privacy')` and
    `config('emplowise.copy.legal.terms/privacy')`, rendered through the shared
    `pages/legal.blade.php` (numbered sections, sticky "On this page", cross
    links, updated date). They are in the sitemap and the static export, which
    now emits 11 pages. Footer links: Terms, Privacy, Cookie settings.
  - **Compact footer.** Reduced top and bottom padding, a two-sentence bio,
    smaller type and social icons, a single legal row, and no bottom space
    reserved for the removed floating pill.
  - **AMOLED dark mode.** `--d-page` is `#000`, surfaces are neutral graphite
    (`#0a0a0b` / `#101012` / `#17171a`), lines are white-alpha, and the hero,
    navy bands and footer use near-black gradients with the teal/indigo glows
    retained. Brand accents, the reverse logo and contrast ratios (17.0:1 body,
    9.01:1 secondary) are preserved. `theme-color` is `#000000` in dark.
  - **Visible motion.** A CSS-only `hero-in` entrance staggers the hero copy and
    panel on load; shortcut and pillar cards lift and slide their arrows on
    hover. All of it is inside `prefers-reduced-motion: no-preference`.
  - **Desktop left rail.** From 1200px the header becomes a fixed 184px column:
    brand at the top, vertical nav, theme toggle and audit CTA pinned to the
    bottom, and `body { padding-left: 184px }`. The consent card sits beside the
    rail. The top bar and mobile drawer are untouched at 1199px and below, which
    keeps the change CSS-only and easy to roll back.
  - **Less white space, clearer paths.** Section padding fell from
    `clamp(4rem, 8vw, 7.5rem)` to `clamp(3rem, 5.5vw, 5rem)`, the hero and page
    heads are tighter, and the four-card `copy.home.shortcuts` grid (What we do,
    Pricing, EmploStart, Contact) fills the gap where the long scroll used to
    be. The home page no longer repeats the method, pricing or EmploStart
    sections; they remain on `/why-emplowise` and `/emplostart`.
  - Tests updated for 11 pages (page render, SEO snippets, diagnostics, sitemap
    count) and the static-export file list. Docs updated in
    `docs/THEME_AND_MOTION.md` and `CONTENT_EDITING_GUIDE.md`.
- Reworked the contact page's closing area and the shared footer:
  - `footer_note` is now SEO-facing brand copy — human resource partner,
    remote HR for teams of 5 to 100, free HR audit, HR Business Manager, and
    retainers from PKR 40,000 — with the grammar of the draft fixed. It is the
    same bio on every page, so `/contact` and the site footer read alike.
  - The contact meta description was rewritten to 143 characters (within the
    SEO audit's 135–155 range) and now matches the AI card's wording.
  - Removed "Market: Pakistan" and the long-term expansion line from the
    footer's Start here column, leaving the email as the single entry.
  - The footer bottom is now copyright plus `footer_tagline`
    ("People analytics without bias, so CEOs can set new standards."). The
    previous scope disclaimer is gone.
  - Removed the footer dark-mode and cookie buttons. Dark mode remains in the
    header and the mobile drawer. (The floating consent pill described in this
    pass was superseded by the UX refresh above: the card now opens on the first
    visit and "Cookie settings" lives in the footer legal row.)
  - The duplicate contact micro line ("Website is actively being built…") was
    removed; `micro_copyright` and `micro_status` no longer exist.
  - The closing AI card is compact at every breakpoint (max 56rem, 1.02–1.18rem
    heading, 0.82rem body, 0.72rem chips) and reads "Ask your favourite GPT
    about Emplowise". It remains the last block before the footer.
  - Docs updated in `docs/THEME_AND_MOTION.md` and
    `CONTENT_EDITING_GUIDE.md`; the contact test now checks the footer tagline
    instead of the removed micro keys.
- Reduced motion no longer flattens the whole interface: movement, loops and
  the intro's travel are removed, but 0.18s colour, opacity and shadow fades
  remain, so hover and focus still register. Reveals, the rotator and the hero
  parallax stay disabled in that mode by design.
- `?intro=1` replayed the intro regardless of the once-per-session flag, for
  demos and for checking the timing. (Superseded: the intro has since been
  removed, so the flag no longer does anything.)

## 2026-11-22 — documentation pass

- Added `docs/THEME_AND_MOTION.md`: theme selection and persistence, the toggle
  contract, the token strategy, a dark-mode checklist, branding derivatives, and
  the motion timeline.
- Added `docs/REPO_TOPOLOGY.md`: the original/copy relationship, the sync
  workflow, backup tags, and why `main` is bot-owned.
- Added this changelog, backfilled from the full history.
- Corrected the README asset inventory and added the styles pipeline section.
- Refreshed every screenshot and added light/dark pairs for all nine pages.
- Disabled the publisher in the copy so failed builds stop appearing on sync.

## 2026-11-22

**Added**

- Dark mode: pre-paint theme bootstrap, OS default, stored choice, toggles in the
  header, nav drawer and footer, a dark palette across every page and the audit
  tool, and reverse-logo derivatives with the alpha haze removed (`18ad800`).
- Team page and AI automation page on the static build (`88d57ec`).

**Changed**

- Slower motion: intro holds 1.65s and exits over 0.7s, scroll reveals 0.95s,
  tokens 0.22/0.42/0.95s, hero rotator 3.8s (`18ad800`).
- Contact page rebuilt: direct-line panel with hours, reply window and base, a
  three-step "what happens next", email compose/copy, social and resource cards,
  and a full-width AI shortcut strip (`18ad800`).
- Intro sequence, consent banner and social links; first-load weight cut
  (`c53a53d`).
- Pages renamed and the tagline system rebuilt (`ba03993`).
- EmploStart page and site copy rewritten (`f5bf9fa`), tools restored (`daeb87b`).

**Fixed**

- Static export rewrites `srcset` candidates under the project base, so the
  header logo resolves on GitHub Pages (`51e6210`).
- Accessibility across hub, chat, audit form and 404 (`e27ad1f`), audit wizard
  focus management and field errors (`2be44d7`).
- Chat suggestion chips mirror the server router offline (`288535c`).
- Toolkit line splitting counts characters, not bytes; ATS keyword score uses
  PHP rounding semantics (`fa92a79`, `d505b68`, `b7474fe`).

## 2026-11-21

**Added**

- SEO foundation: canonical URLs, social cards, JSON-LD, sitemap, robots, and an
  `emplowise:seo-audit` command (`78aa556`, `f2f3273`).
- HSTS with `preload` on HTTPS (`0845185`) and consistent frame denial in the app
  and at the edge (`4b231ff`).
- Student Hub planning set: research, strategy and upgrade plan (`39221cb`).
- EmploStart upgrades: interview score dimensions and next probe (`8729e2c`),
  ATS synonyms and title checks (`db8576d`), local resume autosave (`67c6b82`),
  profile target role (`7b36546`), chat intent scoring (`2af7c3a`), prompt
  category counts (`f0ab597`).
- Browser ports of the interview, ATS and profile engines (`cd1ed39`), static
  publishing with a real form (`10e6ca1`).

**Changed**

- Strict heading order, touch targets and anchor offsets (`656a8dc`).
- Internal jargon replaced with plain scoping wording (`b853471`).
- Editing guide updated for the new controls (`972d079`).

**Fixed**

- Client scripts, scroll locking and mobile form ergonomics (`a2d724f`).
- Toolkit parity: offline chat (`22b8cee`), rendering without sessions or a
  database (`73d6d4f`), hyphen and mixed-script parity tests (`7acae6b`).

**Tests**

- SEO tags, sitemap, asset scoping and heading order (`da1886d`).

## 2026-11-20

- Initial import of the Laravel site, README/licence merge and the demo badge
  (`4a1d711`, `96f3d12`, `9d5660e`, `cbc447d`, `e4b5679`).
- Student tools surface the API message instead of a bare error (`b4bf66b`).

## Conventions

- `feat`, `fix`, `chore`, `docs`, `test`, `ci` prefixes; one concern per commit.
- Backups before a risky change: a git tag, a `git bundle` and a worktree archive
  stored outside the repository.
- Update this file in the same change set as the behaviour it describes.
