# MCYF — Community Youth Forum (Dynamic PHP Version)

## ✅ Phase 1 — Complete: Foundation, Auth, Localization, Theme Settings

This is the dynamic PHP + MySQL rebuild of your static HTML app.
Everything below is done, tested for structure, and ready to run.

---

## 📁 Folder Structure

```
mcyf-php/
├── app/
│   ├── Controllers/          (Phase 2+: Auth/, Admin/ controllers)
│   ├── Models/                (Phase 2+: Member, Event, etc.)
│   ├── Middleware/            (Phase 2+)
│   └── Helpers/
│       ├── DB.php             ← PDO singleton connection
│       ├── Auth.php           ← session, login, permissions, CSRF
│       └── Lang.php           ← t() translator + settings + theme
│
├── config/
│   ├── database.php           ← EDIT THIS: your MySQL credentials
│   ├── app.php                ← default app name/theme/lang (DB overrides this)
│   ├── schema.sql              ← run this to create all tables
│   └── seed.php                ← run this to insert demo data
│
├── lang/
│   ├── ur/lang.php             ← Urdu (default)
│   ├── en/lang.php             ← English
│   └── roman_ur/lang.php       ← Roman Urdu
│   (add more: create lang/xx/lang.php with the same keys)
│
├── views/
│   ├── layouts/
│   │   ├── main.php            ← master layout (navbar+footer wrap)
│   │   └── auth.php            ← minimal layout for login/signup
│   ├── components/
│   │   ├── navbar.php          ← dynamic, permission-aware nav
│   │   └── footer.php          ← dynamic, DB-driven contact info
│   ├── auth/                   ← login/signup view templates
│   ├── admin/                  (Phase 2+)
│   ├── pages/
│   │   └── home.view.php       ← landing page content
│   └── errors/
│
├── public/                     ← 🌐 THIS is your web root / document root
│   ├── index.php               ← landing page
│   ├── auth/
│   │   ├── login.php
│   │   ├── signup.php
│   │   ├── logout.php
│   │   └── change-password.php
│   ├── admin/
│   │   ├── index.php           ← admin dashboard
│   │   └── settings.php        ← ⭐ platform settings form (logo/name/colors)
│   ├── errors/access-denied.php
│   ├── pending.php
│   └── assets/
│       ├── css/style.css       ← all styling, theme-variable driven
│       ├── js/app.js           ← vanilla JS utilities
│       └── uploads/            ← logos/gallery/avatars go here (writable!)
│
├── bootstrap.php               ← loaded by every page first
├── .htaccess                   ← blocks direct access outside /public
└── README.md                   ← this file
```

---

## 🚀 Setup Instructions

### 1. Database
```bash
mysql -u root -p -e "CREATE DATABASE mcyf_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p mcyf_db < config/schema.sql
```

### 2. Configure credentials
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mcyf_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 3. Seed demo data (all your existing static content)
```bash
php config/seed.php
```
This inserts: settings, members (with all 5 roles), announcements, events,
emergency services, notifications, gallery images/videos, about content, contact info.

### 4. Point your web server at `/public`
- **XAMPP/WAMP**: put `mcyf-php` folder in `htdocs`, then visit
  `http://localhost/mcyf-php/public/`
- **Production**: set Apache/Nginx **document root** directly to `mcyf-php/public`
  (cleaner — then URLs have no `/public/` in them)

### 5. Make uploads folder writable
```bash
chmod -R 755 public/assets/uploads
```

### 6. Login
Visit `/auth/login.php` — test account buttons are shown automatically
(pulled live from the `members` table):

| Role | Email | Password |
|---|---|---|
| Super Admin | superadmin@masoodforum.org | Super@123 |
| Admin | admin@masoodforum.org | Admin@123 |
| Committee Head | committee@masoodforum.org | Committee@123 |
| Member | member@masoodforum.org | Member@123 |
| Pending | pending@masoodforum.org | Pending@123 |

---

## ✅ What's Done in Phase 1

1. **Full file/folder architecture** — MVC-ish, clean separation (config / app / views / public)
2. **Database schema** — 13 tables covering every module of your original app
   (members, settings, announcements, notifications, events, event_gallery,
   committees, committee_members, gallery_images, gallery_videos,
   emergency_services, contact_info, about_content)
3. **Authentication** — login, signup (creates `pending` member), logout,
   change password. Passwords hashed with bcrypt. CSRF protection on all forms.
4. **Authorization** — centralized `hasPermission()` / `requireAuth()` system
   mirroring your original role → permission map (visitor, pending, member,
   committee_head, admin, super_admin)
5. **Localization system** — `t('key')` translator function. Three language
   files ready (`ur`, `en`, `roman_ur`). **To add a new language**: copy
   `lang/en/lang.php` → `lang/ps/lang.php`, translate the values, then select
   it in Admin → Platform Settings. No code changes needed.
6. **Centralized Platform Settings form** (Admin → Platform Settings):
   - Upload logo
   - Platform name (Urdu + English)
   - Subtitle
   - Icon (Bootstrap Icons class, for when no logo uploaded)
   - Primary / Secondary / Accent (golden) / optional Extra theme color
   - Active language selector
   - All stored in `settings` DB table, injected as CSS variables on every page
7. **Dynamic navbar & footer components** — permission-aware menu items,
   live unread-notification badge, contact info pulled from DB
8. **Landing page** — fully dynamic: live stats (member/event/committee counts),
   About/Vision/Mission/Objectives from DB, upcoming events from DB
9. **Centralized CSS** — one `style.css` using CSS custom properties, so theme
   color changes apply site-wide instantly, fully responsive (mobile-first),
   RTL-aware (Urdu) and LTR-aware (English/Roman Urdu) automatically
10. **RTL/LTR auto-switching** — `<html dir="rtl|ltr">` and Bootstrap RTL/LTR
    CSS build both switch automatically based on active language

---

## 🔜 Phases 2–5 (awaiting your go-ahead)

- **Phase 2**: Members module (list/search/filter/approve/reject/CRUD),
  Dashboard (role-based widgets), Profile page + photo upload
- **Phase 3**: Announcements, Notifications, Events (+ event gallery), Committees
  — full admin CRUD for each, public-facing list/detail views
- **Phase 4**: Gallery (images + videos, category filter, lightbox), Emergency
  Services, Contact page (+ contact form → DB), About page (dynamic sections)
- **Phase 5**: Admin → Users & Roles management (change role/status,
  permission editor UI), final polish, security hardening pass, testing checklist

---

## 🎨 How the Theme/Localization System Works (for your reference)

**Colors**: Admin sets hex values in Settings → stored in `settings` table →
`themeVars()` in `Lang.php` injects a `<style>` block with `--forum-green`,
`--forum-gold` etc. on every page load → `style.css` uses `var(--forum-gold)`
everywhere. Change once, applies everywhere instantly.

**Language**: Admin picks active language in Settings → stored as
`app_lang` in `settings` table → `Lang.php` loads `lang/{code}/lang.php` →
every view calls `t('some_key')` instead of hardcoding text → switching
`app_lang` changes 100% of static text across the whole site with zero code edits.

**Adding a new language** (e.g. Pashto):
1. Copy `lang/en/lang.php` to `lang/ps/lang.php`
2. Translate every value (keep keys identical)
3. Set `'dir' => 'rtl'` or `'ltr'` and `'bootstrap_css'` to the right CDN build
4. Go to Admin → Platform Settings → select "ps" from the language dropdown

No PHP/HTML files need to be touched.
