# 🛒 PickupOrder-App (PNC)

**PickupOrder-App** is a **Laravel 13 (PHP 8.3+)** web application for **canteen/food pickup ordering**, designed for internal use at **Politeknik Negeri Cilacap**.

It supports 3 main roles:

- **Student (User / Mahasiswa)**: browse canteens & menus, add items to cart, checkout, pay, view order history, review menu items, and reorder.
- **Vendor**: manage canteen profile, manage menus, process incoming orders (including QR/code scanning), and view sales reports.
- **Admin**: manage canteens and users (including bulk actions and user import).

> Payments are integrated via **Midtrans** (Snap/notification) and the app exposes a webhook endpoint to receive payment notifications.
> Built as a college project at Politeknik Negeri Cilacap, Informatics Engineering Department.

---

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![DaisyUI](https://img.shields.io/badge/DaisyUI-v5-5A0EF8?style=for-the-badge&logo=daisyui&logoColor=white)](https://daisyui.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=black)](https://alpinejs.dev)
[![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![Figma](https://img.shields.io/badge/Figma-F24E1E?style=for-the-badge&logo=figma&logoColor=white)](https://figma.com)

---

## Table of Contents

- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Local Setup](#local-setup)
- [Environment Configuration (.env)](#environment-configuration-env)
- [Database Seed / Demo Accounts](#database-seed--demo-accounts)
- [Database Structure (Migrations)](#database-structure-migrations)
- [Payments (Midtrans)](#payments-midtrans)
- [Testing Midtrans Webhook with Ngrok](#testing-midtrans-webhook-with-ngrok)
- [Run the App](#run-the-app)
- [Queue Worker](#queue-worker)
- [Testing](#testing)
- [Security Notes](#security-notes)
- [UI/UX Design](#uiux-design)
- [Screenshots](#screenshots)
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
- Checkout with 3 payment methods:
  * **Midtrans** (online payment via Snap, automatic verification)
  * **QRIS Manual** (scan vendor's QR code, upload payment proof, manual verification by vendor)
  * **Pay Direct** (cash on pickup, confirmed by vendor)
- Order history:
  * view details
  * cancel/delete (including cancel by payment group)
  * review menu items
  * reorder
- Payment status polling API (used by frontend JavaScript)

### Vendor

- Vendor dashboard
- Edit canteen profile
- Toggle canteen open/closed
- Set daily target
- Menu management (CRUD)
- Incoming order management:
  * list orders
  * scan order by code
  * update order status
  * cancel/delete order
  * delete order
- Sales report

### Admin

- Admin dashboard
- Canteen management (CRUD + bulk delete)
- User management (CRUD):
  * bulk delete
  * bulk toggle
  * toggle user per item
  * import users + download template

---

## Tech Stack

**Backend**

- PHP `^8.3`
- Laravel Framework `^13.0`
- Midtrans PHP SDK: `midtrans/midtrans-php`
- Image processing: `intervention/image-laravel`
- Email: `resend/resend-laravel`

**Frontend**

- Blade Templates
- Tailwind CSS v4 + DaisyUI v5
- Alpine.js
- ApexCharts
- Vite (asset bundling)

---

## Project Structure

A breakdown of the project directories, files, and modules:

```
pickuporder-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                # Controllers for Admin Panel (Canteen, User, Dashboard)
│   │   │   ├── User/                 # Controllers for Student/User features (Canteen, Cart, Checkout, Order, Payment)
│   │   │   ├── Vendor/               # Controllers for Vendor Canteen Panel (Canteen, Dashboard, Menu, Order, Report)
│   │   │   ├── AuthController.php    # Handles Login, Logout, and Password Reset Flow
│   │   │   ├── HomeController.php    # Landing Page Controller
│   │   │   └── ProfileController.php # User Profile Updates
│   │   └── Middleware/
│   │       ├── CheckOnlineOrderHours.php # Restricts orders to active operational hours
│   │       ├── CheckRole.php         # Custom role-based routing (Admin, Vendor, Student)
│   │       ├── SecurityHeadersMiddleware.php # Sets secure HTTP response headers
│   │       └── SyncCartSession.php   # Syncs guest session cart to database on login
│   └── Models/
│       ├── Canteen.php               # Canteen Model (stores profile, location, daily targets)
│       ├── CartItem.php              # Persistent User Shopping Cart items
│       ├── Menu.php                  # Food and beverage items linked to Canteen
│       ├── Order.php                 # Orders header (tracks price, method, queue status, qr/proof)
│       ├── OrderItem.php             # Pivot table/line items for individual order components
│       ├── Review.php                # User ratings and comments for orders
│       └── User.php                  # Primary User representation (with roles: Admin, Vendor, Student)
├── config/                           # Standard Laravel configuration files
├── database/
│   ├── factories/                    # Model factories for testing and seeding
│   ├── migrations/                   # Database schema definition files
│   └── seeders/                      # Seeders to populate dummy databases & test accounts
├── public/                           # Compiled CSS/JS assets, uploaded files, images, icons
├── resources/
│   ├── css/
│   │   └── app.css                   # Custom Tailwind CSS v4 entrypoint + color/design tokens
│   ├── js/
│   │   └── app.js                    # Main client-side script entrypoint
│   └── views/
│       ├── admin/                    # Admin Dashboard and management pages
│       ├── auth/                     # Authentication pages (login, first-login password change)
│       ├── components/               # Reusable Blade components
│       │   ├── admin/                # Admin sidebar and specific UI helpers
│       │   ├── user/                 # User cart cards, summaries, and order controls
│       │   ├── vendor/               # Vendor dashboard-specific layout parts
│       │   ├── breadcrumb.blade.php  # Global navigation breadcrumbs
│       │   ├── foodcard.blade.php    # Display menu cards with order CTAs
│       │   ├── status-badge.blade.php# Maps order status to semantic status colors
│       │   └── ...                   # Other base layout blocks (Navbar, Dock, Footer, Toast)
│       ├── errors/                   # Custom HTTP Error boundary screens (403, 404, etc.)
│       ├── layouts/                  # Base document skeletons (app, admin, vendor, auth)
│       ├── user/                     # Student frontend templates (Index, Browse, History, Order Detail)
│       ├── vendor/                   # Vendor panel views (canteen edit, dashboard, menu control, reports)
│       └── profile.blade.php         # User profile configuration screen
├── routes/
│   ├── web.php                       # Application routes grouped by roles (Admin, Vendor, User, Auth)
│   └── console.php                   # CLI routes and tasks
├── bootstrap/                        # Application bootstrap, routing, and middleware registration
├── vite.config.js                    # Vite packaging setup for building assets
└── package.json                      # Frontend dependency configuration (TailwindCSS v4, DaisyUI v5, AlpineJS)
```

---

## Requirements

- PHP **8.3+**
- Composer
- Node.js + npm
- MySQL / MariaDB
- Queue worker (for background jobs)

---

## Local Setup

```bash
# 1) Clone repository
git clone https://github.com/pratama1246/PickupOrder-App.git
cd PickupOrder-App

# 2) Install backend dependencies
composer install

# 3) Create environment file
cp .env.example .env

# 4) Generate app key
php artisan key:generate

# 5) Configure your .env (DB, Midtrans, Resend, etc.)

# 6) Run migrations
php artisan migrate

# 7) Seed demo data (optional)
php artisan db:seed

# 8) Create storage symlink (required for image uploads)
php artisan storage:link

# 9) Install frontend dependencies
npm install

# 10) Build frontend assets
npm run build
```

> Alternatively, you can use the provided composer script (steps 2, 3, 4, 6, 9, 10 in one command):

```bash
composer run setup
```

> Note: `composer run setup` does not run `php artisan storage:link` or `php artisan db:seed`. Run those manually if needed.

---

## Environment Configuration (.env)

### Application

```env
APP_NAME=PickupOrder-App
APP_ENV=local
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
```

### Order Schedule (Jam Operasional)

The app enforces ordering hours. Orders outside this window will be rejected.

```env
ORDER_START_TIME="07:30"
ORDER_END_TIME="15:30"
ORDER_ACTIVE_DAYS="Monday,Tuesday,Wednesday,Thursday,Friday"
```

### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pickuporder_app
DB_USERNAME=root
DB_PASSWORD=
```

### Midtrans Payment

```env
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false
```

> Get your keys from the [Midtrans Dashboard](https://dashboard.sandbox.midtrans.com). Set `MIDTRANS_IS_PRODUCTION=true` when deploying to production. The frontend automatically loads the sandbox or production script depending on this setting.

### Email (Resend)

```env
RESEND_API_KEY=your-resend-api-key
RESEND_AUDIENCE_ID=your-audience-id
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> **Note:** The email integration is currently configured as a boilerplate setup using `resend/resend-laravel`, but the actual transactional email notifications are not yet implemented in the backend application.

### Queue & Session

```env
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

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
  * NIM: `admin`
  * Email: `admin@pnc.ac.id`

- **Vendor 1**
  * NIM: `vendor_harmoni`
  * Email: `kantinharmoni@pnc.ac.id`

- **Vendor 2**
  * NIM: `vendor_mi`
  * Email: `miacademy@pnc.ac.id`

- **Student**
  * NIM: `demo_student`
  * Email: `demo.student@pnc.ac.id`
  * Note: this user is seeded with `is_first_login=true` so they will be forced to change the password on first login.

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
- `create_cart_items_table` (to support database-persistent cart items)

Plus additional changes:

- add avatar to users
- add payment-related fields to orders
- add menu category
- add performance indexes
- add canteen daily target
- drop unique order code constraint from orders
- add manual QRIS and payment proof columns

---

## Payments

This app supports **3 payment methods**:

| Method | Description | Verification |
|---|---|---|
| **Midtrans (Snap)** | Online payment via Midtrans gateway | Automatic via webhook |
| **QRIS Manual** | Student scans vendor's QRIS code, uploads payment proof | Manual by vendor |
| **Pay Direct** | Student pays cash directly at the canteen | Manual by vendor |

### Midtrans Webhook / Notification

Payment notification endpoint:

- `POST /payment/notification`

Notes:

- This endpoint **does not** use auth middleware.
- This endpoint must be excluded from CSRF protection so Midtrans can call it.

### Payment Status Polling (Frontend)

- `GET /api/order/{id}/payment-status`

Used by the frontend to periodically refresh the payment status.

---

## Testing Midtrans Webhook with Ngrok

Since Midtrans needs a **publicly accessible URL** to send payment notifications, you need to expose your local server using [ngrok](https://ngrok.com) during development.

### 1. Install ngrok

```bash
# macOS (Homebrew)
brew install ngrok

# Windows (Chocolatey)
choco install ngrok

# Or download directly from https://ngrok.com/download
```

### 2. Start your Laravel app

```bash
composer run dev
# or
php artisan serve --port=8000
```

### 3. Expose local server with ngrok

```bash
ngrok http 8000
```

You will get a public URL like:

```
Forwarding   https://abc123.ngrok-free.app -> http://localhost:8000
```

### 4. Set notification URL in Midtrans Dashboard

Go to [Midtrans Sandbox Dashboard](https://dashboard.sandbox.midtrans.com):

```
Settings > Configuration > Payment Notification URL
```

Fill in:

```
https://abc123.ngrok-free.app/payment/notification
```

### 5. Update your `.env`

```env
APP_URL=https://abc123.ngrok-free.app
```

> Remember to update the ngrok URL every time you restart ngrok, as the URL changes each session (unless you use a paid ngrok plan with a fixed domain).

---

## Run the App

### Development

```bash
php artisan serve
```

To run a full dev mode (server + queue listener + logs + vite), use:

```bash
composer run dev
```

### Production Build

```bash
npm run build
```

---

## Queue Worker

This app uses database queues. Make sure to run the queue worker so background jobs are processed.

**Development:**

```bash
php artisan queue:listen --tries=1
```

**Production:**

```bash
php artisan queue:work --daemon
```

> For production, it is recommended to use a process manager like **Supervisor** to keep the queue worker running persistently.

---

## Testing

```bash
php artisan test
```

---

## Security Notes

- Never commit `.env` to version control.
- Keep `MIDTRANS_SERVER_KEY` and all secrets inside `.env` only.
- Midtrans webhook endpoint must validate the notification signature per [Midtrans best practices](https://docs.midtrans.com/docs/verifying-data-authenticity).
- Always set `MIDTRANS_IS_PRODUCTION=false` in development to avoid real transactions.
- When going live, update `APP_URL` to your real domain and set `MIDTRANS_IS_PRODUCTION=true`.

---

## 🎨 UI/UX Design

The interface was designed in Figma before development, following a design-first workflow. The design system uses a custom **fern green** palette with Poppins typography, documented in `DESIGN.md`.

---

## 📸 Screenshots

### Main UI

<img width="800" alt="main-page" src="https://github.com/user-attachments/assets/4f1f8180-c962-4472-ac7f-78ed208991f1" />

### Main UI Mobile Version

<img width="320" alt="IMG_20260530_192939" src="https://github.com/user-attachments/assets/0186e14e-5eae-4453-8e4a-bc1c334d728b" />

### Browse Page

<img width="800" alt="browse-page" src="https://github.com/user-attachments/assets/04d379f8-bc73-411c-a7e1-c2a21e5985c2" />

### Menu Detail Page

<img width="800" alt="menu-detail-page" src="https://github.com/user-attachments/assets/48df6952-a5ce-4505-84d1-65ef45cf3c72" />

### Vendor Canteen Dashboard

<img width="800" alt="vendor-dashboard" src="https://github.com/user-attachments/assets/3a467efb-f938-4e31-ab98-1fa361633cf6" />

### Admin Dashboard

<img width="800" alt="admin-dashboard" src="https://github.com/user-attachments/assets/95b1b740-6cb6-4b66-8346-d8846c9defeb" />

---

## 👥 Team

Built as a college project at Politeknik Negeri Cilacap, Informatics Engineering Department.

**Class:** Teknik Informatika 2D
**Course:** Framework Programming
**Institution:** Politeknik Negeri Cilacap

---

## License

This repository currently does not include a `LICENSE` file. If you plan to publish it publicly, consider adding an appropriate license.

---

[![GitHub](https://img.shields.io/badge/GitHub-pratama1246-black?logo=github)](https://github.com/pratama1246)
