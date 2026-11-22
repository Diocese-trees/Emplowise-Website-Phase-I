# Changelog

Notable changes, newest first. Short hashes refer to commits in
[the original repository](https://github.com/Diocese-trees/Emplowise-Website-Phase-I);
each commit body carries the detail. Dates are the commit dates.

## Unreleased

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
