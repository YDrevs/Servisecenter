# Reference capture — content & design tokens

Extracted from the running reference site (`make up`) on 2026-09-06. This is the source of
truth for the rebuild in `app/wp-content/themes/leaderauto`. Raw HTML is in
`app/reference/pages/` (git-ignored — regenerate with `make capture`).

## Design tokens

| Token | Value | Notes |
|---|---|---|
| Brand primary | `#1700be` (a.k.a. `#1700bd`) | deep electric indigo — buttons, gradients, shadows |
| Brand primary — light | `#326bff` / `#1959ff` | lighter blue seen in gradients |
| Hero gradient | `linear-gradient(180deg, #000 0%, #1700be 100%)` | dark hero panels |
| Accent | `#ff6900` | orange, minor use |
| Text | `#333333` | body |
| Muted | `#666666` | secondary text |
| Borders | `#dddddd` / `#eeeeee` | |
| Surfaces | `#ffffff`, `#000000` | dark sections use white text |
| Divi default blue `#2ea3f2` | — | **leftover default, not brand — do not use** |
| Heading font | **Kanit**, 700 | fallback `Helvetica, Arial, sans-serif` |
| Body font | **Open Sans** | fallback `Arial, sans-serif` |
| Button | white bg, `#1700bd` text, **0 radius** (square), Kanit 700 | |
| Also loaded (decorative, unused) | Alfa Slab One, Aboreto, Bungee Tint, Arima | skip |

## Site chrome

- **Nav:** Головна · Автодилер · Про нас · Контакти
- **Phone:** `+380 (95) 066 29 21` (Viber + Telegram: `(095) 066 29 21`)
- **Address:** «Завітайте» — 60313, с. Магала, вул. Гр. Нандріша, 6 (Chernivtsi district)
- **Email on the contact page is `hello@divicardetailing.com` — DEMO, not real. Omit / replace.**
- The rendered footer (Archives / Categories / social / "Elegant Themes" / "WordPress") is the
  **stock Divi footer with default widgets** — not real content. Build a real footer.

## Page 10 — Головна (home / service landing)  → `front-page.php`

Real, rich content. Sections in order:
1. **Hero** — H1 «LeaderAuto – Service & Dealer», sub «Програмний та технічний ремонт
   електромобілів BYD, Tesla, Zeekr, Volkswagen, Nissan та інших марок», CTA «Отримати консультацію».
   Second H1 (should be a sub): «Діагностика, оновлення, кодування, заміна мастил, підбір та
   замовлення запчастин».
2. **Наші послуги** — 3 cards, each with a bullet list:
   - *Програмний ремонт та діагностика*: Комп'ютерна діагностика · Оновлення ПЗ · Кодування та
     адаптація блоків · Усунення помилок та збоїв · Налаштування електронних систем · Робота з
     батарейними системами
   - *Технічне обслуговування*: Заміна мастил та технічних рідин · Обслуговування редуктора ·
     Заміна фільтрів · Перевірка ходової частини · Гальмівна система · Обслуговування систем
     охолодження
   - *Запчастини та комплектуючі*: Замовлення оригінальних запчастин · Аналоги перевірених
     брендів · Пошук рідкісних деталей · Допомога з підбором · Швидка доставка
3. **100% Вирішення проблем** — CTA «Записатися»
4. **Чому власники EV звертаються до нас** — 5 points: Спеціалізація на електромобілях · Досвід
   роботи з BYD · Сучасна діагностика · Прозорий підхід · Допомога із запчастинами
5. **Програма лояльності** — «10% Знижки на замовлення будь-якої деталі»
6. **Налаштування автопілоту та автопаркування** — CTA «Записатися» + «Умови»
7. **FAQ / контакт block** — Viber & Telegram `(095) 066 29 21`; 5 Q&A:
   Ви працюєте тільки з BYD? · Чи можна замовити запчастини у вас? · Ви займаєтесь програмним
   ремонтом? · Чи є гарантія? · Скільки часу займає діагностика?
- Images on this page are `detailing-*.png` **stock demo** — replace with real assets
  (`BYD.jpg`, `1w.jpg`, `icon.jpg`) or neutral placeholders. Fix source typos
  («Запиисатися», «F requently A sked Q uestions»).

## Page 15 — Автодилер (BYD dealer)  → `page-dealer.php`

**Mostly untranslated Divi "Car Detailing" demo** (Full Detailing / Lorem ipsum / Wheel
Protection / Paint Protection / "Make an Appointment" / "Quick Links: FAQ, About Us, Careers,
Press, Contact"). Only real: H2 «Обери свою серед наявних !», CTA «Test Drive», image
`2026/05/1w.jpg` (BYD). **Do not port the demo.** Build a minimal real page: BYD models /
test-drive offer + `BYD.jpg` / `1w.jpg` + `Sea-Lion-08-Дебют.mp4` + a CTA to the contact form.
→ open question for the user: what real dealer content do they want here?

## Page 17 — Про нас (about)  → `page.php` / `page-about.php`

Real. Sections:
1. H1 «Про нас» + H2 «Експертний сервіс для сучасних електромобілів» + body «Ми
   спеціалізуємось на обслуговуванні та ремонті електромобілів, з акцентом на BYD та інші
   сучасні EV-платформи…»
2. **ЯК МИ ПРАЦЮЄМО** — 5 steps: 1. Заявка або дзвінок · 2. Діагностика · 3. Узгодження ·
   4. Ремонт · 5. Видача авто
3. **Stats row** — labels «Happy Clients / Towing Services / Projects Done» are demo; keep the
   3-number layout but supply real Ukrainian labels + numbers (ask user).
4. CTA «Потрібна діагностика або ремонт електромобіля?»
- Images `detailing-14/15.png` are demo — replace.

## Page 13 — Контакти (contacts)  → `page-contacts.php`

Real. H1 «Залишити заявку» + form (fields: Ваше ім'я · Ваш телефон · Ваш автомобіль ·
Напишіть повідомлення · button «Відправити») → wire to `leaderauto-core` REST endpoint.
Info blocks: «Завітайте» (address above) · «Дзвоніть нам» `+380 (95) 066 29 21` · «@mail»
(replace the demo address — ask user for the real one, or drop).

## Brands (order of prominence, from CLAUDE.md + hero copy)

BYD → Tesla → Zeekr → Volkswagen ID → Nissan Leaf
