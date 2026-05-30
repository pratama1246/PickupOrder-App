# 🛒 PickupOrder-App (PNC)

**PickupOrder-App** is a **Laravel 13 (PHP 8.4)** web application for **canteen/food pickup ordering**, designed for internal use at **Politeknik Negeri Cilacap**.

It supports 3 main roles:

- **Student (User / Mahasiswa)**: browse canteens & menus, add items to cart, checkout, pay, view order history, review menu items, and reorder.
- **Vendor**: manage canteen profile, manage menus, process incoming orders (including QR/code scanning), and view sales reports.
- **Admin**: manage canteens and users (including bulk actions and user import).

> Payments are integrated via **Midtrans** (Snap/notification) and the app exposes a webhook endpoint to receive payment notifications.

> Built as a college project at Politeknik Negeri Cilacap, Informatics Engineering Department.

---

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=black)
![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![Figma](https://img.shields.io/badge/Figma-F24E1E?style=for-the-badge&logo=figma&logoColor=white)

---

## Table of Contents

- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Local Setup](#local-setup)
- [Environment Configuration (.env)](#environment-configuration-env)
- [Database Seed / Demo Accounts](#database-seed--demo-accounts)
- [Database Structure (Migrations)](#database-structure-migrations)
- [Payments (Midtrans)](#payments-midtrans)
- [Run the App](#run-the-app)
- [Testing](#testing)
- [Security Notes](#security-notes)
- [UI/UX Design](#uiux-design)
- [Team](#team)
- [License](#license)

---

## Key Features

### Authentication & Profile
- Login (with throttling)
- Forgot password
- Logout
- **Force password change on first login**
- Update profile + update password

### Student (User / Mahasiswa)
- Home & About page
- Browse canteens and menus
- Cart (add/update/remove)
- Checkout (prepare, submit, retry payment)
- Order history:
  - view details
  - cancel/delete (including cancel by payment group)
  - review menu items
  - reorder
- Payment status polling API (used by frontend JavaScript)

### Vendor
- Vendor dashboard
- Edit canteen profile
- Toggle canteen open/closed
- Set daily target
- Menu management (CRUD)
- Incoming order management:
  - list orders
  - scan order by code
  - update order status
  - cancel/delete order
  - delete order
- Sales report

### Admin
- Admin dashboard
- Canteen management (CRUD + bulk delete)
- User management (CRUD):
  - bulk delete
  - bulk toggle
  - toggle user per item
  - import users + download template

---

## Tech Stack

**Backend**
- PHP `^8.3`
- Laravel Framework `^13.0`

**Frontend**
- Blade Templates, Tailwind CSS v4, DaisyUI, Alpine.js
- Vite (asset bundling)

**Integrations**
- Midtrans: `midtrans/midtrans-php`
- Image processing: `intervention/image-laravel`
- Email: `resend/resend-laravel`

---

## Requirements

- PHP **8.3+**
- Composer
- Node.js + npm
- Database: MySQL/MariaDB/PostgreSQL/SQLite (depending on your `.env`)

---

## Local Setup

```bash
# 1) install backend deps
composer install

# 2) create env file
cp .env.example .env

# 3) generate app key
php artisan key:generate

# 4) migrate database
php artisan migrate

# 5) install frontend deps
npm install

# 6) build assets
npm run build
```

> Alternatively, you can use the provided composer script:

```bash
composer run setup
```

---

## Environment Configuration (.env)

Minimum configuration:

- `APP_NAME`
- `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

If you are using Midtrans:

- `MIDTRANS_SERVER_KEY`
- `MIDTRANS_CLIENT_KEY`
- `MIDTRANS_IS_PRODUCTION=false`

If you are using Resend:

- `RESEND_API_KEY`

---

## Database Seed / Demo Accounts

The main seeder is: `database/seeders/DatabaseSeeder.php`

Run:

```bash
php artisan db:seed
```

### Demo accounts (from seeder)

> Demo password: `pncpickup123`

- **Admin**
  - NIM: `admin`
  - Email: `admin@pnc.ac.id`

- **Vendor 1**
  - NIM: `vendor_harmoni`
  - Email: `kantinharmoni@pnc.ac.id`

- **Vendor 2**
  - NIM: `vendor_mi`
  - Email: `miacademy@pnc.ac.id`

- **Student**
  - NIM: `240202115`
  - Email: `pratamaputra6854@gmail.com`
  - Note: this user is seeded with `is_first_login=true` so they will be forced to change the password on first login.

Seeder also creates:
- 2 demo canteens
- multiple demo menus
- ~40 historical orders over the last 7 days
- demo reviews for completed orders

---

## Database Structure (Migrations)

This project contains migrations such as:

- `create_users_table`
- `create_canteens_table`
- `create_menus_table`
- `create_orders_table`
- `create_orders_items_table`
- `create_reviews_table`

Plus additional changes:
- add avatar to users
- add payment-related fields to orders
- add menu category
- add performance indexes
- add canteen daily target

---

## Payments (Midtrans)

### Webhook / Notification
Payment notification endpoint:

- `POST /payment/notification`

Notes:
- This endpoint **does not** use auth middleware.
- This endpoint must be excluded from CSRF protection so Midtrans can call it.

### Payment Status Polling (Frontend)
- `GET /api/order/{id}/payment-status`

Used by the frontend to periodically refresh the payment status.

---

## Run the App

### Development

```bash
php artisan serve
```

To run a more complete dev mode (serve + queue listener + extra tooling), use:

```bash
composer run dev
```

---

## Testing

```bash
php artisan test
```

---

## Security Notes

- Never commit `.env`.
- Keep `MIDTRANS_SERVER_KEY` and other secrets in `.env` only.
- Midtrans webhook endpoints should validate signatures/keys per Midtrans best practices.

---

## 🎨 UI/UX Design

The interface was designed in Figma before development, following a design-first workflow. The design system uses a custom **fern green** palette with Poppins typography, documented in `DESIGN.md`.

---

## 📸 Screenshots

> Tip: For long pages, you can use multiple screenshots per role (Student/Vendor/Admin).

### Main UI

<img width="800" alt="main-page" src="https://github.com/user-attachments/assets/4f1f8180-c962-4472-ac7f-78ed208991f1" />

### Main UI Mobile Version

<img width="540" alt="IMG_20260530_192939" src="https://github.com/user-attachments/assets/0186e14e-5eae-4453-8e4a-bc1c334d728b" />


### Browse Page

<img width="800" alt="browse-page" src="https://github.com/user-attachments/assets/04d379f8-bc73-411c-a7e1-c2a21e5985c2" />

### Menu Detail Page

<img width="800" alt="menu-detail-page" src="https://github.com/user-attachments/assets/48df6952-a5ce-4505-84d1-65ef45cf3c72" />

## Vendor Canteen Dashboard

<img width="800" alt="vendor-dashboard" src="https://github.com/user-attachments/assets/3a467efb-f938-4e31-ab98-1fa361633cf6" />

### Admin Dashboard

<img width="800" alt="admin-dashboard" src="https://github.com/user-attachments/assets/95b1b740-6cb6-4b66-8346-d8846c9defeb" />

---

## 👥 Team

| No | Name | NIM |
|----|------|-----|
| 1 | Pratama Putra Purwanto | 240202115 |

**Class:** Teknik Informatika 2D  
**Course:** Framework Programming  
**Institution:** Politeknik Negeri Cilacap

---

## License

This repository currently does not include a `LICENSE` file. If you plan to publish it publicly, consider adding an appropriate license.

---

[![GitHub](https://img.shields.io/badge/GitHub-pratama1246-black?logo=github)](https://github.com/pratama1246)
