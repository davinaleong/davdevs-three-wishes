# PLAN

## 1) Product scope and milestones

### Phase 0 — Foundations (Day 1–2)

* Repo setup (Laravel, Tailwind, Lucide, Montserrat)
* Base layout + Blade component system
* Auth scaffolding (register/login/password reset)
* Email verification enabled

### Phase 1 — MVP: Wishes + Year Theme + Dec 31 email (Week 1)

* “Theme of the Year” system (annual theme, verse, colors, icon/logo refs)
* Wishes CRUD (3–10 wishes per user)
* Edit window enforcement:

  * **This year only:** allow edits until **Feb 26** (inclusive, Singapore time)
  * **Future years:** allow edits until **Jan 31** of the current year
* Scheduled email on **Dec 31** to send user their wishes + reminder of Jesus’ faithfulness

### Phase 2 — Security & Compliance hardening (Week 2)

* 2FA enable/disable + recovery codes
* Activity logging (user)
* Hard delete account flow (double-confirmation + purge all data)
* Public identification via UUIDs/Slugs (URLs), internal relations via IDs

### Phase 3 — Dev-only email preview + Admin panel (Low priority)

* Dev-only email preview routes/screens
* Admin auth (separate table/guard)
* Admin dashboard + activity logs + theme CRUD + manual email triggers

---

## 2) Core user flows

### A) Landing / Welcome

* Shows:

  * **Theme of the Year**
  * **Jeremiah 29:11 (NKJV)** (theme verse for app)
  * CTA: Sign up / Log in
* Visually driven by theme tokens (colors, logo, favicon)

### B) Onboarding

* Sign up → verify email required before full access
* After verification → create wishes

### C) Wish Management

* Enforce **min 3, max 10** wishes
* Editing allowed only within “edit window”
* After window closes:

  * Wishes become read-only (still viewable)
  * UI shows “Editing closed for this year”

### D) Year-end reminder email

* On Dec 31: send formatted email with:

  * Theme header + colors
  * List of wishes recorded for that year
  * Verse snippet / footer branding

### E) Account deletion (hard delete)

* Step 1: user types current theme text (case-insensitive)
* Step 2: user types “delete” (case-insensitive)
* Then hard-delete:

  * user row
  * wishes
  * logs
  * 2FA secrets/recovery codes
  * anything tied to user

---

## 3) Data model (IDs for relations, UUIDs for public)

Use numeric IDs as PK/FK. Add UUID columns for public URLs.

### Tables

**users**

* id (PK)
* uuid (unique)
* name
* email (unique)
* email_verified_at
* password
* two_factor_enabled_at (nullable)
* two_factor_secret (encrypted/nullable)
* two_factor_recovery_codes (encrypted json/nullable)
* timestamps

**themes** (Theme of the Year)

* id
* uuid (for admin URLs)
* year (int, unique)
* theme_title (string) — “Theme of the Year”
* theme_tagline (nullable)
* theme_verse_reference (default: “Jer 29:11”)
* theme_verse_text (default NKJV text or stored snippet)
* logo_path / logo_key (nullable)
* favicon_path / favicon_key (nullable)
* colors_json (json) — tokenized palette
* email_styles_json (json) — optional overrides for email-safe styling
* is_active (bool)
* timestamps

**wishes**

* id
* uuid (public)
* user_id (FK)
* theme_id (FK) or year (int) (pick one; theme_id is nicer)
* position (int) — ordering 1..10
* content (text)
* timestamps

**user_activity_logs**

* id
* user_id
* action (string) — e.g. WISH_CREATED, LOGIN, 2FA_ENABLED, ACCOUNT_DELETION_REQUESTED
* meta (json nullable) — ip, user_agent, wish_uuid, etc
* created_at

**admins** (separate)

* id
* uuid
* name
* email
* password
* is_super_admin (bool)
* timestamps

**admin_activity_logs**

* id
* admin_id
* action
* meta (json)
* created_at

Optional (nice-to-have later):

* **email_jobs_log**: track Dec 31 sends, retries, failures.

---

## 4) Business rules and “edit window” logic

### Current year special-case

* For the current year’s theme (e.g., 2026), allow edits until **Feb 26, 2026 23:59:59 Asia/Singapore**.
* Future years: allow edits until **Jan 31** of that year 23:59:59.

Implementation idea:

* Put this behind a single service:

  * `WishEditWindow::isOpen(Theme $theme, Carbon $now): bool`
* Store window policy in code (simplest), or store per-theme config (more flexible).

UI:

* If closed:

  * Disable form fields + show notice banner
  * Still allow viewing and exporting (read-only)

