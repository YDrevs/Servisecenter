#!/usr/bin/env bash
# One-time setup for the local reference environment.
#
# The repo is a partial file dump: the database and the plugins were never copied
# into it as usable files. Both live inside the wp-file-manager backup archives.
# This script unpacks them into the (git-ignored) working dirs the compose file mounts.
#
# Safe to re-run; it overwrites the unpacked copies.

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
BACKUP_DIR="$REPO_ROOT/wp-content/uploads/wp-file-manager-pro/fm_backup"
DB_INIT_DIR="$REPO_ROOT/app/db/init"
PLUGINS_DIR="$REPO_ROOT/app/reference/plugins"

db_gz="$(ls -1 "$BACKUP_DIR"/*-db.sql.gz 2>/dev/null | head -n1 || true)"
plugins_zip="$(ls -1 "$BACKUP_DIR"/*-plugins.zip 2>/dev/null | head -n1 || true)"

if [[ -z "$db_gz" ]]; then
  echo "ERROR: no *-db.sql.gz found in $BACKUP_DIR" >&2
  exit 1
fi
if [[ -z "$plugins_zip" ]]; then
  echo "ERROR: no *-plugins.zip found in $BACKUP_DIR" >&2
  exit 1
fi

echo "==> DB dump:     $(basename "$db_gz")"
mkdir -p "$DB_INIT_DIR"
gunzip -c "$db_gz" > "$DB_INIT_DIR/01-dump.sql"
echo "    -> app/db/init/01-dump.sql ($(du -h "$DB_INIT_DIR/01-dump.sql" | cut -f1))"

echo "==> Plugins:     $(basename "$plugins_zip")"
rm -rf "$PLUGINS_DIR"
mkdir -p "$PLUGINS_DIR"
unzip -q "$plugins_zip" -d "$PLUGINS_DIR"
echo "    -> app/reference/plugins/ ($(find "$PLUGINS_DIR" -maxdepth 1 -mindepth 1 -type d | wc -l | tr -d ' ') plugin dirs)"

echo
echo "Done. Next: docker compose up -d   (or: make up)"
echo "The dump imports on the db container's FIRST boot only."
echo "To re-import into an existing volume: make import   (or: make destroy && make up)."
