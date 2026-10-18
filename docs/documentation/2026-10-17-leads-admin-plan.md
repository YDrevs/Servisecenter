# Заявки в адмінці — план реалізації

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** зберігати кожну заявку з контактної форми як запис `la_lead` і дати менеджеру список, пошук, статуси й нотатку в wp-admin.

**Architecture:** новий клас `LeaderAuto\Core\Leads` у плагіні `leaderauto-core` реєструє CPT і чотири статуси, будує екран списку й картку на стандартних механізмах WordPress (`edit.php`, `post.php`, метабокси). `Rest_Contact` створює заявку до відправки e-mail/Telegram.

**Tech Stack:** WordPress 7 / PHP 8.1, плагін `leaderauto-core`, локальне оточення Docker (`make site`, `make wp`).

**Spec:** `docs/documentation/2026-10-17-leads-admin-design.md`

## Global Constraints

- PHP ≥ 8.1, namespace `LeaderAuto\Core`, текстовий домен `leaderauto-core`, стиль коду — як у `class-rest-contact.php` (таби, WordPress-пробіли в дужках, `array()`).
- Копія інтерфейсу — українською.
- Тип запису `la_lead`; статуси `la_new`, `la_progress`, `la_done`, `la_cancel`.
- Meta: `_la_name`, `_la_phone`, `_la_phone_digits`, `_la_car`, `_la_note`, `_la_delivered_mail`, `_la_delivered_tg`.
- Доступ — `edit_others_posts` (адміністратор, редактор); створення вручну заборонене.
- `uninstall.php` не чіпати.
- Тестового фреймворку немає — кожна задача закінчується ручною перевіркою через `curl` / `make wp`, з очікуваним результатом.
- Не робити `git add .`; стейджити явні шляхи.

## Файли

| Файл | Відповідальність |
|---|---|
| `app/wp-content/plugins/leaderauto-core/includes/class-leads.php` (новий) | CPT, статуси, збереження заявки, екран списку, картка |
| `app/wp-content/plugins/leaderauto-core/leaderauto-core.php` | підключення класу |
| `app/wp-content/plugins/leaderauto-core/includes/class-rest-contact.php` | створення заявки в `handle()`, нова логіка `502` |
| `docs/architecture.md` | оновити розділ про форму |
| `docs/documentation/leads-admin-guide.md` (новий) | інструкція для менеджера |

---

### Task 1: Сховище заявок і запис з REST-ендпоінта

**Files:**
- Create: `app/wp-content/plugins/leaderauto-core/includes/class-leads.php`
- Modify: `app/wp-content/plugins/leaderauto-core/leaderauto-core.php`
- Modify: `app/wp-content/plugins/leaderauto-core/includes/class-rest-contact.php` (`handle()`)

**Interfaces:**
- Produces: `Leads::POST_TYPE` (`'la_lead'`), `Leads::statuses(): array<string,string>`,
  `Leads::create( array{name:string,phone:string,car:string,message:string} $p ): int` (0 при помилці),
  `Leads::set_delivery( int $id, bool $mail, bool $tg ): void`.

- [ ] **Step 1:** створити `class-leads.php` з `hooks()` → `init` → `register()` (CPT з `supports => false`, `public => false`, `show_ui => true`, `capabilities => [create_posts => do_not_allow, edit_posts => edit_others_posts]`, `map_meta_cap => true`, `menu_icon => dashicons-email-alt`) і `register_post_status` для кожного статусу (`protected`, `show_in_admin_all_list`, `show_in_admin_status_list`, `label_count`). Додати `create()` (`wp_insert_post` зі статусом `la_new`, `meta_input`, `error_log` при `WP_Error`) і `set_delivery()`.
- [ ] **Step 2:** у `leaderauto-core.php` — `require …/class-leads.php` і `( new \LeaderAuto\Core\Leads() )->hooks();`.
- [ ] **Step 3:** у `Rest_Contact::handle()` після `$payload = …`:

```php
$lead_id       = Leads::create( $payload );
$sent_mail     = $this->send_email( $payload );
$sent_telegram = $this->send_telegram( $payload );
if ( $lead_id ) {
	Leads::set_delivery( $lead_id, $sent_mail, $sent_telegram );
}

if ( ! $lead_id && ! $sent_mail && ! $sent_telegram ) {
	// 502 як раніше
}
```

