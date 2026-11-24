# Vercel deployment

Vercel hosts the same static export that GitHub Pages serves. Laravel is not
deployed there: Vercel has no PHP runtime, the site needs none at request time,
and running the framework in a serverless function would add cold starts and a
maintenance dependency for no gain.

Publishing the export as files also restores two things GitHub Pages cannot
give: real HTTP response headers, and server-side redirects that answer once
instead of shipping a meta refresh.

## How it fits together

```
push to main
  -> GitHub Actions renders build/static with --base=$STATIC_BASE_URL
       checks the render, copies vercel.json in
       publishes it one of two ways (below)
  -> Vercel serves the files, applying vercel.json
```

Nothing is compiled on Vercel's side. The project must therefore stay on the
**Other** framework preset with no build command: a build command would try to
run PHP the build image does not have.

## Choose how Vercel gets the files

| | A. Git integration branch | B. CLI from Actions |
| --- | --- | --- |
| How it updates | Vercel watches the `vercel-static` branch | The workflow calls `vercel deploy` |
| Secret needed | None beyond `GITHUB_TOKEN` | `VERCEL_TOKEN` |
| Extra branch | `vercel-static` | None |
| Vercel dashboard deployments | Yes, each push is a deployment | Yes, but not tied to a commit |
| Best for | Hands-off operation after setup | Keeping Vercel out of Git |

Run one, not both. Both are dormant until a repository variable is set, so
having the files present changes nothing.

## Option A: Vercel updates itself from GitHub

This is the "connect the repo" route. Vercel cannot build PHP, so the rendered
files are committed to a dedicated branch and Vercel is pointed at that branch.

### 1. Add two repository variables

**Settings, Secrets and variables, Actions, Variables, New repository variable**

| Name | Value |
| --- | --- |
| `STATIC_BASE_URL` | The production origin, for example `https://emplowise.com`. No trailing slash |
| `VERCEL_BRANCH_ENABLED` | `true` |

`WEB3FORMS_ACCESS_KEY` must already exist as a secret, or the job stops before
publishing.

### 2. Render the branch

Run **Actions, Publish the Vercel build branch, Run workflow**. It renders the
site, runs the pre-flight checks, and force-pushes the result to `vercel-static`
as a single commit, so the branch never grows.

Confirm the branch exists before going further. On the repository home page,
open the branch selector and look for `vercel-static`.

### 3. Create the Vercel project

1. **Add New, Project, Import Git Repository**, and pick the repository.
2. Set **Framework Preset** to **Other**.
3. Clear **Build Command**, **Install Command** and **Output Directory**. The
   folder already contains the finished site.
4. Open **Settings, Git** and set **Production Branch** to `vercel-static`.
5. Open **Settings, Git, Ignored Build Step** and choose **Custom**, then paste:

   ```bash
   if [ "$VERCEL_GIT_COMMIT_REF" != "vercel-static" ]; then echo "Skipping: not the build branch"; exit 0; fi
   ```

   Exit `0` skips the build, exit `1` runs it. Without this, a push to `main`
   would queue a deployment that tries to build PHP and fails. The badge on the
   commit then shows a skipped deployment, which is the intended result.

6. Deploy once. The first deployment builds from the existing `vercel-static`
   commit.

### 4. Check the first deployment

Open the deployment URL and walk these five:

1. `/` renders with styles and fonts.
2. `/why-emplowise` redirects to `/why-emplowise/` and loads.
3. `/pricing` redirects twice and lands on the Why Emplowise page.
4. `/this-page-does-not-exist` returns the styled 404 page, not a Vercel error.
5. Submit the Free HR Audit form once with a real address and confirm the email
   arrives.

`curl -sSI https://your-domain/` should show `content-security-policy:
frame-ancestors 'none'`, `x-content-type-options: nosniff` and
`x-frame-options: DENY`.

### 5. Point the domain

1. **Settings, Domains, Add**, enter the apex and `www` names.
2. At the DNS provider, add the records Vercel shows (an `A` record for the
   apex, a `CNAME` for `www`).
3. Wait for the certificate. Vercel issues it automatically once DNS resolves.
4. Pick one canonical hostname in **Settings, Domains** so the other redirects.

