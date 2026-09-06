# Production image for LeaderAuto (Railway or any Docker host).
#
# Build context is the repo root:
#   npm --prefix app/build ci && npm --prefix app/build run build
#   docker build -f app/docker/php/Dockerfile -t leaderauto .
#
# The DB, salts and contact-form targets come from environment variables at run
# time (see app/.env.example) — nothing secret is baked in.

FROM wordpress:php8.3-apache

# wp-cli, for the one-time post-deploy activation (see app/docker/php/postdeploy.sh).
COPY --from=wordpress:cli-php8.3 /usr/local/bin/wp /usr/local/bin/wp

# Our code only. Core + wp-config are provided by the base image + env at boot.
COPY app/wp-content/themes/leaderauto   /var/www/html/wp-content/themes/leaderauto
COPY app/wp-content/plugins/leaderauto-core /var/www/html/wp-content/plugins/leaderauto-core
COPY app/docker/php/postdeploy.sh       /usr/local/bin/leaderauto-postdeploy

RUN chmod +x /usr/local/bin/leaderauto-postdeploy \
 && chown -R www-data:www-data /var/www/html/wp-content/themes/leaderauto \
                               /var/www/html/wp-content/plugins/leaderauto-core

# assets/dist must exist at build time — fail loudly if the host build was skipped.
RUN test -f /var/www/html/wp-content/themes/leaderauto/assets/dist/.vite/manifest.json \
 || (echo 'ERROR: build assets first: npm --prefix app/build run build' && exit 1)
