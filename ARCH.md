Я б одразу будував проєкт не як «WordPress залили на сервер і редагуємо все руками», а як **керований кодом продукт**.

# 1. Загальна архітектура

```
                    ┌──────────────────────┐
                    │      Розробник        │
                    │                      │
                    │ VS Code + AI          │
                    └──────────┬───────────┘
                               │
                               │ Git
                               ▼
                    ┌──────────────────────┐
                    │       GitHub         │
                    │                      │
                    │ main / develop       │
                    └──────────┬───────────┘
                               │
                               │ Auto Deploy
                               ▼
                    ┌──────────────────────┐
                    │       Railway        │
                    │                      │
                    │ WordPress Container  │
                    │ PHP + Apache/Nginx   │
                    └──────────┬───────────┘
                               │
                    ┌──────────┴───────────┐
                    ▼                      ▼
          ┌─────────────────┐    ┌─────────────────┐
          │    MySQL DB      │    │ Persistent      │
          │                  │    │ Volume / Files  │
          └─────────────────┘    └─────────────────┘
```

Логіка проста:

**Ви пишете код локально → перевіряєте → Git commit → GitHub → Railway автоматично деплоїть нову версію.**

---

# 2. Головний принцип: що зберігаємо в Git

Я рекомендую **не зберігати весь WordPress разом із ядром** у Git-репозиторії.

Краще розділити:

### У GitHub:

```
/wp-content/themes/my-landing-theme/
/wp-content/plugins/my-custom-plugin/
/config/
/Dockerfile
/docker-compose.yml
composer.json
package.json
README.md
```

### Не зберігати в Git:

```
WordPress Core
wp-config.php з паролями
uploads/
cache/
node_modules/
.env
```

Таким чином ваш GitHub буде містити **саме ваш продукт**, а не десятки тисяч файлів WordPress.

---

# 3. Рекомендована структура проєкту

Я б зробив так:

```
wordpress-landing/
│
├── .github/
│   └── workflows/
│       └── deploy.yml
│
├── app/
│   │
│   └── wp-content/
│       │
│       ├── themes/
│       │   └── company-landing/
│       │       │
│       │       ├── assets/
│       │       │   ├── css/
│       │       │   ├── js/
│       │       │   ├── images/
│       │       │   └── fonts/
│       │       │
│       │       ├── inc/
│       │       │   ├── setup.php
│       │       │   ├── enqueue.php
│       │       │   ├── custom-post-types.php
│       │       │   └── api.php
│       │       │
│       │       ├── template-parts/
│       │       │   ├── hero.php
│       │       │   ├── services.php
│       │       │   ├── advantages.php
│       │       │   ├── testimonials.php
│       │       │   ├── faq.php
│       │       │   └── contact-form.php
│       │       │
│       │       ├── functions.php
│       │       ├── style.css
│       │       ├── index.php
│       │       ├── front-page.php
│       │       ├── header.php
│       │       ├── footer.php
│       │       └── screenshot.png
│       │
│       └── plugins/
│           │
│           └── company-core/
│               ├── company-core.php
│               ├── includes/
│               └── assets/
│
├── docker/
│   ├── php/
│   └── nginx/
│
├── .env.example
├── .gitignore
├── docker-compose.yml
├── Dockerfile
├── composer.json
├── package.json
└── README.md
```

## Чому саме так

Я рекомендую всю логіку конкретного лендингу розділити на:

### `company-landing`

Це:

- дизайн;
- HTML;
- CSS;
- JavaScript;
- шаблони сторінок;
- Hero-блок;
- FAQ;
- секції;
- адаптивність.

### `company-core`

Це бізнес-логіка:

- власні REST API;
- інтеграції;
- обробка форм;
- CRM;
- Telegram;
- email;
- AI-функції;
- кастомні сутності.

Це дуже важливе розділення.

**Дизайн змінюється — тема.  
Логіка бізнесу — плагін.**

