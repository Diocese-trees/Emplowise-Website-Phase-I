# Changelog

Notable changes, newest first. Short hashes refer to commits in
[the original repository](https://github.com/Diocese-trees/Emplowise-Website-Phase-I);
each commit body carries the detail. Dates are the commit dates.

## Unreleased — documentation pass

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
