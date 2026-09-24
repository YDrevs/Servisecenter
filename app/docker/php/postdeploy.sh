#!/bin/bash
# One-time (idempotent) post-deploy step for a hosted environment.
#
#   railway run --service <web> bash /usr/local/bin/leaderauto-postdeploy
#
# Assumes WORDPRESS_DB_* / WP_HOME / WP_SITEURL are set and the database is reachable.
set -euo pipefail

cd /var/www/html
WP=(wp --allow-root)

if ! "${WP[@]}" core is-installed 2>/dev/null; then
  echo "WordPress is not installed yet."
  # WP_HOME is a PHP constant from WORDPRESS_CONFIG_EXTRA, not a shell variable — pass the URL literally.
  echo "Run once:  wp --allow-root core install --url='https://your-domain' --title='LeaderAuto' \\"
  echo "             --admin_user=admin --admin_password=<pw> --admin_email=<you@example.com>"
  echo "…or import a prepared dump:  wp --allow-root db import dump.sql"
  exit 1
fi

"${WP[@]}" plugin activate leaderauto-core
"${WP[@]}" theme activate leaderauto
# `theme activate` is a no-op on an already-active theme, so after_switch_theme — and
# with it the scaffold — never fires on redeploys. Call it directly: it is idempotent
# and creates any page/menu item added since (e.g. /parts/).
"${WP[@]}" eval 'leaderauto_scaffold_site();'
"${WP[@]}" rewrite structure '/%postname%/' --hard
"${WP[@]}" rewrite flush --hard
"${WP[@]}" cache flush || true

echo "Post-deploy done."
