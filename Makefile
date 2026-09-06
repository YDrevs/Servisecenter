# LeaderAuto — run the site locally.
#
# Quick start (needs only Docker Desktop / colima):
#     make prepare      # once — unpack the DB dump + plugins from the backup
#     make site         # build assets, boot WordPress, show the NEW site at :8080
#
# `make help` lists everything.

COMPOSE    := docker compose
WP         := $(COMPOSE) run --rm wpcli wp
NODE       := $(COMPOSE) run --rm node
DEV_MARKER := app/wp-content/themes/leaderauto/assets/dist/.dev

.DEFAULT_GOAL := help
.PHONY: help prepare site up down destroy restart logs open import fixurls \
        wp shell capture build dev use-leaderauto use-divi

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

prepare: ## Once: unpack the DB dump + plugins from fm_backup
	bash ./app/reference/bin/prepare-reference.sh

site: up build use-leaderauto open ## Build + boot + open the NEW LeaderAuto site
	@echo "LeaderAuto is running: http://localhost:8080"

up: ## Start WordPress + DB + Mailpit (boots the OLD Divi site first)
	$(COMPOSE) up -d db wordpress mailpit
	@echo "waiting for WordPress..."
	@until curl -sf -o /dev/null http://localhost:8080/; do sleep 2; done
	@echo "up: http://localhost:8080   mail: http://localhost:8025"

down: ## Stop containers (keeps the database)
	$(COMPOSE) down

destroy: ## Stop containers AND wipe the database volume
	$(COMPOSE) down -v

restart: ## Restart the WordPress container
	$(COMPOSE) restart wordpress

logs: ## Tail WordPress + DB logs
	$(COMPOSE) logs -f wordpress db

open: ## Open the site in the default browser (macOS)
	@command -v open >/dev/null && open http://localhost:8080 || echo "open http://localhost:8080"

import: ## Re-import the reference dump into a running DB (destructive)
	$(COMPOSE) exec -T db sh -c 'exec mariadb -uwordpress -pwordpress matede01_20260503_095713' < app/db/init/01-dump.sql
	$(MAKE) fixurls

fixurls: ## Rewrite the staging host -> localhost in the DB
	$(WP) search-replace 'http://matede01.wp-box.com' 'http://localhost:8080' --all-tables --precise --skip-columns=guid
	$(WP) cache flush

wp: ## Run wp-cli, e.g. make wp CMD="option get blogname"
	$(WP) $(CMD)

shell: ## Shell into the WordPress container
	$(COMPOSE) exec wordpress bash

capture: ## Save the 4 old pages' rendered HTML into app/reference/pages/
	@mkdir -p app/reference/pages
	@for id in 10 13 15 17; do \
		curl -s "http://localhost:8080/?page_id=$$id" > "app/reference/pages/page-$$id.html" && \
		echo "saved app/reference/pages/page-$$id.html"; \
	done

build: ## Build the theme's CSS/JS (runs Node in a container)
	$(NODE) sh -c '[ -x node_modules/.bin/vite ] || npm ci; npm run build'
	@rm -f $(DEV_MARKER)

dev: ## Vite dev server with live-reload (http://localhost:5173); Ctrl-C to stop
	@touch $(DEV_MARKER)
	-$(COMPOSE) run --rm --service-ports node sh -c '[ -x node_modules/.bin/vite ] || npm ci; npm run dev -- --host'
	@rm -f $(DEV_MARKER)

use-leaderauto: ## Activate the NEW theme + core plugin
	$(WP) plugin activate leaderauto-core
	$(WP) theme activate leaderauto
	$(WP) cache flush

use-divi: ## Switch back to the OLD Divi site
	$(WP) theme activate Divi
	$(WP) cache flush