- [ ] **Step 4: перевірка**

```bash
curl -s -X POST localhost:8080/wp-json/leaderauto/v1/contact -H 'Content-Type: application/json' \
  -d '{"name":"Тест","phone":"+38 (098) 111-22-33","car":"BYD Seal","message":"Перевірка"}'
# → {"ok":true,...}
make wp CMD="post list --post_type=la_lead --post_status=any --fields=ID,post_title,post_status"
# → один рядок, статус la_new
make wp CMD="post meta list <ID>"
# → _la_phone, _la_phone_digits=380981112233, _la_car, _la_delivered_mail=1, _la_delivered_tg=0
```

Невалідний телефон → `422`, заявки немає. Заповнений `website` → `200`, заявки немає.

- [ ] **Step 5: commit** — `git add` трьох файлів, `git commit -m "Заявки: зберігати в БД як la_lead"`.

### Task 2: Екран списку

**Files:** Modify `includes/class-leads.php`

- [ ] **Step 1:** колонки (`manage_la_lead_posts_columns`): `cb`, `la_date` (дата `post_date`, сортування як `date`), `title` (Ім'я), `la_phone` (`tel:`-посилання), `la_car`, `la_message` (перші 80 символів), `la_status` (назва статусу, «Нова» жирним).
- [ ] **Step 2:** бейдж кількості `la_new` на пункті меню (`admin_menu`, пріоритет 99, `<span class="awaiting-mod">`).
- [ ] **Step 3:** пошук (`posts_search`): лише головний запит адмінки для `la_lead`; додати `OR ID IN (SELECT post_id FROM postmeta WHERE meta_key IN ('_la_phone','_la_car','_la_note') AND meta_value LIKE %s)` і окремо `_la_phone_digits LIKE %digits%`, якщо в запиті ≥ 3 цифр.
- [ ] **Step 4:** масові дії: прибрати `edit`, додати `la_status_<key>` «Статус → <назва>»; обробник `handle_bulk_actions-edit-la_lead` міняє статус через `wp_update_post` з перевіркою `edit_post` і повертає `updated=N`. У `post_row_actions` прибрати «Швидке редагування» (його список статусів не знає наших).
- [ ] **Step 5: перевірка** — `/wp-admin/edit.php?post_type=la_lead`: вкладки статусів, бейдж «1», пошук `1112233` знаходить заявку, масова дія «Статус → В роботі» переносить її у вкладку «В роботі», бейдж зникає.
- [ ] **Step 6: commit** — `git commit -m "Заявки: список, пошук, масова зміна статусу"`.

### Task 3: Картка заявки

**Files:** Modify `includes/class-leads.php`

- [ ] **Step 1:** `add_meta_boxes_la_lead`: прибрати `submitdiv`; додати «Клієнт» (main, лише читання), «Обробка» (side: `<select name="post_status">`, `<textarea name="la_note">`, nonce, кнопка `name="save"`, посилання «У кошик»), «Доставка» (side, ✓/✗/—).
- [ ] **Step 2:** `save_post_la_lead`: перевірити nonce `leaderauto_lead_save<ID>` і `edit_post`; зберегти `_la_note` (`sanitize_textarea_field`).
- [ ] **Step 3:** `wp_insert_post_data`: для `la_lead` статус не з білого списку (і не `trash`) замінити на попередній статус, а для нового запису — на `la_new`.
- [ ] **Step 4:** `wp_untrash_post_status`: для `la_lead` відновлювати попередній статус, а не `draft`.
- [ ] **Step 5: перевірка** — відкрити заявку, змінити статус на «Виконана» й нотатку → після збереження обидва значення на місці; `make wp CMD="post update <ID> --post_status=publish"` → статус лишається попереднім; у кошик і назад → статус попередній; subscriber не бачить меню (`make wp CMD="user create sub sub@example.test --role=subscriber"`, перевірити `user_can`).
- [ ] **Step 6: commit** — `git commit -m "Заявки: картка зі статусом і нотаткою"`.

### Task 4: Документація

- [ ] `docs/architecture.md` — у розділі про форму: заявка зберігається як `la_lead` до відправки, `502` лише якщо не вдалось і збереження, і доставка.
- [ ] `docs/documentation/leads-admin-guide.md` — інструкція для менеджера: де заявки, статуси, пошук, нотатка, кошик.
- [ ] commit.