### 6. Retire or keep the old host

If Vercel becomes canonical, disable the Pages workflow by deleting
`.github/workflows/publish-static.yml` or by removing its trigger, then turn
Pages off in **Settings, Pages**. Keeping both live on different hostnames
splits search signals.

If Pages stays canonical, leave this branch unpublished: set
`VERCEL_BRANCH_ENABLED` to `false` and use Vercel for previews only.

### 7. Day to day

Nothing to do. Push to `main`, and the chain runs itself:

```
push -> Actions renders and checks -> vercel-static updated -> Vercel deploys
```

A failure in the render or the checks stops the chain, and the branch is left
on the last known-good build, so a bad commit never reaches production.

## Option B: the workflow deploys with the CLI

Use this when you would rather Vercel never see the repository.

1. Create an empty project in Vercel, framework **Other**, no build command.
2. From **Project Settings, General**, copy **Project ID** and **Team ID**.
3. Create a token in **Account Settings, Tokens**.
4. Add these values under **Settings, Secrets and variables, Actions**:

   | Kind | Name | Value |
   | --- | --- | --- |
   | Secret | `VERCEL_TOKEN` | The token from step 3 |
   | Variable | `VERCEL_ORG_ID` | The team ID |
   | Variable | `VERCEL_PROJECT_ID` | The project ID |
   | Variable | `STATIC_BASE_URL` | The production origin, no trailing slash |
   | Variable | `VERCEL_ENABLED` | `true` |

5. Run **Actions, Deploy static site to Vercel, Run workflow**.

The job writes `.vercel/project.json` from the two IDs, so a non-interactive run
cannot create a second project or prompt for a scope. It then deploys and
smoke-tests the live origin.

## What both workflows check before publishing

The render is the risky step: an export pointed at the wrong base URL produces
pages that look correct and link nowhere. So the job tests content first:

- every required file exists and is non-empty: `index.html`, `404.html`,
  `robots.txt`, `sitemap.xml`, `vercel.json`, a rendered page and one download;
- no `127.0.0.1` or `localhost` string survived anywhere in the build;
- the Free HR Audit form did not come out inert (`data-audit-nokey` absent from
  the HTML).

After a CLI deploy the same job also requests the live origin and fails on:

- `/`, `/why-emplowise/`, `/free-hr-audit/` and a DOCX answering anything but
  200;
- `/pricing/` not answering 301 or 308, or `/pricing` not landing on 200;
- an unknown path not answering 404;
- a missing `frame-ancestors 'none'`, `nosniff` or `DENY` header;
- `/css/site.css` without `max-age=86400`, which would mean the asset rule never
  reached the CDN.

## What `vercel.json` does, and why

| Rule | Reason |
| --- | --- |
| `trailingSlash: true` | The exporter writes `/page/index.html`. Without this, `/page` is a 404 and `/page/` is the only working form |
| Five redirects, `permanent: true` | `/pricing`, `/career-hub`, `/student-hub`, `/ai-automation` and `/team`, matching `routes/web.php` and the exporter's redirect pages |
| `Content-Security-Policy: frame-ancestors 'none'` | The one CSP directive a `<meta>` tag cannot express |
| `nosniff`, `DENY`, referrer policy, permissions policy, HSTS | The same values `SecurityHeaders` sends from Laravel |
| `max-age=86400` with `stale-while-revalidate` on assets | A day, not a year: `site.css` and `chat.js` keep their names across releases |

Three choices are deliberate and worth keeping:

**The header CSP carries only `frame-ancestors`.** The full policy travels in
the exported `<meta>` tag, which already pins the theme bootstrap by SHA-256.
A second full policy in a header would duplicate that hash and drift from
`ThemeBootstrap::hash()` the first time either changes. Both policies apply when
both are present, and a header cannot relax a meta policy.

**Redirects use `permanent: true`, not `statusCode: 301`.** `permanent` is the
documented switch and answers 308, which preserves the request method and is
treated as permanent by search engines. `statusCode` is marked `private` in
Vercel's published schema, so it is not relied on.

