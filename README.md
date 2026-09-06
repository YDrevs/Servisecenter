# LeaderAuto

EV service shop & BYD dealer site (Chernivtsi region). Being rebuilt as code-managed
WordPress — a bespoke theme + a small plugin. Architecture: [`docs/architecture.md`](docs/architecture.md).

---

## Run it locally on a Mac

### Prerequisites

- **Docker Desktop** (or colima / OrbStack) — running. That's the only requirement;
  Node, PHP and MySQL all run in containers.
- `make` (ships with the Xcode command-line tools: `xcode-select --install`).

### Quick start

```bash
make prepare      # once: unpacks the DB dump + plugins from the backup archive
make site         # builds assets, boots WordPress, opens the NEW site
```

Then:

| URL | What |
|---|---|
| <http://localhost:8080> | the site |
| <http://localhost:8080/wp-admin> | WordPress admin |
| <http://localhost:8025> | Mailpit — every e-mail the site sends lands here |

Admin login is whatever the imported database has (`matedevops@gmail.com`). To set a
known password:

```bash
make wp CMD="user update matedevops@gmail.com --user_pass=dev"
```

### Two sites in one environment

The same containers can serve either the **new build** or the **old Divi site** (handy
for comparing while rebuilding):

```bash
make use-leaderauto   # the new theme  (this is what `make site` leaves active)
make use-divi         # the original Divi site, from the DB dump
```

### Live-editing the theme

```bash
make dev              # Vite dev server on :5173 with live-reload; Ctrl-C to stop
```

While it runs, CSS/JS changes in `app/wp-content/themes/leaderauto/assets/src/` refresh
the browser automatically. When you stop it, run `make build` once to regenerate the
static assets.

### Everyday commands

```bash
make help            # list all targets
make down            # stop (keeps the database)
make destroy         # stop and wipe the database (next `make up` re-imports the dump)
make logs            # tail WordPress + DB logs
make wp CMD="..."    # run wp-cli, e.g. make wp CMD="plugin list"
make shell           # bash inside the WordPress container
```

### Troubleshooting

- **`make prepare` fails** — the backup archives must exist at
  `wp-content/uploads/wp-file-manager-pro/fm_backup/*-db.sql.gz` and `*-plugins.zip`.
- **Port already in use** — something else is on `8080` / `8025` / `5173`. Stop it, or
  change the left-hand port numbers in `docker-compose.yml`.
- **"database update required" in wp-admin** — the dump is from a slightly newer
  WordPress than the image; click the button, it's harmless for local use.
- **Styles missing** — run `make build`.
- **Start over completely** — `make destroy && make prepare && make site`.

---

## Layout

| Path | |
|---|---|
| `app/wp-content/themes/leaderauto/` | the theme |
| `app/wp-content/plugins/leaderauto-core/` | contact-form endpoint + integrations |
| `app/build/` | Vite config |
| `app/docker/`, `railway.json` | production image / deploy — see [`docs/deploy-railway.md`](docs/deploy-railway.md) |
| `app/reference/` | local-env helpers, captured old-site content & design tokens |
| `docker-compose.yml`, `Makefile` | the local environment |
| `wp-admin/`, `wp-includes/`, `wp-content/`, `ARCH.md` | frozen reference archive — not edited |
