# Заявки → Make.com (webhook) — дизайн

Дата: 2026-10-17 · Статус: погоджено · Етап 2 з 3 (етап 1 — `2026-10-17-leads-admin-design.md`)

## Мета

Сайт надсилає в сценарій Make.com подію при новій заявці та при зміні її статусу,
щоб на Make будувати автоматизації (таблиці, SMS/Viber, CRM).

## Події

| `event` | Коли | Звідки |
|---|---|---|
| `lead.created` | заявку збережено (після e-mail / Telegram) | `Rest_Contact::handle()` |
| `lead.status_changed` | статус змінився між двома нашими статусами (`la_new`, `la_progress`, `la_done`, `la_cancel`) — з картки або масовою дією | хук `transition_post_status` |

Не є подіями: переміщення в кошик / з кошика, зміна лише нотатки, honeypot-заявки.

## Дані (JSON, `Content-Type: application/json`)

```json
{
  "event": "lead.status_changed",
  "sent_at": "2026-10-17T12:30:00+03:00",
  "site": "https://example.com/",
  "previous_status": "la_new",
  "lead": {
    "id": 152,
    "created_at": "2026-10-17T10:33:10+03:00",
    "name": "Олена",
    "phone": "095 066 29 21",
    "phone_digits": "0950662921",
    "car": "Tesla Model 3",
    "message": "…",
    "status": "la_done",
    "status_label": "Виконана",
    "note": "…",
    "admin_url": "https://example.com/wp-admin/post.php?post=152&action=edit"
  }
}
```

- `previous_status` — лише в `lead.status_changed`.
- `"test": true` — лише в тестовій події з екрана налаштувань.
- Дати — ISO 8601 у часовому поясі сайту.
- Об'єкт `lead` формує `Leads::to_array( int $id ): array` — він же піде в CRM-API (етап 3).

## Налаштування

Settings → LeaderAuto, env-константи мають пріоритет (як для Telegram):

| Поле | Env | Призначення |
|---|---|---|
| Make.com webhook URL | `LEADERAUTO_MAKE_WEBHOOK_URL` | порожньо = інтеграція вимкнена; лише `https://` |
| Make.com API key | `LEADERAUTO_MAKE_API_KEY` | необов'язково; заголовок `x-make-apikey` |

Кнопка **«Надіслати тестову подію»** (лише `manage_options`, nonce) — надсилає `lead.created`
із тестовими даними й `"test": true`, показує результат (код відповіді) у повідомленні адмінки.

## Доставка

- Синхронно, `wp_remote_post`, timeout 5 с. Успіх = HTTP 2xx.
- Невдача → `wp_schedule_single_event` на повтор: спроба 2 через 1 хв, 3 — через 5 хв,
  4 — через 30 хв. Після 4-ї невдалої спроби — лише `error_log`.
- Повтор надсилає ті самі дані, зафіксовані в момент події (не перечитує заявку).
- URL порожній → нічого не надсилається, meta не пишеться.
- Тестова подія не повторюється й не пише meta.

**Meta заявки:** `_la_make_status` (`1` / `0`) і `_la_make_at` (unix-час останньої спроби) —
оновлюються на кожній спробі будь-якої події цієї заявки. У картці, блок «Доставка»:
`Make.com: ✓ / ✗ / —` + час останньої спроби.

## Файли

- **Новий:** `includes/class-make-webhook.php` — `Make_Webhook`: `hooks()`, `send( string $event, int $lead_id, array $extra = [] )`,
  обробник cron-повтору, тестова кнопка (`admin_post_leaderauto_make_test`).
- **Змінено:** `class-leads.php` — `to_array()`, рядок Make.com у блоці «Доставка».
- **Змінено:** `class-rest-contact.php` — виклик `lead.created`.
- **Змінено:** `class-settings.php`, `helpers.php` — два нові поля + env, кнопка тесту.
- **Змінено:** `leaderauto-core.php` — підключення.
- `uninstall.php` — без змін (налаштування вже видаляються разом з опцією).

## Поза обсягом

HMAC-підпис, кілька webhook-адрес, журнал усіх відправок, події видалення.

## Перевірка

Локальний приймач запитів у Docker-мережі (тимчасовий контейнер, що логує тіло й заголовки):

1. URL порожній → заявка зберігається, запитів немає.
2. Нова заявка → `lead.created` з усіма полями, заголовок `x-make-apikey`, у картці ✓.
3. Зміна статусу в картці та масово → `lead.status_changed` з `previous_status`; зміна лише нотатки — без події.
4. Приймач вимкнено → у картці ✗, заплановано повтор (`wp cron event list`); приймач увімкнено, `wp cron event run` → ✓.
5. Тестова кнопка → запит з `"test": true`, повідомлення з кодом відповіді.
6. Справжній Make.com — перевіряє власник, вказавши URL сценарію.
