# Production image for LeaderAuto (Railway or any Docker host).
#
# Build context is the repo root; the image builds its own assets, so a plain
#   docker build -f app/docker/php/Dockerfile -t leaderauto .
# is enough — no host-side npm step. That matters because assets/dist/ is
# git-ignored and Railway builds straight from the repo, where it never exists.
#
# The DB, salts and contact-form targets come from environment variables at run
# time (see app/.env.example) — nothing secret is baked in.

# --- Stage 1: build the theme's CSS/JS with Vite ------------------------------
# Paths mirror the repo layout because app/build/vite.config.js resolves the
# theme as ../wp-content/themes/leaderauto, relative to itself.
FROM node:20-alpine AS assets
WORKDIR /repo

COPY app/build/package.json app/build/package-lock.json app/build/
RUN npm --prefix app/build ci

COPY app/build/vite.config.js app/build/
COPY app/wp-content/themes/leaderauto/assets/src app/wp-content/themes/leaderauto/assets/src
RUN npm --prefix app/build run build

# --- Stage 2: the runtime image ----------------------------------------------
FROM wordpress:php8.3-apache

# wp-cli, for the one-time post-deploy activation (see app/docker/php/postdeploy.sh).
COPY --from=wordpress:cli-php8.3 /usr/local/bin/wp /usr/local/bin/wp

# Our code only. Core + wp-config are provided by the base image + env at boot.
COPY app/wp-content/themes/leaderauto   /var/www/html/wp-content/themes/leaderauto
COPY app/wp-content/plugins/leaderauto-core /var/www/html/wp-content/plugins/leaderauto-core
COPY app/docker/php/postdeploy.sh       /usr/local/bin/leaderauto-postdeploy

# Built assets come from stage 1, never from the context — this also overwrites
# a stale dist/ that a local build may have left in the theme directory.
COPY --from=assets /repo/app/wp-content/themes/leaderauto/assets/dist \
                   /var/www/html/wp-content/themes/leaderauto/assets/dist

RUN chmod +x /usr/local/bin/leaderauto-postdeploy \
 && chown -R www-data:www-data /var/www/html/wp-content/themes/leaderauto \
                               /var/www/html/wp-content/plugins/leaderauto-core

# Sanity check that stage 1 actually produced a manifest for inc/enqueue.php to read.
RUN test -f /var/www/html/wp-content/themes/leaderauto/assets/dist/.vite/manifest.json \
 || (echo 'ERROR: asset stage produced no manifest' && exit 1)
