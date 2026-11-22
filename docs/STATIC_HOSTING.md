# Static hosting on GitHub Pages

GitHub Pages serves files; it cannot run PHP. The site is therefore published as
rendered HTML from `main`, and every interactive feature either works in the
browser or talks to a form service.

| Piece | How it works on the live site |
| --- | --- |
| Seven public pages | Pre-rendered HTML committed to `gh-pages` |
| Chat assistant | Local 18-intent knowledge base in `public/js/chat.js` |
| Interview, ATS, profile tools | Browser ports in `public/js/toolkit/`, no network calls |
| Resume builder, prompt library | Client-side only, drafts in `localStorage` |
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
`data-audit-nokey="1"`, and the wizard stops with "Booking is not connected in
this preview build" instead of posting to a broken endpoint.

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
  `https://api.web3forms.com` for `connect-src` and `form-action`.
- Replaces the EmploStart endpoint attributes, so the page never fires calls
  to `/api/...` that can only fail.
- Rewrites the booking form to `https://api.web3forms.com/submit`, adds the
  `access_key`, `subject`, `from_name` and honeypot fields, and switches the
  client to `credentials: 'omit'` with no CSRF header.
- Normalises canonicals, `og:url`, sitemap entries and internal links to the
  trailing-slash form GitHub Pages actually serves (`/faq/`).

## Browser-engine parity

The toolkit ports must return exactly what the PHP service returns. The fixtures
are generated from the PHP code with no API key:

```bash
php tools/parity/generate.php
node tools/parity/check-interview.mjs
node tools/parity/check-ats.mjs
node tools/parity/check-profile.mjs
```

Each harness loads the shipped browser file in `node:vm` with a fake `window`,
runs the fixture inputs, deep-compares the JSON, and exits non-zero on the first
difference. Regenerate the fixtures after any change to `StudentToolkit`, and
fix the JavaScript until all three harnesses pass.

The chat widget's intent routing is a port of `EmplowiseAssistant::detectIntent`,
so it is checked the same way — the corpus comes from PHP and the browser is
driven over CDP:

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