Тоді через рік ви зможете повністю переробити дизайн сайту, але не втратити функціональність.

---

# 4. Локальне середовище

Я рекомендую використовувати **Docker**.

Архітектура локального середовища:

```
Ваш Windows ПК
│
├── VS Code
│
├── Docker Desktop
│
├── WordPress
│      localhost:8080
│
└── MySQL
```

Файл:

```
docker-compose.yml
```

умовно буде запускати:

```
wordpress
mysql
phpmyadmin (опціонально)
```

Тоді запуск проєкту:

```
docker compose up -d
```

І ви відкриваєте:

```
http://localhost:8080
```

---

# 5. Як я рекомендую працювати з WordPress

Є два можливих підходи.

## Варіант А — стандартний WordPress

Ви створюєте:

```
Сторінка "Головна"
```

І у WordPress:

```
Settings
↓
Reading
↓
Homepage = Головна
```

А ваша тема використовує:

```
front-page.php
```

Для простого лендингу це найкращий варіант.

---

## Варіант Б — Headless WordPress

```
WordPress
    ↓ API
React / Next.js
    ↓
Frontend
```

**Для вашого першого лендингу я це не рекомендую.**

Ви отримаєте:

- більше складності;
- більше серверів;
- більше деплоїв;
- більше точок відмови.

Для лендингу краще:

```
WordPress + Custom Theme
```

---

# 6. Архітектура самого лендингу

Я б не робив одну величезну:

```
front-page.php
```

на 3 000 рядків.

Краще:

```
front-page.php
```

```
<?php get_header(); ?>

<main>

<?php get_template_part('template-parts/hero'); ?>

<?php get_template_part('template-parts/services'); ?>

<?php get_template_part('template-parts/advantages'); ?>

<?php get_template_part('template-parts/about'); ?>

<?php get_template_part('template-parts/testimonials'); ?>

<?php get_template_part('template-parts/faq'); ?>

<?php get_template_part('template-parts/contact-form'); ?>

</main>

<?php get_footer(); ?>
```

Структура:

```
front-page.php
       │
       ├── Hero
       │
       ├── Послуги
       │
       ├── Переваги
       │
       ├── Про компанію
       │
       ├── Відгуки
       │
       ├── FAQ
       │
       └── Контактна форма
```

Це ідеально для роботи з ШІ.

Ви зможете давати AI конкретні завдання:

> Створи блок FAQ у template-parts/faq.php.

або:

> Перероби Hero section, не змінюючи інші файли.

Це значно безпечніше, ніж дозволяти ШІ змінювати величезний монолітний файл.

---

# 7. CSS-архітектура

Я рекомендую не писати все в один `style.css`.

```
assets/
└── css/
    │
    ├── base/
    │   ├── reset.css
    │   ├── variables.css
    │   └── typography.css
    │
    ├── components/
    │   ├── buttons.css
    │   ├── forms.css
    │   └── cards.css
    │
    ├── sections/
    │   ├── hero.css
    │   ├── services.css
    │   ├── faq.css
    │   └── contact.css
    │
    └── main.css
```

Особливо рекомендую використовувати CSS Variables:

```
:root {
    --color-primary: #1E40AF;
    --color-secondary: #0F172A;
    --color-accent: #F59E0B;

    --container-width: 1200px;

    --border-radius: 12px;
}
```

Тоді якщо ви захочете змінити дизайн — не доведеться шукати колір у 30 файлах.

---

# 8. JavaScript

Структура:

```
assets/js/

├── main.js
├── menu.js
├── form.js
├── faq.js
└── animations.js
```

`main.js`:

```
import './menu.js';
import './form.js';
import './faq.js';
```

Я б одразу використовував:

```
npm
Vite
```

для:

- збірки CSS;
- JavaScript;
- мінімізації;
- оптимізації production-файлів.

Тоді:

```
npm run dev
```

для розробки.

І:

```
npm run build
```

перед production.

---

# 9. Git-стратегія

