# Repository topology

The project is being split into three cooperating repositories, plus the
private mirror used for work in progress. The main site publishes, the Docs
repo authors, and the product repo ships EmploStart.

| Repo | Role | Publishes |
| --- | --- | --- |
| `Emplowise-Website-Phase-I` (main) | B2B conversion site, the `/emplostart` launcher, the showcase pages and the `/docs` hub | `gh-pages` → GitHub Pages |
| `Emplowise-Docs` (staged in `_staging/Emplowise-Docs/`) | Single source of truth for `.md`, canonical `brand-tokens/`, DOCX/PPTX generators, built `exports/`, sync scripts | internal |
| `EmploStart` (staged in `_staging/EmploStart/`) | Standalone, permanently free career utility with a deterministic Non-AI default and opt-in AI | its own GitHub Pages site |
| `Emplowise-repo-qodar-ai-work-in-progress` | Private 1:1 mirror of the main site: every branch, tag and `gh-pages` | nothing (publisher disabled) |

The main site consumes the Docs repo outputs: `brand-tokens/tokens.css` lands
in `public/css/tokens.css`, and `exports/*.docx|*.pptx` land in
`public/downloads/`. `scripts/sync.py check` fails when the copies drift. The
rest of this document describes the main site itself; the mirror workflow above
applies to it.

## The main site and its private mirror

The site lives in two GitHub repositories. The original publishes; the copy is an
exact replica, so a clone of it works on its own with no reference back to the
original.

| | Original (source of truth) | Copy (1:1 mirror) |
| --- | --- | --- |
| URL | https://github.com/Diocese-trees/Emplowise-Website-Phase-I | https://github.com/Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress |
| Visibility | public | private |
| Published by | `.github/workflows/publish-static.yml` → `gh-pages` → GitHub Pages | nothing (publisher disabled) |
| `main` written by | maintainers, via pull request or direct push | the mirror run only |

The copy is a full mirror, not a fork: `main`, `gh-pages`, every branch and
every tag match the original by commit id. Nothing is authored in the copy. A
mirror run replaces `main` and `gh-pages`, adds refs that are missing and
**deletes refs the original does not have**.

## Keeping the copy in sync

A mirror run is a `--mirror` clone of the original followed by a `--mirror`
push to the copy:

```bash
rm -rf /tmp/emplowise-mirror.git
git clone --mirror https://github.com/Diocese-trees/Emplowise-Website-Phase-I.git /tmp/emplowise-mirror.git
git -C /tmp/emplowise-mirror.git push --mirror https://github.com/Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress.git
```

`push --mirror` is a forced update by design: it rewrites `main`, moves
`gh-pages`, and prunes branches and tags that no longer exist upstream. That is
what makes the copy 1:1; it is also why nothing is ever committed there.

Verify the result from any clone that has a `wip` remote. Both diffs must be
empty:

```bash
diff <(git ls-remote --heads origin | sort) <(git ls-remote --heads wip | sort)
diff <(git ls-remote --tags origin | sed 's/\^{}//' | sort -u) \
     <(git ls-remote --tags wip | sed 's/\^{}//' | sort -u)
```

The read-only CI workflow runs in the copy on every mirrored `main` and is
expected to match the original's result. The publisher stays disabled as a
repository setting, so it survives each ref update:

```bash
gh workflow disable "Publish static site" \
  --repo Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress
```

### Work in progress belongs in the original

Because a mirror run deletes refs the original does not have, a branch pushed
only to the copy disappears on the next run. Push experiments to the original:

```bash
git push origin my-experiment
```

If an experiment already exists only in the copy, move it upstream before the
next mirror:

```bash
git fetch wip my-experiment
git push origin refs/remotes/wip/my-experiment:refs/heads/my-experiment
```

## Working with both repositories

The maintainer clone has two remotes:

| Remote | Points at | Use |
| --- | --- | --- |
| `origin` | the original | normal work; `git push origin main` publishes |
| `wip` | the copy | read-only inspection only; never push |

```bash
git fetch wip --tags
git log --oneline wip/main -5          # inspect the mirror
git diff --stat wip/main origin/main   # must be empty after a mirror run
```

To restore a lost ref, use the `backup/*` tags on the original (for example
`backup/pre-showcase-push-20261123-004151`) or the maintainer's bundle backups.
The copy is not a recovery source: it always follows the original.

## The publisher is disabled in the copy

The original's `publish-static.yml` runs on every push to `main`. In the copy it
would fail every sync, because it requires a `WEB3FORMS_ACCESS_KEY` secret and
the copy has no site to publish. It is disabled with
`gh workflow disable "Publish static site" --repo Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress`.

To publish a demo from the copy instead, enable it, add the secret, and point
GitHub Pages at `gh-pages`:

```bash
gh workflow enable "Publish static site" \
  --repo Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress
gh secret set WEB3FORMS_ACCESS_KEY \
  --repo Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress
```

Two caveats if you do: the page URLs baked into that build use the copy's own
name (`...github.io/Emplowise-repo-qodar-ai-work-in-progress`), and the demo will
then compete with the original's Pages site for search indexing.

## Site shell

The rendered shell is three partials: `resources/views/partials/header.blade.php`
(brand, one `Company` dropdown built from `config('emplowise.footer_company')`,
the EmploStart product link, Contact and the audit CTA), `footer.blade.php`, and
`errors/404.blade.php`. The Company group is a native `<details>` element, so it
opens without JavaScript on desktop, in the mobile drawer and in the left rail.
`ExportStatic` mirrors the 404 view to `404.html`.

## Interactive components

- **Showcase pages** (`pages/company-profile.blade.php`, `pages/portfolio.blade.php`,
  `pages/pitch-deck.blade.php`, `pages/docs.blade.php`, `css/showcase.css`):
  four long-form surfaces share one stylesheet. Each starts with a sticky jump
  navigation whose offset follows the desktop rail and the mobile header, plus a
  download link to its Word or PowerPoint file. The `/docs` hub indexes every
  deliverable, with a live-edition ghost button for the three commercial routes
  and an editable download for each document. The deck adds a sticky `01 / 08`
  counter, 44px prev and next controls, an `aria-pressed` presentation mode and a
  deferred `js/deck.js` that handles arrows, Escape, an IntersectionObserver and
  reduced motion. Slides declare their own ground colours, so dark mode cannot
  invert them, and print is A4 landscape with one slide per page. No showcase
  page carries a `[To Verify]` marker.
- **Team showcase** (`partials/team-showcase.blade.php`): one card with a
  `role="tablist"` row and three tabpanels. No simulated browser chrome or
  stacked-card props; placeholder portraits render an initials monogram
  (`.team-avatar`) at the same 4:5 ratio as a real photo. Arrow keys, Home and
  End move between tabs; colours come from the shared surface/ink tokens so the
  names stay readable in dark mode.
- **FAQ accordion** (`js/faq.js`): each `.acc-trigger` has an `id`, and its
  panel is a `role="region"` labelled by that trigger and marked `hidden` until
  opened. Click toggles, Escape collapses the open item, and the search filter
  keeps `aria-expanded` and `hidden` in sync when it hides items.
- **Contact** (`pages/contact.blade.php`): email is the only verified direct
  channel, so every action is a `mailto:`; no `tel:` is invented. `.btn--sm`,
  contact chips, the email link and footer social buttons all meet the 44px
  target.
- **Audit wizard** (`pages/audit.blade.php`, `js/audit.js`): four `.wizard-step`
  sections with a `novalidate` form, so inline errors are the only validation
  surface. `#wizard-label` is `aria-live="polite"` and updates as steps change;
  each invalid control gets `aria-invalid` plus `aria-describedby` pointing at
  its `.field-error` (which stays `role="alert"`), and the first invalid control
  is focused. While a request is in flight the form carries `aria-busy="true"`
  and the submit button is disabled and relabelled; drafts live in
  `sessionStorage` (`emplowise_audit_draft`) behind `try`/`catch` with a guarded
  `JSON.parse`. A keyless preview build never posts: it states that online
  booking is not connected and offers the fallback `mailto:`.
- **Chat widget** (`js/chat.js`): `window.EmplowiseChat.intent/reply` is exposed
  as soon as the file evaluates (pure functions only), so the routing parity
  harness can run without touching the DOM. All panel wiring (open/close,
  focus trap, Escape, suggestions, send) is inside `bind()`, which runs on the
  first launcher click, `pointerenter`, focus, `[data-open-chat]` click, or a
  `requestIdleCallback` slot (1200 ms timeout fallback). Reply rendering uses
  `textContent` and `createElement`; the only `innerHTML` writes are empty
  strings that clear a container.
- **EmploStart launcher** (`pages/emplostart.blade.php`, `css/pages.css`):
  six flat cards from `emplostart_modules`, a "what runs where" disclosure, and
  a prominent link to the standalone app at `emplowise.emplostart_url`. The Fair
  Jobs and Fair Wages band comes from `fair_jobs_pledge`. The page carries no
  tool panels, no engine scripts, and no `/api/student/*` routes; the app owns
  all of that runtime in its own repository.

## Visibility

The copy is private. To make it public:

```bash
gh repo edit Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress \
  --visibility public --accept-visibility-change-consequences
```

A public copy exposes its Actions logs and the full history, including any
branch you pushed there for safekeeping. Treat it as a publication step.
