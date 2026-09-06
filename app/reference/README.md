# Local reference environment

Boots the **existing** LeaderAuto site (Divi 5 beta) from the database dump, so we can
read its real content, layout and design tokens while rebuilding it in `../wp-content`.

It is disposable. It uses the official `wordpress` Docker image's core plus this repo's
`wp-content` — it does **not** use the repo's `wp-admin/` or `wp-includes/`.

## Prerequisites

- Docker + Docker Compose v2
- The backup archives present at
  `wp-content/uploads/wp-file-manager-pro/fm_backup/*-db.sql.gz` and `*-plugins.zip`

## Run

```bash
make prepare      # unpack the dump -> app/db/init/01-dump.sql, plugins -> app/reference/plugins/
make up           # http://localhost:8080  (admin: http://localhost:8080/wp-admin)
make fixurls      # rewrite the old staging host -> localhost in the DB (run once, after first up)
```

The four real pages (plain permalinks are on in the dump, so use query IDs):

| Page | URL |
|---|---|
| Головна (home / service) | http://localhost:8080/?page_id=10 |
| Автодилер (BYD dealer)   | http://localhost:8080/?page_id=15 |
| Про нас (about)          | http://localhost:8080/?page_id=17 |
| Контакти (contacts)      | http://localhost:8080/?page_id=13 |

`make capture` saves their rendered HTML into `app/reference/pages/` (git-ignored).

## Admin login

Credentials are whatever the dump carries (single admin, `matedevops@gmail.com`). If you
need in and don't have the password, reset it:

```bash
make wp CMD="user update matedevops@gmail.com --user_pass=dev"
```

## Notes / known quirks

- **WordPress version:** the dump is from WP 7.0.2; the image may be older and show a
  one-time "database update required" prompt. Harmless for a reference env — click it, or
  ignore it and just read the front end.
- **Divi is a public beta, unlicensed here.** Expect admin nag banners. The front end
  renders fine.
- `make import` re-imports the dump into a running DB (destructive). `make destroy`
  removes the DB volume entirely; the next `make up` re-imports from scratch.
- `app/db/init/` and `app/reference/plugins/` are git-ignored — they are unpacked copies
  of archive contents, not source.
- The whole `wp-content/uploads/` tree is bind-mounted, including the ~88 MB
  `wp-file-manager-pro/fm_backup/` archives. That's fine locally; `.gitignore` keeps them
  out of git.
