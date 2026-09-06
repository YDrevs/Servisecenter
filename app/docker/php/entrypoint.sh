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

# mod_php needs mpm_prefork, and Apache refuses to start with more than one MPM.
# The Dockerfile already asserts a single MPM in the image, so anything extra
# seen here was added after the build — drop it rather than crash-loop.
for m in mpm_event mpm_worker; do
	if [ -e "/etc/apache2/mods-enabled/$m.load" ]; then
		echo "[leaderauto] unexpected $m enabled at run time — disabling it"
		a2dismod "$m" >/dev/null 2>&1 \
			|| rm -f "/etc/apache2/mods-enabled/$m.load" "/etc/apache2/mods-enabled/$m.conf"
	fi
done
if [ ! -e /etc/apache2/mods-enabled/mpm_prefork.load ]; then
	echo "[leaderauto] mpm_prefork missing — enabling it"
	a2enmod mpm_prefork >/dev/null 2>&1 \
		|| ln -sf ../mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
fi
echo "[leaderauto] MPM after normalisation: $(ls /etc/apache2/mods-enabled/mpm_*.load 2>/dev/null | tr '\n' ' ')"

exec docker-entrypoint.sh "$@"
