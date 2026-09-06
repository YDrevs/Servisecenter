# Deploying LeaderAuto to Railway

Status: **scaffolding only.** The files below are ready; nothing is provisioned.
You need a Railway account and your own secrets.

The model (from `ARCH.md`): code in Git → Railway builds the Docker image → WordPress
runs against a managed MySQL, with `wp-content/uploads` on a persistent volume. Content
lives in the database, not in Git.

## What's in the repo

| File | Purpose |
|---|---|
| `app/docker/php/Dockerfile` | Production image: `wordpress:php8.3-apache` + the `leaderauto` theme + `leaderauto-core` plugin + built assets. No secrets baked in. |
| `app/docker/php/postdeploy.sh` | One-time idempotent activation (theme, plugin, permalinks). Installed in the image as `leaderauto-postdeploy`. |
| `railway.json` | Tells Railway to build from that Dockerfile; healthcheck `/`. |
| `.dockerignore` | Keeps the build context to `app/` code only. |
| `app/.env.example` | The full list of environment variables to set. |

## One-time setup

> **Assets need no host-side step.** The Dockerfile's first stage runs `npm ci &&
> npm run build` inside the image, so `assets/dist/` stays git-ignored and Railway,
> which builds straight from the repo, produces it during the build. Never commit
> `assets/dist/` or force-add it.

1. **Create the Railway project** and add two services:
   - **MySQL** (Railway's managed plugin).
   - **Web** — deploy from this GitHub repo; Railway reads `railway.json`.

2. **Set environment variables** on the Web service (values from `app/.env.example`):
   - `WORDPRESS_DB_HOST`, `WORDPRESS_DB_NAME`, `WORDPRESS_DB_USER`, `WORDPRESS_DB_PASSWORD`
     — from the MySQL service's connection vars. Keep `WORDPRESS_TABLE_PREFIX=wp_` for a
     fresh install.
   - `WORDPRESS_CONFIG_EXTRA` — set `WP_HOME` and `WP_SITEURL` to the public URL, plus the
     eight auth salts (generate at <https://api.wordpress.org/secret-key/1.1/salt/>), plus the
     proxy line below. Railway terminates TLS and forwards plain HTTP, so without it WordPress
     thinks the request is insecure, emits `http://` URLs and can redirect-loop on login:
     ```php
     if ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
         $_SERVER['HTTPS'] = 'on';
     }
     ```
   - `LEADERAUTO_CONTACT_EMAIL`, and optionally `LEADERAUTO_TELEGRAM_TOKEN` /
     `LEADERAUTO_TELEGRAM_CHAT_ID` for the contact form.
   - SMTP vars for outbound mail (Railway has no local MTA — configure `easy-wp-smtp` or a
     transactional provider, or rely on Telegram only).

3. **Add a volume** on the Web service mounted at `/var/www/html/wp-content/uploads`
   (`ARCH.md` §15) — otherwise uploaded media is lost on every redeploy.

4. **First deploy**, then choose a content path:
   > `WP_HOME`/`WP_SITEURL` are PHP constants defined inside `WORDPRESS_CONFIG_EXTRA`, **not**
   > shell variables — `"$WP_HOME"` expands to nothing in these commands. Pass the URL
   > literally (or add `WP_HOME` as a plain Railway variable as well).

   - **Fresh:** `railway run --service <web> wp --allow-root core install --url='https://<your-domain>' --title="LeaderAuto" --admin_user=admin --admin_password='…' --admin_email='you@example.com'`
   - **From the reference dump:** `gunzip -c wp-content/uploads/wp-file-manager-pro/fm_backup/*-db.sql.gz | railway run --service <web> wp --allow-root db import -`
     then `wp --allow-root search-replace 'http://matede01.wp-box.com' 'https://<your-domain>' --all-tables --skip-columns=guid`.
     Note the dump's prefix is `wpQsD69K_`, so set `WORDPRESS_TABLE_PREFIX=wpQsD69K_` to use it.

5. **Activate:** `railway run --service <web> bash /usr/local/bin/leaderauto-postdeploy`

## Environments

Per `ARCH.md` §9–10: branch `develop` → a **staging** Railway environment, branch `main`
→ **production**. Each gets its own MySQL + volume + vars. Promote by merging `develop → main`.

## Not handled here

- **Redirects** for old inbound links. The legacy site used plain permalinks
  (`/?page_id=10`) and Russian slugs (`главная`, `контакты`, `о-нас`); the new site uses
  `/`, `/dealer/`, `/about/`, `/contacts/`. There is no redirect plugin — add one (or
  server rules) if the old URLs are live anywhere.
- CDN, caching, backups, monitoring — out of scope for this pass.