---

## 5) UI architecture (Blade + Tailwind + Lucide + Montserrat)

### Blade layout & components

* `layouts/app.blade.php` (auth pages)
* `layouts/guest.blade.php` (welcome)
* Components:

  * `<x-app.header />` (logo, theme title)
  * `<x-app.container />`
  * `<x-ui.card />`, `<x-ui.button />`, `<x-ui.input />`, `<x-ui.alert />`
  * `<x-wishes.list />`, `<x-wishes.item />`, `<x-wishes.form />`
* Lucide icons:

  * wrap via Blade component helper, e.g. `<x-icon name="sparkles" />`

### Theming & tokens (annual)

* Store token palette in `themes.colors_json`
* At runtime:

  * Apply CSS variables on `:root` from the active theme (inline `<style>` or compiled)
  * Tailwind can reference CSS vars (recommended so you don’t rebuild per year)
* Assets:

  * logo + favicon loaded from theme config

---

## 6) Email system

### Email templates

* Use Mailable + Blade email views
* Design constraints:

  * Keep email CSS simple (inline-friendly)
  * Use theme colors via “email_styles_json” (fallback to theme colors)

### Scheduling (Dec 31)

* Use Laravel scheduler + queued jobs:

  * Daily scheduler checks if today is Dec 31, then dispatch “SendYearEndWishesEmail” jobs per user (batched)
* Ensure:

  * Only verified emails receive the Dec 31 email
  * Idempotency key: (user_id + theme_id + year) to avoid double-send

### Dev-only preview

* Local environment only routes:

  * `/dev/email/year-end?user=...&year=...`
  * `/dev/email/verify`
  * `/dev/email/2fa`
* Restrict via `APP_ENV=local` middleware.

---

## 7) Security checklist (your requirements mapped)

* **Email verification**: required before wish creation/editing
* **2FA**: enable/disable, recovery codes, re-auth for sensitive actions
* **UUIDs in URLs**:

  * `/wishes/{wish:uuid}`
  * `/me/settings/security`
  * Admin `/admin/themes/{theme:uuid}`
* **Hard deletion**:

  * Double confirmation inputs
  * Transactional delete
  * Consider a final “Are you absolutely sure” screen + password re-entry (optional but strong)
* **Activity logs**:

  * Log auth, CRUD, 2FA changes, deletion attempts, email triggers
* **Rate limiting**:

  * login, verify resend, 2FA attempts, delete confirmations

---

## 8) Admin panel (low priority) — what to include

### Admin dashboard (recommended widgets)

* Active theme (year + title)
* User counts:

  * total users
  * verified users
  * users with 2FA enabled
* Wishes stats:

  * average wishes per user
  * users below minimum (data validation)
* Email stats (if you log sends):

  * Dec 31 send status: queued/sent/failed
* Recent activity feed:

  * last 20 admin actions
  * last 20 user actions (view-only)

### Admin features list

* Manage Theme of the Year:

  * CRUD theme, set active, upload logo/favicon, edit token colors
* View user activity logs (filter by user email/uuid, action, date)
* Manual triggers:

  * resend verification email
  * send “year-end wishes” email for a user/year
  * send “password reset” link (or guide user)
* Admin broadcast email UI:

  * subject + content + optional target segment (verified only, etc.)
* Admin activity logging (separate table)

### “One account only” best practice

* Seed admin in database seeder (env-driven email/password)
* Disable admin self-registration
* Restrict admin routes behind admin guard + 2FA mandatory for admin

---

## 9) Suggested route map (high level)

**Public**

* `/` welcome (theme)
* `/register`, `/login`, `/forgot-password`
* `/email/verify` etc.

**User**

* `/wishes` (index + add/edit within window)
* `/wishes/{uuid}` (optional detail)
* `/me/settings` (profile basics)
* `/me/settings/security` (2FA, delete account)

**Dev only**

* `/dev/emails/*`

**Admin**

* `/admin` dashboard
* `/admin/themes`
* `/admin/users` (optional basic list)
* `/admin/logs/users`
* `/admin/logs/admins`
* `/admin/emails`

---

## 10) Build order (so you don’t get stuck)

1. Theme model + active theme resolver (welcome page shows theme)
2. Auth + email verification gate
3. Wishes CRUD + min/max rules + position ordering
4. Edit-window enforcement (Feb 26 special-case + Jan 31 generic)
5. Year-end email template + scheduler job
6. Activity logging hooks
7. 2FA + recovery codes
8. Hard delete flow (double confirmation) + full purge
9. Dev-only email previews
10. Admin panel basics
