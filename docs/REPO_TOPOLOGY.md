# Repository topology

The project lives in two GitHub repositories. They share history, but only one
publishes the site, and only one is written by a bot.

| | Original (source of truth) | Copy (work in progress) |
| --- | --- | --- |
| URL | https://github.com/Diocese-trees/Emplowise-Website-Phase-I | https://github.com/Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress |
| Visibility | public | private |
| Published by | `.github/workflows/publish-static.yml` → `gh-pages` → GitHub Pages | nothing (publisher disabled) |
| `main` written by | maintainers, via pull request or direct push | the sync workflow, plus maintainers on any other branch |

The copy is a full mirror, not a fork: it holds all branches and tags, so its
clone works on its own, and nothing about the original depends on it.

## Continuous integration

`.github/workflows/ci.yml` runs on pushes to `main`, on pull requests and by
manual dispatch. It fails if the regenerated bundles differ from the committed
`site.css`/`hub.css`, checks JavaScript syntax, runs `emplowise:seo-audit` and
the Feature suite, replays the interview/ATS/profile parity fixtures, and
renders a keyless static export with `index.html`, `404.html`, the audit form
and the CSP meta asserted. It has read-only permissions; only
`publish-static.yml` writes `gh-pages`.

## Keeping the copy in sync

`.github/workflows/sync-from-upstream.yml` runs **every two hours at :23** and on
demand. Each run:

1. checks out the copy's `main` and fetches the original as a read-only
   `upstream`;
2. stops if `main` already contains upstream's head;
3. tags the copy's current head `backup/pre-sync-<UTC stamp>` and pushes that tag;
4. merges upstream with a merge commit — `--no-ff`, never a reset or force push;
5. aborts the merge and pushes nothing if the two sides conflict;
6. pushes `main`.

Only `main` is touched. Other branches in the copy are yours and are never read
or written by the workflow. The job also refuses to run outside this repository,
and `concurrency` prevents overlapping runs.

Manual run, including a no-change dry run:

```bash
gh workflow run sync-from-upstream.yml \
  --repo Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress

gh workflow run sync-from-upstream.yml \
  --repo Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress \
  -f dry_run=true
```

### Why pushes to the copy's `main` are rejected

Because the bot commits merges, the copy's `main` is always ahead of the
original's `main` by at least the workflow file. A local `main` is therefore
never a fast-forward against it, and `git push wip main` fails by design. Push
work-in-progress to a **branch** instead:

```bash
git push wip my-experiment        # a branch, not main
```

To bring such a branch back into the original later, open a pull request from
the copy to the original, or cherry-pick locally and push to `origin`.

## Working with both repositories

The maintainer clone has two remotes:

| Remote | Points at | Use |
| --- | --- | --- |
| `origin` | the original | normal work; `git push origin main` publishes |
| `wip` | the copy | WIP branches only |

To inspect the copy or restore a pre-sync snapshot (list the tags first with
`git tag --list 'backup/pre-sync-*'`):

```bash
git fetch wip --tags
git switch --detach backup/pre-sync-<stamp>   # read-only view
git switch -c rescue backup/pre-sync-<stamp>  # or a branch to work from
```

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
  `pages/pitch-deck.blade.php`, `css/showcase.css`): three long-form pages share
  one stylesheet. Each starts with a sticky jump navigation whose offset follows
  the desktop rail and the mobile header, plus a download link to its Word or
  PowerPoint file. The deck adds a sticky `01 / 08` counter, 44px prev and next
  controls, an `aria-pressed` presentation mode and a deferred `js/deck.js` that
  handles arrows, Escape, an IntersectionObserver and reduced motion. Slides
  declare their own ground colours, so dark mode cannot invert them, and print
  is A4 landscape with one slide per page. No showcase page carries a
  `[To Verify]` marker.
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
- **EmploStart tool rail** (`js/student.js`, `css/student.css`): five
  `role="tab"` buttons with arrow/Home/End navigation. Below 1000px the rail is
  `position: sticky` under the header in a five-column grid; below 760px it
  becomes one horizontal scroll row (`scroll-snap-type: x proximity`, hidden
  scrollbar) so the panel stays near the fold. `.hub-panel` scroll margins clear
  the pinned rail. Resume rows and empty states are built with
  `createElement`/`textContent` (no interpolated `innerHTML`), and the draft
  uses guarded `localStorage` get/set/remove so blocked storage still leaves a
  working builder.

## Visibility

The copy is private. To make it public:

```bash
gh repo edit Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress \
  --visibility public --accept-visibility-change-consequences
```

A public copy exposes its Actions logs and the full history, including any
branch you pushed there for safekeeping. Treat it as a publication step.
