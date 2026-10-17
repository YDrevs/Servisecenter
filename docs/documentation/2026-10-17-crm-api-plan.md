# API заявок для CRM — план реалізації

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** REST API `GET /leads`, `GET /leads/{id}`, `PATCH /leads/{id}` під ключем `X-Api-Key` для самописної CRM.

**Architecture:** клас `LeaderAuto\Core\Rest_Leads` у `leaderauto-core`, дані — `Leads::to_array()`, ключ — `settings()['crm_api_key']` з env-пріоритетом.

**Tech Stack:** WordPress 7 REST API, PHP 8.1, Docker (`docker compose run --rm -T wpcli wp …`), `curl`.

**Spec:** `docs/documentation/2026-10-17-crm-api-design.md`

## Global Constraints

- Стиль / namespace / текстовий домен — як у `class-rest-contact.php`; повідомлення українською.
- Namespace маршрутів `leaderauto/v1`; заголовок `X-Api-Key`; env `LEADERAUTO_CRM_API_KEY`; ключ ≥ 32 символів.
- Статуси: `la_new`, `la_progress`, `la_done`, `la_cancel` (+ `trash` лише для читання).
- `per_page` 1…100 (типово 50); нотатка ≤ 2000 символів.
- Стейджити явні шляхи; видаляти лише тестові заявки, створені в цій перевірці (за ID).

### Task 1: `updated_at` і ключ у налаштуваннях

**Files:** `includes/class-leads.php`, `includes/helpers.php`, `includes/class-settings.php`

- [ ] `to_array()`: `'updated_at' => get_post_datetime( $post, 'modified' )->format( DATE_ATOM )`; `status_label` для `trash` = «У кошику».
- [ ] `helpers.php`: `crm_api_key` у defaults і env (`LEADERAUTO_CRM_API_KEY`).
- [ ] `class-settings.php`: поле «CRM API key»; `sanitize`: якщо непорожній і < 32 символів — `add_settings_error` і зберегти попереднє значення; абзац-підказка з командою генерації (`openssl rand -hex 32`).
- [ ] Перевірка: `wp eval 'print_r( LeaderAuto\Core\Leads::to_array( <ID> ) );'` містить `updated_at`.

### Task 2: REST-маршрути

**Files:** create `includes/class-rest-leads.php`; modify `leaderauto-core.php`

- [ ] `permission( WP_REST_Request $r )`: порожній ключ сайту → `WP_Error( 'leaderauto_api_disabled', …, 503 )`; `hash_equals( $key, (string) $r->get_header( 'x_api_key' ) )` інакше `401`.
- [ ] `GET /leads`: валідація `updated_since` (`DateTimeImmutable`, → GMT `Y-m-d H:i:s`, `date_query` по `post_modified_gmt`, `inclusive`), `status` (білий список), `page`, `per_page`; `WP_Query` з `orderby => [ 'modified' => 'ASC', 'ID' => 'ASC' ]`, `no_found_rows => false`; відповідь `items/page/per_page/total/total_pages`.
- [ ] `GET /leads/(?P<id>\d+)`: `to_array()` або `404`.
- [ ] `PATCH /leads/(?P<id>\d+)`: `404` / `409` (trash) / `400` (немає полів) / `422` (статус, довжина нотатки); `update_post_meta( '_la_note' )`, потім `wp_update_post( [ 'ID', 'post_status' ] )`; відповідь — свіжий `to_array()`.
- [ ] Перевірка — пп. 1–6 спеки.

### Task 3: Документація

- [ ] `docs/documentation/crm-api.md` — для розробника CRM: автентифікація, ендпоінти, поля, коди помилок, алгоритм синхронізації, приклади `curl`.
- [ ] `docs/architecture.md` — абзац про API.
