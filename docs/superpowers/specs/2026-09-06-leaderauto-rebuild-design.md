# LeaderAuto rebuild — design record

Date: 2026-09-06
Status: implemented (first pass)

## Context

The repo was a partial WordPress file dump (Divi 5 beta site, no `wp-config.php`, no root
PHP, no plugins, DB only inside `fm_backup/*-db.sql.gz`). `ARCH.md` proposed rebuilding it
as code-managed WordPress. This spec adapts that proposal to the repo and records the
first implementation pass.

## Decisions

| Topic | Decision |
|---|---|
| Stack | Keep WordPress. Bespoke theme + small core plugin. Not headless. |
| Build root | `app/` (ARCH.md layout). `wp-admin/`, `wp-includes/`, `wp-content/` stay frozen. |
| Slug | `leaderauto` theme, `leaderauto-core` plugin. |
| Local reference env | Docker Compose: `wordpress` image + DB dump + Divi + Mailpit. Disposable. |
| Asset pipeline | Vite (`app/build`), manifest-driven enqueue, dev-server HMR. |
| Styling | Tokens extracted from the running reference; `theme.json` + CSS custom properties. |
| Content fidelity | Content parity + approximate visual match, clean semantic HTML, `uk` only. Not a pixel Divi clone. |
| New URLs | `/`, `/dealer/`, `/about/`, `/contacts/` (Latin). Pretty permalinks on. |
| Contact form | `POST /wp-json/leaderauto/v1/contact` → validate → e-mail + optional Telegram. |
| Deploy | Railway, Dockerfile build. Config + docs only this pass; not provisioned. |

## Delivered

- `docker-compose.yml`, `Makefile`, `app/reference/` (prepare script, README, NOTES with
  captured content + design tokens, dev mail mu-plugin), `.gitignore`, `.dockerignore`.
- Theme: bootstrap, `setup`/`enqueue`/`template-tags`/`activation` includes, `theme.json`,
  `header`/`footer`/`index`/`page`/`404`, `front-page` + 7 home parts, three
  `Template Name` page templates, CSS (`base`/`components`/`sections`), JS (`main`/`menu`/`form`).
- Plugin: REST contact endpoint (honeypot, throttle, validation, e-mail + Telegram),
  Settings screen, env-var override, `uninstall.php`.
- Railway: `app/docker/php/Dockerfile` + `postdeploy.sh`, `railway.json`, `docs/deploy-railway.md`.
- `docs/architecture.md` (adapted ARCH.md); `CLAUDE.md` updated.

## Verified (local)

- Reference site boots; all 4 legacy pages render under Divi.
- `leaderauto` theme + `leaderauto-core` activate cleanly; scaffold hook creates pages +
  menu + front page + permalinks; no PHP notices in `debug.log`.
- `/`, `/dealer/`, `/about/`, `/contacts/` each render their own template; bogus URL → 404.
- Built CSS/JS load (200); `window.LEADERAUTO` exposes REST URL + nonce.
- Contact endpoint: valid → 200 + mail visible in Mailpit; invalid → 422 with field
  errors; honeypot → 200 no delivery; repeat → 429.
- `docker build -f app/docker/php/Dockerfile .` succeeds.

## Follow-ups

- Real Dealer content and real About stats (client input).
- Real contact e-mail address, or remove the block.
- Legacy-URL redirects.
- Self-hosted fonts; PHPCS + WordPress standard.
- Sub-project B proper (deeper content migration) if more than the 4 pages is wanted.
