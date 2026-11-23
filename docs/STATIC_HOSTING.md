# Static hosting on GitHub Pages

GitHub Pages serves files; it cannot run PHP. The site is therefore published as
rendered HTML from `main`, and every interactive feature either works in the
browser or talks to a form service.

| Piece | How it works on the live site |
| --- | --- |
| Fifteen public pages | Pre-rendered HTML committed to `gh-pages`; `/pitch-deck` ships `noindex` and stays out of the sitemap |
| Chat assistant | Local 18-intent knowledge base in `public/js/chat.js` |
| EmploStart | Linked from `/emplostart`; the app is a separate static repo |
| Free HR Audit form | Posts to Web3Forms (free tier: 250 submissions/month) |
| Security policy | CSP `<meta>` injected into every page by the exporter |

## How publishing works

`.github/workflows/publish-static.yml` runs on every push to `main`:

1. Installs PHP 8.3 and Composer dependencies.
2. Runs `php artisan emplowise:export-static build/static --base=... --audit-key=...`.
3. Force-pushes `build/static` to the `gh-pages` branch.

GitHub Pages is configured to serve the `gh-pages` branch, so nothing else is
needed. If the `WEB3FORMS_ACCESS_KEY` secret is missing, the workflow fails on
purpose instead of publishing a booking form that cannot send.

Free for a public repository: Actions minutes, Pages hosting, and the export
itself. GitHub's documented soft limits are 1 GB per published site and 100 GB
of bandwidth per month, which is far above this site's size.

## One-time setup

1. Get a free access key at <https://web3forms.com>. It is tied to the inbox
   that should receive audit requests. Confirm the key from the email Web3Forms
   sends.
2. In the repository: **Settings → Secrets and variables → Actions → New
   repository secret**. Name it `WEB3FORMS_ACCESS_KEY` and paste the key.
3. In **Settings → Pages**, set **Source** to *Deploy from a branch* and pick
   `gh-pages` / `/ (root)`.
4. Push to `main` (or run the workflow manually from the Actions tab).

Until step 1 is done, local exports still work: the form is marked
`data-audit-nokey="1"`, and the wizard stops before submitting and shows an
email link (`data-fallback-email`, taken from `config/emplowise.php`) instead of
posting to a broken endpoint.

### Changing where submissions go

The access key decides the destination inbox, so there is no address to edit in
the code. To change recipients, create a new key for the new inbox and update the
secret.

## Building locally

```bash
php artisan emplowise:export-static build/static \
  --base=https://diocese-trees.github.io/Emplowise-Website-Phase-I \
  --audit-key=YOUR_KEY
```

Then serve the folder with any static file server and open it, for example:

```bash
php -S 127.0.0.1:8081 -t build/static
```

Defaults live in `config/static.php` (`STATIC_BASE_URL`,
`WEB3FORMS_ACCESS_KEY`), so the flags can be dropped once `.env` is set.

The command writes `index.html` plus `/<page>/index.html` for each route,
copies `public/css`, `public/js`, `public/img`, and `public/fonts`, rewrites
root-relative asset URLs and CSS `url(/...)` references to the base path, adds
`.nojekyll`, `robots.txt`, `sitemap.xml`, and `404.html`.

## What the exporter changes in the HTML

- Removes the CSRF meta tag and injects a CSP meta policy that allows
  `https://api.web3forms.com` for `connect-src` and `form-action`, and
  whitelists the pre-paint theme bootstrap by SHA-256 hash
  (`script-src 'self' 'sha256-…'`) instead of `'unsafe-inline'`.
- Leaves the `/emplostart` launcher's external app link untouched, so the page
  never fires calls to `/api/...` that can only fail.
- Rewrites the booking form to `https://api.web3forms.com/submit`, adds the
  `access_key`, `subject`, `from_name` and honeypot fields, and switches the
  client to `credentials: 'omit'` with no CSRF header.
- Normalises canonicals, `og:url`, sitemap entries and internal links to the
  trailing-slash form GitHub Pages actually serves (`/faq/`).
- Writes `404.html` at the build root with the same brand chrome and
  base-prefixed links to Home, the Free HR Audit and Contact, so GitHub Pages
  serves a real page for unknown paths instead of its default error.

## API boundary on a static host

`routes/web.php` keeps three `POST /api/*` routes for the Laravel deployment.
None of them may appear in a static build, so each client script has a fallback
that runs before any request is attempted:

| Route | Static behaviour |
| --- | --- |
| `POST /api/chat` | `data-endpoint` is removed; `chat.js` answers from its bundled knowledge base |
| `POST /api/audit` | Form is rewritten to Web3Forms; without a key it stops and shows the email fallback |
| `POST /api/csp-report` | The exported CSP has no `report-uri`, so nothing is sent |

Gating lives in the clients rather than one global flag: `chat.js` checks the
endpoint attribute and `audit.js` checks `data-audit-mode="web3forms"`. That
keeps each page independent. `ExportStaticTest` enforces the result by scanning
every exported `.html` file for `/api/chat`, `/api/audit`, and
`/api/csp-report`, so a new endpoint cannot leak into the static site silently.

Verified with headless Chromium against a local export: loading
`/free-hr-audit/`, sending a chat message, and opening `/emplostart/` produced
zero `fetch()` calls, zero `/api/` requests, and zero console errors.

## Chat routing parity

The chat widget's intent routing is a port of `EmplowiseAssistant::detectIntent`,
so it is checked against a served export with a corpus generated from PHP and
a browser driven over CDP:

```bash
php tools/parity/generate-chat.php
php artisan emplowise:export-static /tmp/chat-parity --base=http://127.0.0.1:8116
php -S 0.0.0.0:8116 -t /tmp/chat-parity &
chromium --headless=new --remote-debugging-port=9316 --no-sandbox --user-data-dir=/tmp/chat-parity-profile &
node tools/parity/check-chat-routing.mjs
```

The canned replies live in `fallbackReply()` on the PHP side and in the `KB`
object in `public/js/chat.js`. `SiteAndApiTest` asserts every intent is
byte-identical on both sides, so a copy edit has to land in both places.

## Differences from the Laravel deployment

- Audit requests are emailed, not written to `audit_requests`; the reference is
  generated in the browser as `EMP-YYMMDD-XXXX`.
- Chat replies always come from the local knowledge base; the model path and
  transcript logging need the Laravel app.
- Responses carry no custom headers (HSTS, `X-Frame-Options`); GitHub Pages
  controls those. The CSP travels as a `<meta>` tag instead of a header.
- Server behaviours (throttling, 419 CSRF expiry) do not exist; the client
  handles their absence as "use the local engine".

## Vercel (a second static target)

Vercel has no PHP runtime of its own, so Laravel is not deployed there. The
same export that feeds GitHub Pages is published to Vercel as a static folder,
which restores the two things Pages cannot give: real HTTP response headers and
server-side 301s. Nothing is built on Vercel's side.

### One-time setup

1. Create an empty project in the Vercel dashboard, framework preset **Other**,
   with no build command and no output directory. The CLI uploads a folder that
   is already built, so any build step would be wrong.
2. In Vercel, open **Project Settings, General** and copy the project ID and the
   team ID.
3. In GitHub, open **Settings, Secrets and variables, Actions**:
   - Secret `VERCEL_TOKEN`, from Vercel **Account Settings, Tokens**.
   - Variable `VERCEL_ORG_ID`, the team ID.
   - Variable `VERCEL_PROJECT_ID`, the project ID.
   - Variable `STATIC_BASE_URL`, the production origin, for example
     `https://emplowise.com`. No trailing slash.
   - Variable `VERCEL_ENABLED`, the literal string `true`.
4. Point the domain at Vercel and keep GitHub Pages serving, or turn Pages off,
   once the smoke test passes. Do not run both on the same hostname.

The workflow does nothing until `VERCEL_ENABLED` is `true`, so adding it cannot
start a deploy by accident. Setting the variable is the decision to publish.

### What the workflow does

`.github/workflows/deploy-vercel.yml` runs on a push to `main` or on demand:

1. Installs PHP 8.3 and Composer dependencies on the runner.
2. Renders `build/static` with `--base="${STATIC_BASE_URL}"` and the Web3Forms
   key, exactly as the Pages workflow does.
3. Copies `vercel.json` into the build root.
4. Fails before publishing if a page, `404.html`, the sitemap, the downloads
   folder or `vercel.json` is missing, if a `localhost` URL survived into the
   build, or if the audit form came out inert.
5. Runs `vercel deploy --prod` from inside `build/static`.
6. Curls the published site and fails if `/`, `/why-emplowise/`,
   `/free-hr-audit/` or a download does not answer 200, if `/pricing/` does not
   answer 301, or if the security headers are absent.

Step 4 is the safety net: an export pointed at the wrong base URL renders
perfect-looking pages with dead links, so the check is a content test rather
than a size test.

### What `vercel.json` adds

| Rule | Why |
| --- | --- |
| `trailingSlash: true` | The exporter writes `/page/index.html`, so `/page` must become `/page/` rather than 404 |
| Five 301s | `/pricing`, `/career-hub`, `/student-hub`, `/ai-automation` and `/team`, matching the Laravel routes and the exporter's redirect pages |
| `Content-Security-Policy: frame-ancestors 'none'` | The one CSP directive a `<meta>` tag cannot express |
| `nosniff`, `DENY`, referrer and permissions policy, HSTS | The same values `SecurityHeaders` sends from Laravel |
| `Cache-Control: max-age=0, must-revalidate` on HTML | Asset names are not content-hashed, so HTML must never be pinned |
| `max-age=86400` with `stale-while-revalidate` on assets | A day, not a year: `site.css` and `chat.js` keep their names across releases, so `immutable` would serve a stale stylesheet to a fresh page |

The header-level CSP deliberately carries only `frame-ancestors`. The full
policy travels in the exported `<meta>` tag, and a second full policy in a
header would have to repeat the theme bootstrap hash and drift from
`config/security.php` the first time either changes. Both policies apply when
both are present, and a meta policy cannot be relaxed by a header.

`VercelConfigTest` asserts that the redirect map matches the exporter's, that
every redirect is a 301, that the header values match, and that no asset rule
uses `immutable`. Editing one copy without the other fails the suite.

### Limits

- **Previews are not page-accurate.** The exporter bakes one absolute base URL
  into every asset and canonical link, so a preview deployment inherits the
  production origin. Use previews for build sanity, not for link testing. Only
  the production deploy is smoke-tested.
- **One canonical host.** Serving the same pages from Pages and Vercel splits
  search signals. Pick one, and add a 301 layer if the other stays reachable.
- **The `wip` mirror is untouched.** This workflow only reads the repository.

### Rolling back

Delete or set `VERCEL_ENABLED` to `false` to stop publishing, then repoint the
domain at GitHub Pages. The Vercel project keeps the last deployment, so the
old origin keeps answering until the domain moves.