Для початку вам не потрібна складна корпоративна схема.

Я рекомендую:

```
main
 │
 └── production
```

і:

```
develop
 │
 ├── feature/hero-section
 │
 ├── feature/contact-form
 │
 └── feature/telegram-integration
```

Робочий процес:

```
1. Створили feature branch

feature/new-hero

        ↓

2. VS Code + AI

        ↓

3. Локально перевірили

        ↓

4. Git commit

        ↓

5. Push

        ↓

6. Pull Request

        ↓

7. Merge → develop

        ↓

8. Перевірка staging

        ↓

9. Merge → main

        ↓

10. Railway Production
```

Але якщо ви розробляєте самостійно і це один лендинг, можна навіть почати простіше:

```
main
```

і працювати через:

```
git add .
git commit -m "Add FAQ section"
git push
```

---

# 10. GitHub → Railway

Я б налаштував **два середовища**.

## Staging

```
GitHub branch:

develop
```

↓

```
Railway Staging
```

Наприклад:

```
staging.site.com
```

## Production

```
GitHub branch:

main
```

↓

```
Railway Production
```

```
site.com
```

Схема:

```
feature/*
    ↓
develop
    ↓
Railway STAGING
    ↓
Перевірка
    ↓
main
    ↓
Railway PRODUCTION
```

Це набагато правильніше, ніж одразу деплоїти кожну зміну на живий сайт.

---

# 11. Environment Variables

Паролі та ключі **ніколи не зберігаємо у GitHub**.

Файл локально:

```
.env
```

```
DB_NAME=wordpress
DB_USER=wordpress
DB_PASSWORD=super_secret_password

WP_ENV=development
WP_DEBUG=true

MAIL_HOST=smtp.example.com
MAIL_USER=user
MAIL_PASSWORD=password
```

У Railway:

```
Variables
```

Створюємо ті самі параметри.

У Git:

```
.env
```

ігнорується.

Але додаємо:

```
.env.example
```

```
DB_NAME=
DB_USER=
DB_PASSWORD=

WP_ENV=
WP_DEBUG=
```

---

# 12. `.gitignore`

Приблизно:

```
# Environment
.env

# WordPress
wp-content/uploads/
wp-content/cache/

# Dependencies
node_modules/
vendor/

# IDE
.vscode/
.idea/

# OS
.DS_Store
Thumbs.db

# Logs
*.log
```

---

# 13. База даних

Тут є важливий момент.

Ваш код зберігається в:

```
GitHub
```

А контент WordPress:

```
Pages
Posts
Settings
Forms
```

зберігається в:

```
MySQL
```

Тобто архітектура така:

```
                 GitHub
                    │
                    │ Code
                    ▼
                 Railway
                    │
          ┌─────────┴─────────┐
          ▼                   ▼
      WordPress             MySQL
      Application           Database
```

Проблема, яку треба врахувати:

Якщо ви локально створили сторінку:

```
"Головна"
```

вона буде у вашій локальній базі.

А після:

```
git push
```

вона **сама в Railway не з'явиться**, тому що Git не передає MySQL.

---

# 14. Як вирішити проблему контенту

Для лендингу я рекомендую максимально багато структури робити через код.

Наприклад:

```
Theme
├── Hero
├── Services
├── Advantages
├── FAQ
└── Contact
```

А текст та контент можна зробити через:

```
WordPress Custom Fields
```

Наприклад:

```
Hero Title
Hero Subtitle
Hero Button
Hero Image
```

У production контент редагується через WordPress Admin.

А структура і дизайн — через Git.

Тобто:

```
CODE
│
├── GitHub
├── VS Code
└── Railway Deploy


CONTENT
│
└── WordPress Admin
```

Це дуже правильне розділення.

---

# 15. Persistent Volume

Для Railway вам потрібно окремо продумати зберігання:

```
wp-content/uploads/
```

Бо якщо Railway створює новий контейнер при деплої, завантажені користувачами файли можуть бути втрачені без persistent storage.

