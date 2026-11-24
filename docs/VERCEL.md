# Vercel deployment

Vercel hosts the same static export that GitHub Pages serves. Laravel is not
deployed there: Vercel has no PHP runtime, the site needs none at request time,
and running the framework in a serverless function would add cold starts and a
maintenance dependency for no gain.

The export is published as a folder of files. That route also restores two
things GitHub Pages cannot give: real HTTP response headers, and server-side
redirects that answer once instead of shipping a meta refresh.

## How it fits together

```
push to main
  -> deploy-vercel.yml (GitHub Actions)
       installs PHP 8.3, renders build/static with --base=$STATIC_BASE_URL
       checks the render, copies vercel.json in
       vercel deploy --prod  (uploads an already built folder)
  -> Vercel serves build/static with vercel.json applied
```

Nothing is compiled on Vercel's side. The project must therefore stay on the
**Other** framework preset with an empty build command. A build command would
try to run PHP that the build image does not have.

## Files that make this work

| Path | Role |
| --- | --- |
| `vercel.json` | Redirects, headers, trailing slashes for the exported folder |
| `.github/workflows/deploy-vercel.yml` | Renders, checks, deploys, then smoke-tests |
| `tests/Feature/VercelConfigTest.php` | Fails when the config drifts from the exporter or `config/security.php` |
| `app/Console/Commands/ExportStatic.php` | Produces the folder and owns the redirect map |
| `config/security.php` | Owns the header values a header rule mirrors |

`vercel.json` at the repository root is the source of truth. The workflow copies
it into the build root before uploading, because the deploy treats that folder
as the project root.

## One-time setup

1. **Create the project.** In Vercel, add a new project without connecting Git,
   or connect it and cancel the first build. Set the framework preset to
   **Other**, clear the build command, install command and output directory, and
   leave Root Directory at the repository root.
2. **Copy two identifiers.** In **Project Settings, General**, take **Project
   ID** and **Team ID**.
3. **Create a token.** In **Account Settings, Tokens**, create one scoped to the
   team that owns the project.
4. **Add the GitHub values.** In the repository, open **Settings, Secrets and
   variables, Actions**:

   | Kind | Name | Value |
   | --- | --- | --- |
   | Secret | `VERCEL_TOKEN` | The token from step 3 |
   | Variable | `VERCEL_ORG_ID` | The team ID |
   | Variable | `VERCEL_PROJECT_ID` | The project ID |
   | Variable | `STATIC_BASE_URL` | The production origin, for example `https://emplowise.com`, no trailing slash |
   | Variable | `VERCEL_ENABLED` | The literal string `true` |

   `WEB3FORMS_ACCESS_KEY` is already a secret if the Pages workflow runs.

5. **Decide the canonical host.** Either point the domain at Vercel and stop the
   Pages deploy, or keep Pages canonical and treat Vercel as a second target.
   Running both on different hostnames without a canonical tag splits search
   signals.
6. **Run the workflow.** Actions, Deploy static site to Vercel, Run workflow.

Until `VERCEL_ENABLED` is `true` the job is skipped, so merging the workflow
cannot publish anything.

## What the workflow checks

The render step is the risky one: an export pointed at the wrong base URL
produces pages that look correct and link nowhere. So the job tests content
before it uploads:

- every required file exists and is non-empty: `index.html`, `404.html`,
  `robots.txt`, `sitemap.xml`, `vercel.json`, a rendered page, and one
  download;
- no `127.0.0.1` or `localhost` string survived anywhere in the build;
- the Free HR Audit form did not come out inert (`data-audit-nokey` absent from
  the HTML).

After the deploy it requests the live origin and fails on the wrong answer:

- `/`, `/why-emplowise/`, `/free-hr-audit/` and a DOCX must answer 200;
- `/pricing/` must answer 301 or 308, and following `/pricing` must land on 200;
- an unknown path must answer 404 with the custom page;
- the response must carry `frame-ancestors 'none'`, `nosniff` and `DENY`;
- `/css/site.css` must carry `max-age=86400`, proving the asset rule reached the
  CDN and not just the repository.

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

Also note the two-step hop for `/pricing`: with `trailingSlash: true`, Vercel
first normalises `/pricing` to `/pricing/` with a 308, then the project rule
sends it to `/why-emplowise/`. Two requests, one visible URL change.

## Checking it without deploying

```bash
php artisan test --filter=VercelConfigTest      # config against the exporter and security config
php artisan emplowise:export-static /tmp/vercel-check \
  --base=https://emplowise.example --audit-key=test-key-1234
cp vercel.json /tmp/vercel-check/vercel.json
php -S 127.0.0.1:8199 -t /tmp/vercel-check
```

The PHP server normalises `/page` to `/page/` the same way Vercel does, so the
folder layout can be walked before a deploy. It does not apply `vercel.json`, so
redirects and headers need the live smoke test.

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
| Every page returns 404 | The upload did not include the rendered folder, or `trailingSlash` was overridden in the dashboard |
| Deploy fails with "no build command" or a Node error | The project has a framework preset or build command set. Clear both |
| Deploy creates a second project | `VERCEL_PROJECT_ID` or `VERCEL_ORG_ID` is wrong. The job writes `.vercel/project.json` from them, so a mismatch links by name instead |
| Pages load but every link 404s | `STATIC_BASE_URL` was set to a path, or the build ran without it. The gate stops on `localhost`, not on a wrong public host, so check the value |
| The audit form posts nowhere | `WEB3FORMS_ACCESS_KEY` is missing. The gate stops on an inert form before uploading |
| An old stylesheet persists | Expected within the one-day TTL. HTML is always revalidated, so a hard reload fixes it immediately |
| A redirect lands on a 404 | The destination has no trailing slash. Every destination in `vercel.json` ends with one, except the fragment form |

## Limits

- **Previews are not page-accurate.** The exporter bakes one absolute base URL
  into assets and canonical links, so a preview deployment inherits the
  production origin. Previews are for build sanity, not link testing. Only the
  production deploy is smoke-tested.
- **No server-side form handling.** Submissions go to Web3Forms, as on Pages.
- **One canonical host.** Pick Vercel or Pages. A duplicate loses the benefit of
  both.
- **The `wip` mirror is untouched.** This workflow only reads the repository.

## Rolling back

Set `VERCEL_ENABLED` to `false`, or delete the workflow, then point the domain
back at GitHub Pages. The last Vercel deployment keeps answering until the
domain moves, so there is no outage window. Nothing in the repository needs to
change: the Pages workflow is independent and keeps publishing.
