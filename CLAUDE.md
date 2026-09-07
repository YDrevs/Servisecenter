# CLAUDE.md — LeaderAuto

**Read this before doing anything.**

1. **The archive is a partial file dump copied off a WordPress server, not a runnable install.** There is no `wp-config.php`, `index.php`, `wp-load.php`, `wp-settings.php` or `wp-login.php` in it, and it had zero PHP files at repo root. On its own it cannot boot. (There is now a local reference environment — `docker-compose.yml` + `make up` — that boots the *old* site from the DB dump using a stock `wordpress` image; the archive's own core is still never used.)
2. **Almost nothing in the archive is the site.** All five bundled themes are byte-for-byte stock, there is no child theme, and there is no custom CSS or PHP in those files. The old design and content live in the **database**.
3. **The archive is source material. The rebuild lives in `app/`.** See [What we're doing](#what-were-doing) and `docs/architecture.md`.

---

## What we're doing

Rebuilding LeaderAuto as **code-managed WordPress**: a bespoke theme + a small core plugin,
developed against a local Docker copy of the old site, deployed as a Docker image. This is
the `ARCH.md` direction, now decided and underway. Full detail in `docs/architecture.md`.

| Zone | Rule |
|---|---|
| `wp-admin/`, `wp-includes/`, `wp-content/`, `ARCH.md` | Frozen reference archive / historical proposal. Read to learn what the site was. Never edit, never port wholesale. |
| `app/` | The new build. Theme `app/wp-content/themes/leaderauto`, plugin `app/wp-content/plugins/leaderauto-core`, Vite in `app/build`, prod Dockerfile in `app/docker`, local-env helpers in `app/reference`. |
| `docker-compose.yml`, `Makefile`, `railway.json`, `docs/` | Build & deploy tooling for the rebuild. |
| `To do now/` | The user's local scratch — screenshots and short notes dropped in for the current task. **Git-ignored on purpose; never commit it.** Read what is in it, but treat it as transient. |
| `Planned work/` | Design source of truth for matching the old site: `leaderauto-design-spec.md` (measured `getComputedStyle()` values from the live reference, captured via the browser) and `leaderauto-design-plan.md` (the worklist derived from it). Read the spec before changing section layout, type scale or colours. Note its counter figures (78/19/213) were read mid-animation — the settled values are 100% / 24/7 / 275+. |

Run it (needs only Docker — Node/PHP/MySQL are containerised). Full guide: `README.md`.

```
make prepare        # once: unpack the DB dump + plugins from fm_backup
make site           # build assets + boot WordPress + open the NEW site at localhost:8080
make dev            # Vite dev server (:5173, live-reload) for the theme
make use-divi       # flip the active theme back to the OLD Divi site (same DB)
make use-leaderauto # flip back to the new build
```

The stack is settled — see [Rebuild conventions](#rebuild-conventions). Don't reopen it without reason.

---

## What this site is

**LeaderAuto** — an **electric-vehicle service shop in Chernivtsi, Ukraine**. Not a general garage; the site's own positioning line is «Ми не "звичайне СТО". Ми працюємо саме з електромобілями».

- **Services:** EV diagnostics, ECU software updates and coding, high-voltage battery service, reduction gear (редуктор), cooling systems, brakes, suspension checks, fluid changes, OEM parts sourcing.
- **Brands, in order of prominence:** **BYD** (lead brand — dedicated banner and launch video), Tesla, Zeekr, Volkswagen ID, Nissan Leaf.
- **Phone used on-site:** +380 (95) 066 29 21
- **Audience:** Ukrainian-speaking, local. Tone is expert-but-plain, not corporate.

---

## Source-of-truth map

Where each thing you need for the rebuild actually lives. The backup path below is `wp-content/uploads/wp-file-manager-pro/fm_backup/`; its five archives are named `backup_2026_08_01_13_45_37-b0e9b118-{db.sql.gz,plugins.zip,themes.zip,uploads.zip,others.zip}`. The dump inside is still of DB `matede01_20260503_095713` (the `2026_08_01` in the filename is the backup date, not a newer database).

**Easier than reading the DB: `make up` and read the rendered site.** The four real pages are
`?page_id=10` (Головна), `15` (Автодилер), `17` (Про нас), `13` (Контакти). Captured content
and design tokens are already written up in `app/reference/NOTES.md`.

| What you need | Where it actually is |
|---|---|
| Page copy, layouts, navigation | `fm_backup/*-db.sql.gz` → `wpQsD69K_posts.post_content`. **Divi 5 block markup** (`<!-- wp:divi/section {…json…} -->`), *not* D4 `[et_pb_*]` shortcodes. Deeply nested, quote-escaped JSON. |
| Logo, colors, fonts, header/footer | same dump → `wpQsD69K_options`, row `et_divi` (~150 keys) |
| Customizer values, menu locations | same dump → `theme_mods_Divi` |
| Theme Builder templates, saved layouts | same dump → `et_theme_builder_*` / `et_pb_layout` post types |
| Real images and video | `wp-content/uploads/2026/05/` |
| Plugin set | `fm_backup/*-plugins.zip` (`wp-content/plugins/` was never copied) |
| Custom CSS or PHP | **Nowhere. None exists.** |

**Consequence: grepping the theme tree for site text or styling will always return nothing.** Absence from these files is not absence — it means it's in the database. Don't conclude a thing doesn't exist because `grep` came up empty.

Verified: `theme_mods_Divi.custom_css_post_id` points at post 48, but that `wp_custom_css` post is empty, and there is no `divi_custom_css` value. There is genuinely zero custom CSS on this site, in files or DB.

---

## Environment and versions

| Thing | Value |
|---|---|
| WordPress core | **7.0.2** (`wp-includes/version.php`, `$wp_db_version = 61833`), requires PHP ≥ 7.4 |
| Site URL | `http://matede01.wp-box.com` — plain HTTP **staging/sandbox host**, not a production domain |
| Active theme | **Divi 5.0.0-public-beta.2.1** — a public beta running as the live theme |
| Other themes (all inactive, all stock) | Extra 4.27.4, twentytwentythree 1.6, twentytwentyfour 1.3, twentytwentyfive 1.3 |
| Locale | `WPLANG = uk` (Ukrainian) |
| DB name | `matede01_20260503_095713` |
| Table prefix | **`wpQsD69K_`** — randomized. Never assume `wp_`. |
| Users | single admin, `matedevops@gmail.com` |
| Repo / checkout | GitHub `YDrevs/Servisecenter`, local path `…/BYD_Cervisecenter/Servisecenter`. Repo and folder names diverge from the site's own branding (`blogname` is still **LeaderAuto**), and the VS Code workspace file is still `LeaderAvto.code-workspace`. Don't read meaning into the "BYD" in the folder name — BYD is the lead brand, not a rename. |

Only **three plugins** exist, per the backup zip: `easy-wp-smtp` 2.15.0 (active), `wp-file-manager` 8.0.4 free version (active), `akismet` 5.7 (installed, inactive). **No caching, SEO, forms, security or backup plugin.** Contact forms are Divi's built-in `et_pb_contact_form`.

`wp-content/` here holds only `themes/`, `uploads/`, `languages/`, and empty `et-cache/`, `temp/`, `upgrade/`, `upgrade-temp-backup/`. No `mu-plugins/`, no `object-cache.php`, no `advanced-cache.php`.

---

## Extraction rules

- **Divi block markup is not portable.** The content is Divi 5 blocks (`wp:divi/*` with JSON attrs), not D4 shortcodes. Reproduce the *content*, not the builder DOM — no wrapper divs, generated ids, or Divi class names in the rebuild.
- **Prefer the rendered site over the block JSON.** `make up`, read the page, translate what it says into `leaderauto` templates. `app/reference/NOTES.md` already has this done for the 4 pages.
- `wp-content/et-cache/` is Divi's generated static CSS. Disposable output, not a styling source (though the reference env's rendered CSS is a fine place to read colour/font values).
- **Copy** real assets into `app/wp-content/themes/leaderauto/assets/` as you need them. Leave the archive untouched. (Done for the 6 real files — see the theme's `assets/images/`.)
- Divi ships two builders side by side — `themes/Divi/includes/builder/` (legacy D4) and `themes/Divi/includes/builder-5/` (the D5 rewrite, which is what this site uses). If you read Divi internals to decode a layout, you want `builder-5/`.

---

## Known debt and traps

- **The site was built from Divi's "Car Detailing" premade layout pack and the demo text was never stripped.** `hello@divicardetailing.com` is **not a real address** — never present it as one.
- **The layout-pack images ARE licensed and stay.** `detailing-02.png` … `detailing-21.jpg` and
  `handyman_04–06.jpg` are paid assets the client bought, committed deliberately in `Images/`
  (`9800c56`). They are **placeholders the client will swap for their own photos over the coming
  days**, not junk to strip. Do not delete them, do not "clean them up", and do not treat their
  presence as a mistake. Only the demo *copy* (addresses, names) is fake.
- **Real client assets are only in `wp-content/uploads/2026/05/`:** `ЛОГО.png` (the actual logo), `BYD.jpg`, `CAR-Lider-Avto-2.mp4`, `Sea-Lion-08-Дебют.mp4`, `1w.jpg`, `icon.jpg` — plus generated size variants. That's the whole real media library. `uploads/2026/06`, `07`, `08` exist but are empty; `uploads/et_temp/` is Divi scratch space.
- **Copy is Ukrainian, but the archive's page slugs are legacy Russian** (`главная`, `контакты`, `о-нас`) and `permalink_structure` is **empty** (plain `?page_id=` URLs). The rebuild uses deliberate Latin slugs — `/`, `/dealer/`, `/about/`, `/contacts/` — and pretty permalinks. There is no redirect plugin, so any old inbound links are unprotected; see `docs/deploy-railway.md`.
- `wp-content/languages/` carries both `uk` (71 files) and `ru_RU` (80 files). The `ru_RU` set is dead weight from the earlier config.
- **The backup directory's `.htaccess` is malformed** — it opens `<FilesMatch "\.(zip|gz)$">` and closes with `</Files>`, which is an Apache fatal-config error. The deny rule is therefore not reliably in force, and ~88 MB of archives — including a DB dump containing the admin's bcrypt password hash — may be publicly fetchable over plain HTTP. **Report this; do not silently patch it.** It's a live server config, not our code.
- Whether a production domain exists behind the staging host is **unknown**. Ask before assuming this is the only environment.

---

## Hard rules

- **Never edit `wp-admin/`, `wp-includes/`, `wp-content/themes/Divi/`, `wp-content/themes/Extra/`,** or the bundled default themes. Core and vendor code. All are currently byte-for-byte stock — keep them that way so diffing against the official release stays a valid integrity check. All new work goes under `app/` (plus the root build/deploy files).
- **Never `git add .` / `git add -A` / `git add -u`.** Stage explicit paths only. There is a `.gitignore` now, but treat it as a backstop, not a licence — see [Git reality](#git-reality).
- **Never commit** `wp-content/uploads/**` (the whole tree, incl. `fm_backup/`), any `*.sql` / `*.sql.gz` / `*.zip`, any `.DS_Store`, anything under `claude/`, `app/db/**`, `app/reference/plugins/**`, `assets/dist/**`, `node_modules/`, or a `wp-config.php` if one ever appears. Most are `.gitignore`d; still check `git status` before every commit.
- **The local reference runtime (`docker-compose.yml`, `make up`) is sanctioned** and boots the *old* site for reference. Don't scaffold a *second* runtime or add a `wp-config.php` to the archive.
- **Don't update, downgrade or switch the Divi theme in the archive.** It's a public beta; layout formats shift between betas and can break pages irreversibly. (`make use-leaderauto` / `make use-divi` only flip the *active theme* in the local DB — that's fine and reversible.)
- **Don't `chmod` or `chown` the archive.** The 777 directories and the mode-700 `claude/` are copy artifacts, not something to fix. Making a *new* script under `app/` executable is fine.

---

## Ignore `claude/`

`claude/` at the repo root is **not part of this project.** It contains `claude/agents/` (seven agent-role markdown files) and `claude/worktrees/agent-a70a59c1f71b860fe/` — a complete, unrelated **"Accurly ERP" NestJS + React monorepo**. That worktree is orphaned: its `.git` file points at `E:/ERP-platform-v2/.git/worktrees/…`, a Windows path that doesn't exist here, so git commands inside it fail.

- **It has its own CLAUDE.md. That file does not apply here.** Don't read it for conventions.
- Don't let its patterns leak into this work, and don't create a `docs/agent-jobs/` folder.
- Never search, refactor or commit inside `claude/`. Exclude it from repo-wide greps (`--exclude-dir=claude`).
- If a task genuinely concerns that ERP project, stop and say the user is in the wrong repo.

---

## Git reality

Remote `https://github.com/YDrevs/Servisecenter.git` (earlier history was on `YevhenDrevs/LeaderAvto.git`), branch **`main`**, exactly **one commit**: `324a6d5 "Create newfile with initial content"`.

**Exactly one file is tracked: `newfile`** (a one-word placeholder), and it is already `git rm`'d in the working tree — `git status` shows `D newfile`. Everything else — `wp-admin/`, `wp-includes/`, all of `wp-content/`, this file, `ARCH.md`, `claude/` — is **untracked**. An earlier state of this repo tracked all of `wp-admin/`; that is no longer true. Nothing of value is committed.

**A root `.gitignore` and `.dockerignore` now exist.** They cover `wp-content/uploads/`, `*.sql*`,
`*.zip`, `.env`, `.DS_Store`, `node_modules/`, `vendor/`, `app/db/`, `app/reference/plugins/`,
`app/reference/pages/`, and the theme's `assets/dist/`.

**The whole frozen archive is now git-ignored too** — `/wp-admin/`, `/wp-includes/`,
`/wp-content/` and `/claude/`, each anchored with a leading slash so the rebuild under
`app/wp-content/` is untouched. That was the user's call: those ~250 MB are stock WordPress
7.0.2 core, stock Divi/Extra/default themes and the `uk`/`ru_RU` language packs, all
recoverable from wordpress.org and ElegantThemes, so they stay on disk as source material
and never reach GitHub. What's left to push is **72 files, ~7.5 MB** — of which 6.3 MB is the
two real `.mp4` files in the theme's `assets/images/`. Still:

- **Never `git add .` / `-A` / `-u`.** Stage explicit paths, and re-read `git status` before every commit.
- The `.gitignore` is a backstop. If anything from the "Never commit" list in [Hard rules](#hard-rules) shows up staged anyway: unstage and stop.
- Nothing is committed yet. The first real commit should stage `app/`, `docker-compose.yml`,
  `Makefile`, `railway.json`, `.gitignore`, `.dockerignore`, `docs/`, and the updated `CLAUDE.md` —
  explicitly, path by path.

---

## Rebuild conventions

Decided and in effect. `docs/architecture.md` is the authoritative version; summary:

| Area | Convention |
|---|---|
| Stack | WordPress, kept. Bespoke theme `leaderauto` + plugin `leaderauto-core`. No page builder, no Divi in the rebuild. |
| Layout | Everything under `app/` (`wp-content/themes/leaderauto`, `wp-content/plugins/leaderauto-core`, `build/`, `docker/`, `reference/`). Root: `docker-compose.yml`, `Makefile`, `railway.json`. |
| Rendering | Classic PHP templates. `front-page.php` + `template-parts/home/*`; `templates/page-*.php` (`Template Name`) assigned on theme activation by `inc/activation.php`. |
| Routing / slugs | `/`, `/dealer/`, `/about/`, `/contacts/`. Pretty permalinks (`/%postname%/`). |
| Styling | Tokens from the reference site in `theme.json` + `assets/src/css/base/variables.css`. CSS split `base/ components/ sections/`, bundled via `main.css`. Brand `#1700be`, Kanit / Open Sans. |
| Assets | Vite in `app/build`. `npm --prefix app/build run build` → `assets/dist/` (git-ignored) + manifest; `inc/enqueue.php` reads it, with a dev-server (`:5173`) fallback. |
| i18n | `uk` only. Text domain `leaderauto` / `leaderauto-core` is wired; copy is inline Ukrainian for now — no second locale planned. |
| Forms | `POST /wp-json/leaderauto/v1/contact` in `leaderauto-core` → validate → `wp_mail` + optional Telegram. Targets via env constants first, then a Settings screen. |
| Media | Real files in `app/wp-content/themes/leaderauto/assets/images/`. The client's licensed layout-pack images live in `Images/` and are in play as placeholders until their own photos land. |
| Hosting | Railway, Dockerfile build (`app/docker/php/Dockerfile`). `develop`→staging, `main`→prod. `uploads/` on a volume. See `docs/deploy-railway.md`. Not provisioned yet. |
| AI context | This file. No separate `AI_CONTEXT.md` (ARCH.md §17 suggested one; folded in here). |

---

## Build, run, verify

- **Run it:** `make prepare` once, then `make site` → `localhost:8080` (new build), mail at
  `localhost:8025`. `make up` alone boots the OLD Divi site; `make use-divi` / `make use-leaderauto`
  flip the active theme against the same DB. `README.md` is the full walkthrough.
- **Build assets:** `make build` (Node runs in a container — no host Node needed). Live-reload: `make dev`.
- **`make wp CMD="…"`** runs wp-cli in the container. `make help` lists targets.
- **No test suite or linter yet.** PHPCS + the WordPress standard is a planned follow-up
  (`docs/architecture.md` → Open items). No CI.
- Vendor `package.json` under `wp-content/themes/Divi/**` is Elegant Themes' tooling — not ours.
- The **archive** still can't run on its own. When you can't verify something, say so plainly;
  don't imply you saw it work.

---

## Working principles

- State assumptions before implementing. If uncertain, ask. If multiple readings exist, present them — don't pick one silently.
- Write the minimum that solves the problem. Nothing speculative: no unrequested features, abstractions, config flags, or error handling for impossible cases.
- **Surgical changes.** Touch only what you must. Don't reformat, don't refactor working code, match existing style. Remove only the imports and variables your own change orphaned.
- If you spot unrelated dead code or debt, **mention it — don't delete it.**
- **Before concluding old content doesn't exist, remember it may be in the database** — `make up` and look. Before claiming the rebuild works, actually run it (`make up`, `curl`, wp-cli) — don't imply you saw something you didn't.
