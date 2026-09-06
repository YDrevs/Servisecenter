# LeaderAuto — architecture

This is `ARCH.md` made concrete for this repository. `ARCH.md` stays as the original
(generic, hedged) proposal; **this file records the decisions and what was actually built.**

## Decision: keep WordPress, manage it as code

WordPress stays. It is not going headless. The site is a bespoke **theme** plus a small
**core plugin**, developed against a local Docker copy of the old site and deployed as a
Docker image.

- **Presentation** — theme `leaderauto` (`app/wp-content/themes/leaderauto`). Plain PHP
  templates, no page builder. Assets built by Vite.
- **Business logic** — plugin `leaderauto-core` (`app/wp-content/plugins/leaderauto-core`).
  Currently: the contact-form REST endpoint and its delivery (e-mail + Telegram). The
  design can be rewritten without touching this.
- **Content** — in the database, edited in wp-admin. Structure and copy for the four
  known pages are baked into templates for now (single locale, `uk`); see "Content".

## Repository layout

```
app/
  wp-content/themes/leaderauto/      the theme
  wp-content/plugins/leaderauto-core/ the plugin
  build/                             Vite config + package.json  (npm root)
  docker/php/                        production Dockerfile + postdeploy.sh
  reference/                         local-env helpers, captured content, dev mu-plugin
  db/                                local-env DB import dir (git-ignored contents)
  .env.example                       env var template for hosted environments
docker-compose.yml  Makefile         local REFERENCE environment (the OLD site)
railway.json  .dockerignore          production build
docs/                                this file, deploy-railway.md, specs/
wp-admin/ wp-includes/ wp-content/    frozen reference archive — never edited
ARCH.md                              original proposal (historical)
```

`app/` is the ARCH.md `app/` layout. `site/` from earlier `CLAUDE.md` drafts is not used.

## Local environments — two, don't confuse them

| | Command | What it runs | Purpose |
|---|---|---|---|
| **New site** | `make prepare` once, then `make site` | `wordpress` image + DB dump, `leaderauto` active, assets built | run/see the rebuild |
| **Old site** | `make up` + `make use-divi` | same containers, Divi active | read the old site while rebuilding |
| flip themes | `make use-leaderauto` / `make use-divi` | | same DB, swap active theme |
| live-edit | `make dev` | Vite on `:5173` (Node in a container), live-reload | theme CSS/JS |

Only Docker is required — Node, PHP and MySQL run in containers. `README.md` is the
step-by-step for a fresh Mac.

`docker-compose.yml` = reference environment only. It uses the image's WordPress core
(the repo's `wp-admin/`/`wp-includes/` are untouched), imports
`fm_backup/*-db.sql.gz`, unpacks `fm_backup/*-plugins.zip` (plugins were never in the
repo), routes mail to **Mailpit** (`http://localhost:8025`) so the form is testable.
WP version note: dump is 7.0.2, image is newer; `db_version` matches (61833), no upgrade
friction observed.

## Rendering & routing

- `front-page.php` → home (`template-parts/home/*`).
- `templates/page-{about,dealer,contacts}.php` — `Template Name` templates, assigned to
  pages on theme activation by `inc/activation.php` (idempotent: creates the four pages
  with Latin slugs `/`, `/dealer/`, `/about/`, `/contacts/`, sets the static front page,
  builds the `primary` menu, and — only if unset — switches permalinks to `/%postname%/`).
- `page.php` is the generic fallback; it also pulls `template-parts/page-{slug}.php` if
  one exists.

## Styling

Tokens extracted from the running reference site (`app/reference/NOTES.md`): brand indigo
`#1700be`, hero gradient `#000 → #1700be`, **Kanit** headings / **Open Sans** body, square
buttons. Encoded once in `theme.json` (editor + `--wp--preset--*`) and in
`assets/src/css/base/variables.css` (`--c-*`, `--font-*`). CSS is split
`base/ · components/ · sections/` and bundled via `assets/src/css/main.css`.

## Asset pipeline (Vite)

- **`make build`** — runs Vite in a `node:22` container (its `node_modules` is a named
  volume, isolated from any host copy). Hashed output + `manifest.json` → `assets/dist/`.
- **`make dev`** — Vite dev server on `:5173` with live-reload (polling watcher for the
  macOS bind mount). It drops a `assets/dist/.dev` marker; `inc/enqueue.php` sees it and
  loads from the dev server instead of the manifest, and `make dev` clears the marker on exit.
- Host `npm --prefix app/build …` still works if you have Node locally; not required.
- `assets/dist/` is git-ignored; the production image expects it to exist at build time
  (force-add it or add a Railway build step — see `deploy-railway.md`).

## leaderauto-core

`POST /wp-json/leaderauto/v1/contact` — honeypot + per-IP throttle (120s) + field
validation (`name`, `phone` required; `car`, `message` optional). On success: `wp_mail`
to the configured address (falls back to `admin_email`) and Telegram `sendMessage` if a
token + chat id are set; if both channels fail → `502`. Delivery targets come from
**env constants first** (`LEADERAUTO_CONTACT_EMAIL`, `LEADERAUTO_TELEGRAM_TOKEN`,
`LEADERAUTO_TELEGRAM_CHAT_ID`), then the **Settings → LeaderAuto** screen.

## Content — what's real vs. carried over

| Page | Source | Notes |
|---|---|---|
| Home | reference page 10 — real | 7 sections rebuilt; demo `detailing-*` imagery and source typos dropped |
| About | reference page 17 — real | 5-step process kept; stat numbers/labels are **placeholders** (originals were Divi demo) |
| Dealer | reference page 15 — **~90% Divi demo** | not carried over; minimal real page (BYD sales/test-drive + real media). **Needs real content from the client.** |
| Contacts | reference page 13 — real | form → REST endpoint; demo e-mail `hello@divicardetailing.com` omitted |

Real media copied into `assets/images/` (`logo.png`, `byd.jpg`, `byd-1.jpg`, `icon.jpg`,
`promo.mp4`, `sea-lion-08-debut.mp4`). Stock "Car Detailing" photos are not used.

## Deploy

Railway, Dockerfile build. `develop` → staging, `main` → production. Uploads on a
persistent volume. Full steps + the two content-seeding paths in
[`deploy-railway.md`](deploy-railway.md). Not provisioned yet.

## Open items

- Real Dealer-page content; real About stats.
- Real contact e-mail address (or drop the block).
- Redirects for legacy URLs (`/?page_id=…`, Russian slugs) — no redirect plugin exists.
- Self-host fonts instead of Google Fonts.
- Coding-standards tooling (PHPCS + WordPress ruleset) — `ARCH.md` §17; not added yet.
