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

## Visibility

The copy is private. To make it public:

```bash
gh repo edit Diocese-trees/Emplowise-repo-qodar-ai-work-in-progress \
  --visibility public --accept-visibility-change-consequences
```

A public copy exposes its Actions logs and the full history, including any
branch you pushed there for safekeeping. Treat it as a publication step.
