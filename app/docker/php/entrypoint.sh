#!/bin/bash
# Wrapper around the stock WordPress entrypoint.
#
# TEMPORARY DIAGNOSTIC: Railway crash-loops with
#   AH00534: apache2: Configuration error: More than one MPM loaded.
# while the image we build has only mpm_prefork enabled (verified on both
# linux/amd64 and linux/arm64, and asserted at build time in the Dockerfile).
# Something in the running container therefore differs from the image, so print
# what is actually on disk before Apache parses it. Remove this file once the
# cause is identified.
echo "[leaderauto] MPM modules present at run time:"
ls -l /etc/apache2/mods-enabled/mpm_*.load 2>&1 || echo "[leaderauto]   (none)"
echo "[leaderauto] all enabled modules: $(ls /etc/apache2/mods-enabled/ 2>/dev/null | tr '\n' ' ')"

exec docker-entrypoint.sh "$@"
