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

## Vercel

Vercel can host the same export with real response headers and server-side
redirects, which GitHub Pages cannot provide. Setup, the configuration table,
the pre-flight checks and the troubleshooting steps live in
[`VERCEL.md`](VERCEL.md). Nothing is deployed there until the repository
variable `VERCEL_ENABLED` is set, and Pages remains the canonical host.