**The catch-all header rule sets no `Cache-Control`.** An earlier version did,
and then two rules matched every asset, leaving the merge order to decide
whether the asset cache applied at all. Vercel already answers static HTML with
`public, max-age=0, must-revalidate`, so the catch-all stays out of it and only
the asset rules opt in.

The `/pricing` path takes two hops: with `trailingSlash: true`, Vercel first
normalises `/pricing` to `/pricing/` with a 308, then the project rule sends it
to `/why-emplowise/`. Two requests, one visible URL change.

## Files that make this work

| Path | Role |
| --- | --- |
| `vercel.json` | Redirects, headers, trailing slashes for the exported folder |
| `.github/workflows/publish-vercel-branch.yml` | Option A: renders and force-pushes `vercel-static` |
| `.github/workflows/deploy-vercel.yml` | Option B: renders, deploys with the CLI, smoke-tests |
| `tests/Feature/VercelConfigTest.php` | Fails when the config drifts from the exporter or `config/security.php` |
| `app/Console/Commands/ExportStatic.php` | Produces the folder and owns the redirect map |
| `config/security.php` | Owns the header values a header rule mirrors |

`vercel.json` at the repository root is the source of truth. Both workflows copy
it into the build root, because the deploy treats that folder as the project
root.

## Checking it without deploying

```bash
php artisan test --filter=VercelConfigTest
php artisan emplowise:export-static /tmp/vercel-check \
  --base=https://emplowise.example --audit-key=test-key-1234
cp vercel.json /tmp/vercel-check/vercel.json
php -S 127.0.0.1:8199 -t /tmp/vercel-check
```

The PHP server normalises `/page` to `/page/` the same way Vercel does, so the
folder layout can be walked before a deploy. It does not apply `vercel.json`, so
redirects and headers need a real host.

The schema is public, so the file can also be validated directly:

```bash
curl -sS https://openapi.vercel.sh/vercel.json -o /tmp/vercel-schema.json
python3 -c "import json,jsonschema; s=json.load(open('/tmp/vercel-schema.json')); \
d=json.load(open('vercel.json')); \
print(len(list(jsonschema.validators.validator_for(s)(s).iter_errors(d))), 'errors')"
```

## Troubleshooting

| Symptom | Likely cause |
| --- | --- |
| Vercel shows a failed deployment for every push to `main` | The ignored build step is not set, so Vercel tried to build PHP. Add the command from Option A step 3 |
| Every page returns 404 | The deployment is serving the repository root instead of the branch root, or `trailingSlash` was overridden in the dashboard |
| The branch never appears | `VERCEL_BRANCH_ENABLED` is unset, or the job failed its pre-flight check |
| Deploy creates a second project | Option B only: `VERCEL_PROJECT_ID` or `VERCEL_ORG_ID` is wrong |
| Pages load but every link 404s | `STATIC_BASE_URL` was wrong at render time. The gate catches `localhost`, not a wrong public host |
| The audit form posts nowhere | `WEB3FORMS_ACCESS_KEY` is missing. The gate stops on an inert form before publishing |
| An old stylesheet persists | Expected within the one-day TTL. HTML is always revalidated, so a hard reload settles it |
| A redirect lands on a 404 | The destination has no trailing slash. Every destination ends with one, except the fragment form |

## Limits

- **Previews are not page-accurate.** The exporter bakes one absolute base URL
  into assets and canonical links, so a preview deployment inherits the
  production origin. Previews are for build sanity, not link testing.
- **Branch deployments carry a bot commit.** `vercel-static` is a build
  artefact, not a place to edit. The next render replaces it.
- **No server-side form handling.** Submissions go to Web3Forms, as on Pages.
- **One canonical host.** Pick Vercel or Pages.
- **The `wip` mirror is untouched.** No workflow writes to it.

## Rolling back

Option A: open **Deployments** in Vercel and promote the previous deployment.
The branch keeps the old commit until the next successful render, and a failed
run leaves it untouched.

Option B: set `VERCEL_ENABLED` to `false`, then promote a previous deployment.

Moving the domain back to GitHub Pages needs no repository change: the Pages
workflow is independent and keeps publishing.