Тому:

```
Railway
│
├── WordPress Service
│
├── Database
│
└── Persistent Volume
        │
        └── uploads/
```

Наприклад:

```
/wp-content/uploads
```

повинен бути persistent.

---

# 16. Форма зворотного зв'язку

Я рекомендую одразу робити форму не просто через стандартний плагін.

Архітектура:

```
User
  ↓
Contact Form
  ↓
WordPress AJAX / REST API
  ↓
Validation
  ↓
Email
  ↓
CRM / Telegram
```

Наприклад:

```
Клієнт
   │
   ▼
Форма
   │
   ▼
POST /wp-json/company/v1/contact
   │
   ├── Перевірка
   │
   ├── Spam Protection
   │
   ├── Email
   │
   ├── Telegram
   │
   └── CRM
```

Цю логіку я б розміщував у:

```
company-core
```

а не в темі.

---

# 17. AI-first розробка

Оскільки ви хочете активно використовувати ШІ, я рекомендую створити в репозиторії файл:

```
AI_CONTEXT.md
```

Наприклад:

```
# Project Architecture

## Stack

- WordPress
- PHP 8.x
- MySQL
- Docker
- Railway
- GitHub

## Rules

1. Do not modify WordPress Core.
2. Theme contains presentation logic only.
3. Business logic goes into company-core plugin.
4. Do not store secrets in Git.
5. Every new feature must be modular.
6. Do not modify unrelated files.
7. Follow WordPress Coding Standards.
8. All user input must be validated and sanitized.
9. Escape all output.
10. Before changing architecture, explain the proposed change.
```

Це буде вашим **постійним технічним контекстом для AI**.

Особливо корисно, якщо ви будете працювати з різними AI-інструментами.

---

# 18. Повна рекомендована схема

```
                         ┌───────────────┐
                         │    VS Code    │
                         │               │
                         │      AI       │
                         └───────┬───────┘
                                 │
                                 ▼
                         ┌───────────────┐
                         │ Local Docker  │
                         │               │
                         │  WordPress    │
                         │  MySQL        │
                         └───────┬───────┘
                                 │
                                 │ Git Push
                                 ▼
                         ┌───────────────┐
                         │    GitHub     │
                         │               │
                         │ develop/main  │
                         └───────┬───────┘
                                 │
                      ┌──────────┴──────────┐
                      │                     │
                      ▼                     ▼
               ┌─────────────┐       ┌─────────────┐
               │   STAGING   │       │ PRODUCTION  │
               │             │       │             │
               │   Railway   │       │   Railway   │
               └──────┬──────┘       └──────┬──────┘
                      │                     │
                      ▼                     ▼
                 MySQL DB               MySQL DB
```

# 19. Моя конкретна рекомендація для вас

Я б зробив першу версію саме так:

### Development

```
Windows
↓
VS Code
↓
AI
↓
Docker Desktop
↓
WordPress + MySQL
```

### Source Control

```
Git
↓
GitHub
```

### Deployment

```
develop
↓
Railway Staging

main
↓
Railway Production
```

### WordPress

```
Custom Theme
+
Custom Plugin
```

### Frontend

```
PHP Templates
+
HTML
+
CSS
+
JavaScript
+
Vite
```

### Дані

```
MySQL
```

### Файли

```
Persistent Storage
```

---

## Найважливіше рішення

Я б **не починав одразу з десятків плагінів і Elementor**.

Для вашого підходу — особливо з урахуванням того, що ви вже працюєте над власною ERP і плануєте використовувати ШІ для розробки — логічніше зробити:

```
WordPress
      +
Custom Theme
      +
Custom Core Plugin
      +
GitHub
      +
Railway
      +
Docker
      +
VS Code + AI
```

Це дасть вам нормальну контрольовану архітектуру, яку можна буде поступово розширювати: спочатку лендинг, потім інтеграція з CRM, потім API, особистий кабінет або навіть інтеграція з вашою ERP.