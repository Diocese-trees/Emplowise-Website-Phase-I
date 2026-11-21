# Hosting Emplowise

The application is a standard Laravel 13 / PHP 8.3 app. It needs one PHP process,
one writable `storage/` directory, and a SQLite file (or MySQL/Postgres if you
prefer). There is no Node or asset build step: the CSS and JavaScript are served
directly from `public/`.

---

## 0. What a host must provide

| Requirement | Value |
| --- | --- |
| PHP | 8.3 or newer |
| Extensions | `pdo_sqlite`, `mbstring`, `openssl`, `json`, `curl`, `fileinfo`, `tokenizer`, `xml` |
| Apache modules | `mod_rewrite` (or Nginx `try_files`) |
| Document root | the `public/` folder, never the project root |
| Writable paths | `storage/`, `bootstrap/cache/`, `database/` |
| Writable files | `database/database.sqlite` |

---

## 1. Docker (fastest route to a public URL)

```bash
cp .env.example .env
php artisan key:generate --show      # copy the value into APP_KEY in .env
docker compose up -d --build
```

Open <http://localhost:8080>. The container rewrites, caches config/routes/views
and runs migrations on every start, so a fresh volume still boots.

The same image runs unchanged on any container host:

| Host | How |
| --- | --- |
| Render | New Web Service → Docker → this repo. Add env vars, set port `80`. |
| Railway | New Project → Deploy from repo, it detects the `Dockerfile`. |
| Fly.io | `fly launch --dockerfile Dockerfile`, then `fly deploy`. |
| DigitalOcean App Platform | Dockerfile component, HTTP port 80. |
| Azure Container Apps / Google Cloud Run | Deploy the image, port 80. |

Set these environment variables on the host:

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://your-domain
LOG_LEVEL=warning
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
SECURITY_HSTS=true
SECURITY_HSTS_MAX_AGE=63072000
SECURITY_HSTS_INCLUDE_SUBDOMAINS=true
SECURITY_HSTS_PRELOAD=true
ALIBABA_CLOUD_API_KEY=
ALIBABA_CLOUD_BASE_URL=https://dashscope-intl.aliyuncs.com/compatible-mode/v1
ALIBABA_CLOUD_PRIMARY_MODEL=qwen-max
ALIBABA_CLOUD_FALLBACK_MODEL=kimi
```

> SQLite lives inside the container. Mount a volume at
> `/var/www/html/database` (compose already does) or switch to managed MySQL by
> changing `DB_CONNECTION` — otherwise submissions disappear on redeploy.

### HTTPS and HSTS preload

The app sends `Strict-Transport-Security: max-age=63072000; includeSubDomains; preload`
only when the request reaches PHP over HTTPS. The header is already correct, but
the browser only trusts it after a real HTTPS response, so the host must do the
rest:

1. Terminate TLS on the host and serve the site over `https://your-domain`.
2. Redirect every `http://` request to `https://` with a 308, at the proxy, not in PHP.
3. Set `APP_URL=https://your-domain` and keep `trustProxies(at: '*')` so generated URLs stay HTTPS. On nginx with PHP-FPM, also pass `fastcgi_param HTTPS $https if_not_empty;`; `deploy/nginx.conf.example` already does, and it hides the app's duplicate frame and sniffing headers so each value is sent once.
4. Confirm `https://your-domain`, `https://www.your-domain`, and every other subdomain answer over a valid certificate. `includeSubDomains` covers them all.
5. Check the response has the full header: `curl -sI https://your-domain | grep -i strict-transport`.
6. Only then submit the domain at <https://hstspreload.org>. Removal takes months, so treat this as a one-way step. Set `SECURITY_HSTS_PRELOAD=false` and redeploy if you need to stop sending the token first.

Because the middleware skips plain HTTP, local `php artisan serve` and preview
tunnels never receive the header and cannot pin a developer's browser.

---

## 2. Shared hosting / cPanel (most common for a Pakistan-based SME site)

1. Upload the project so that the folder containing `artisan` sits **above**
   `public_html`, for example `/home/user/emplowise`.
2. Point the domain's document root at `/home/user/emplowise/public`.
   If the host forces `public_html`, move or symlink `public_html` to that folder.
3. Create the environment file and key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Create the database file and tables:
   ```bash
   touch database/database.sqlite
   php artisan migrate --force
   ```
5. Set permissions: `storage/` and `bootstrap/cache/` need `755` on folders and
   `644` on files, with the web-server user able to write.
6. Cache for production:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
7. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`.

`public/.htaccess` ships with the project, so Apache rewriting works without
extra configuration.

---

## 3. VPS (Ubuntu + Nginx + PHP-FPM)

```bash
sudo apt install nginx php8.3-fpm php8.3-sqlite3 php8.3-mbstring php8.3-curl php8.3-xml unzip
sudo mkdir -p /var/www/emplowise
# copy the project to /var/www/emplowise, then:
cd /var/www/emplowise
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache database

sudo cp deploy/nginx.conf.example /etc/nginx/sites-available/emplowise
sudo ln -s /etc/nginx/sites-available/emplowise /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d your-domain
```

---

## 4. Deploy checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_KEY` set and stored in your secret manager
- [ ] Document root points at `public/`
- [ ] `php artisan migrate --force` has run
- [ ] `storage/` and `bootstrap/cache/` are writable by the web-server user
- [ ] `database/database.sqlite` is writable and on a persistent volume
- [ ] `php artisan config:cache route:cache view:cache` has run (re-run after `.env` changes)
- [ ] HTTPS certificate installed
- [ ] `ALIBABA_CLOUD_API_KEY` added, or leave it empty to run the deterministic local engine
- [ ] `php artisan queue:work` running only if you later move work onto queues

---

## 5. Why the front end can look unstyled, and what was fixed

Laravel's `asset()` builds absolute URLs from the incoming request. Behind a
proxy, tunnel, or port mapping that forwards a loopback `Host`, the HTML came out
as `http://127.0.0.1:8000/css/base.css`. On a phone, `127.0.0.1` is the phone
itself, so every stylesheet and script failed and the page fell back to raw HTML.

Two changes remove that failure mode permanently:

1. `App\Http\Middleware\ForcePublicUrl` honours `X-Forwarded-Host` /
   `X-Forwarded-Proto` and rewrites absolute same-origin URLs to root-relative
   ones, so markup works on whatever host served it.
2. All stylesheets and scripts use `asset_v()`, which emits `/css/base.css?v=<mtime>`
   — root-relative, and cache-busted after every deploy.

Verified by loading the site through a public HTTPS tunnel: every CSS, JS, and
image request returned `200`.

---

## 6. Operations

```bash
php artisan about                 # environment summary
php artisan migrate:status        # database state
tail -f storage/logs/laravel.log  # application log
php artisan optimize:clear        # after editing .env
```

Back up `database/database.sqlite` (and `storage/app` if you add uploads). Audit
submissions and chat turns are stored in `audit_requests` and `chat_messages`;
they contain contact details, so treat the database file as personal data.
