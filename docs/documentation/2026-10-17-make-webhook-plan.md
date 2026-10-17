# Заявки → Make.com — план реалізації

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** надсилати `lead.created` і `lead.status_changed` у webhook Make.com з повторами при невдачі.

**Architecture:** клас `LeaderAuto\Core\Make_Webhook` у `leaderauto-core`; дані заявки — `Leads::to_array()`; налаштування — у наявному екрані Settings → LeaderAuto з env-пріоритетом.

**Tech Stack:** WordPress 7 / PHP 8.1, WP HTTP API, WP-Cron, Docker (`docker compose run --rm -T wpcli wp …`).

**Spec:** `docs/documentation/2026-10-17-make-webhook-design.md`

## Global Constraints

- Стиль і namespace — як у `class-rest-contact.php`; текстовий домен `leaderauto-core`; копія UI українською.
- Env: `LEADERAUTO_MAKE_WEBHOOK_URL`, `LEADERAUTO_MAKE_API_KEY`; ключі налаштувань `make_webhook_url`, `make_api_key`.
- Заголовок ключа — `x-make-apikey`. Timeout 5 с. Повтори: +60 с, +300 с, +1800 с (усього 4 спроби).
- Meta: `_la_make_status`, `_la_make_at`. Cron-хук: `leaderauto_make_retry`.
- Стейджити явні шляхи; тестів-фреймворку немає — ручна перевірка через приймач-контейнер.

### Task 1: Дані заявки й налаштування

**Files:** `includes/class-leads.php`, `includes/helpers.php`, `includes/class-settings.php`

**Produces:** `Leads::to_array( int $id ): array` (структура `lead` зі спеки, `[]` якщо не `la_lead`); `settings()['make_webhook_url']`, `settings()['make_api_key']`.

- [ ] `to_array()` — поля зі спеки; дати `get_post_datetime()->format( DATE_ATOM )`; `admin_url( 'post.php?post=ID&action=edit' )`.
- [ ] `helpers.php` — два ключі в defaults та env-резолві.
- [ ] `class-settings.php` — два поля (URL `type="url"`), `sanitize`: URL через `esc_url_raw` і лише `https://`; env-мапінг імені константи для `make_webhook_url` → `LEADERAUTO_MAKE_WEBHOOK_URL`.
- [ ] Перевірка: `wp eval 'print_r( LeaderAuto\Core\Leads::to_array( <ID> ) );'`; поля на екрані налаштувань зберігаються.

### Task 2: Відправка, повтори, події

**Files:** create `includes/class-make-webhook.php`; modify `leaderauto-core.php`, `class-rest-contact.php`, `class-leads.php` (блок «Доставка»)

**Produces:** `Make_Webhook::send( string $event, int $lead_id, array $extra = array() ): void`.

- [ ] `send()` збирає payload (`event`, `sent_at`, `site`, `$extra`, `lead`) і викликає `deliver( $payload, 1 )`.
- [ ] `deliver( array $payload, int $attempt ): bool` — POST JSON; 2xx → meta `1`; інакше meta `0`, `error_log`, і якщо `$attempt < 4` — `wp_schedule_single_event( time() + DELAYS[$attempt], 'leaderauto_make_retry', [ $payload, $attempt + 1 ] )`. Тестова подія (`test`) не пише meta й не повторюється.
- [ ] `transition_post_status` → якщо `la_lead`, обидва статуси наші й різні → `send( 'lead.status_changed', $id, [ 'previous_status' => $old ] )`.
- [ ] `Rest_Contact::handle()` — після `set_delivery` викликати `Make_Webhook::send( 'lead.created', $lead_id )`.
- [ ] «Доставка» — рядок `Make.com: ✓/✗/—` і час `_la_make_at`.
- [ ] Перевірка — пп. 1–4 спеки.

### Task 3: Тестова кнопка

**Files:** `includes/class-make-webhook.php`, `includes/class-settings.php`

- [ ] На екрані налаштувань — окрема форма на `admin-post.php`, `action=leaderauto_make_test`, nonce, кнопка «Надіслати тестову подію» (неактивна, якщо URL порожній).
- [ ] Обробник: `manage_options` + nonce → `deliver()` з тестовим payload → redirect назад з `la_make_test=<код>`; на екрані — `notice-success` / `notice-error`.
- [ ] Перевірка — п. 5 спеки.

### Task 4: Документація

- [ ] `docs/architecture.md` — абзац про Make.com.
- [ ] `docs/documentation/make-setup-guide.md` — як підключити сценарій: Custom webhook → URL і API key → «Надіслати тестову подію» → «Redetermine data structure» → фільтр за `event`.
