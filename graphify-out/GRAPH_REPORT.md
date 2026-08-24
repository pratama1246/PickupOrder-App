# Graph Report - .  (2026-08-09)

## Corpus Check
- 147 files · ~73,851 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 554 nodes · 891 edges · 113 communities (109 shown, 4 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 66 edges (avg confidence: 0.82)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Canteen CRUD & Model
- Controllers & Auth Flow
- Composer Dependencies
- Admin CRUD Controllers
- App Architecture & Conventions
- Checkout & Order Flow
- Order Scheduling Rules
- Frontend JS Packages
- Composer Scripts
- User Cart & Canteen Browsing
- User Factory & Eloquent Base
- Service Provider Bootstrap
- Hero Illustration Asset
- Admin/Vendor Dashboard Layout
- App JS Utilities
- PNC Campus Branding
- Docs: User-Side Routes (/browse, /canteen/{id}, /history)
- Docs: Alpine.js
- Asset: Robots.txt Allow All Crawling

## God Nodes (most connected - your core abstractions)
1. `Menu` - 44 edges
2. `User` - 44 edges
3. `Order` - 43 edges
4. `Canteen` - 36 edges
5. `Controller` - 34 edges
6. `StockManagementTest` - 15 edges
7. `UserController` - 14 edges
8. `OrderItem` - 13 edges
9. `PublicAccessTest` - 13 edges
10. `OrderHelper` - 12 edges

## Surprising Connections (you probably didn't know these)
- `Design-First Workflow (Figma before development)` --conceptually_related_to--> `Design System Token Model (colors, typography, rounded, spacing)`  [INFERRED]
  README.md → docs/DESIGN.md
- `StockManagementTest` --references--> `Menu`  [EXTRACTED]
  tests/Feature/StockManagementTest.php → app/Models/Menu.php
- `PickupOrder-App (Laravel 13 + PHP 8.3)` --conceptually_related_to--> `Sistem Pickup Order PNC (Platform)`  [INFERRED]
  README.md → AGENTS.md
- `Three Roles Model (Student, Vendor, Admin)` --conceptually_related_to--> `Mahasiswa (Student) Role`  [INFERRED]
  README.md → AGENTS.md
- `Three Roles Model (Student, Vendor, Admin)` --conceptually_related_to--> `Vendor (Pemilik Kantin) Role`  [INFERRED]
  README.md → AGENTS.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Three Payment Methods + Verification Subsystem** — readme_midtrans_payment, readme_qris_manual, readme_pay_direct, readme_payment_verification_flow, readme_midtrans_webhook [EXTRACTED 1.00]
- **Three-Role System (Mahasiswa, Vendor, Admin)** — agents_sistem_pickup_order, agents_mahasiswa_role, agents_vendor_role, agents_admin_role, readme_three_roles [INFERRED 0.85]
- **Shared Design System Tokens Across Docs** — docs_design_design_system, docs_design_color_tokens, docs_design_poppins_typography, docs_design_status_badge_token, agents_custom_color_tokens [INFERRED 0.85]

## Communities (113 total, 4 thin omitted)

### Community 0 - "Canteen CRUD & Model"
Cohesion: 0.05
Nodes (15): Canteen, User, DatabaseSeeder, HasManyThrough, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Database\Eloquent\Relations\HasOne, Illuminate\Database\Seeder (+7 more)

### Community 1 - "Controllers & Auth Flow"
Cohesion: 0.08
Nodes (16): DashboardController, Controller, ProfileController, PaymentCallbackController, ReviewController, CanteenController, DashboardController, ReportController (+8 more)

### Community 2 - "Composer Dependencies"
Cohesion: 0.05
Nodes (43): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+35 more)

### Community 3 - "Admin CRUD Controllers"
Cohesion: 0.09
Nodes (8): CanteenController, UserController, AuthController, HomeController, MenuController, Illuminate\Http\RedirectResponse, Illuminate\View\View, Symfony\Component\HttpFoundation\StreamedResponse

### Community 4 - "App Architecture & Conventions"
Cohesion: 0.06
Nodes (37): Admin Role, App Layout (navbar + dock + footer), Blade Component Convention (@props, variant, dot notation), Custom Color Tokens (fern, vanilla-custard, shadow-grey), DaisyUI (Base Components), Dock Bottom Navigation (mobile), Laravel + Blade (Frontend & Backend), Mahasiswa (Student) Role (+29 more)

### Community 5 - "Checkout & Order Flow"
Cohesion: 0.09
Nodes (6): CheckoutController, Carbon, OrderController, OrderController, Order, Carbon\Carbon

### Community 6 - "Order Scheduling Rules"
Cohesion: 0.11
Nodes (9): OrderHelper, Carbon, CheckOnlineOrderHours, CheckRole, SecurityHeadersMiddleware, SyncCartSession, Closure, Symfony\Component\HttpFoundation\Response (+1 more)

### Community 7 - "Frontend JS Packages"
Cohesion: 0.07
Nodes (26): alpinejs, apexcharts, axios, concurrently, daisyui, laravel-vite-plugin, dependencies, alpinejs (+18 more)

### Community 8 - "Composer Scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "User Cart & Canteen Browsing"
Cohesion: 0.12
Nodes (4): CanteenController, CartController, MenuController, Menu

### Community 10 - "User Factory & Eloquent Base"
Cohesion: 0.38
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 12 - "Hero Illustration Asset"
Cohesion: 0.60
Nodes (5): Eating Healthy Food Cuate Illustration, Cuate Flat Illustration Style, Healthy Eating Concept, Landing Page Hero Visual, Mint Green Brand Palette

### Community 13 - "Admin/Vendor Dashboard Layout"
Cohesion: 0.67
Nodes (4): Admin Dashboard (canteens, users), Stat Card Shared Component (vendor & admin), Sidebar Layouts for Vendor & Admin, Vendor Dashboard (orders, transactions, menus)

### Community 14 - "App JS Utilities"
Cohesion: 1.00
Nodes (3): fetchData(), init(), triggerSearch()

### Community 32 - "PNC Campus Branding"
Cohesion: 1.00
Nodes (3): Campus Identity Asset, Logo PNC, Politeknik Negeri Cilacap (PNC)

## Knowledge Gaps
- **78 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+73 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **4 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `Canteen CRUD & Model` to `Controllers & Auth Flow`, `User Factory & Eloquent Base`, `Admin CRUD Controllers`?**
  _High betweenness centrality (0.040) - this node is a cross-community bridge._
- **Why does `Order` connect `Checkout & Order Flow` to `Canteen CRUD & Model`, `Controllers & Auth Flow`, `User Factory & Eloquent Base`, `User Cart & Canteen Browsing`?**
  _High betweenness centrality (0.039) - this node is a cross-community bridge._
- **Why does `Menu` connect `User Cart & Canteen Browsing` to `Canteen CRUD & Model`, `Controllers & Auth Flow`, `Admin CRUD Controllers`, `Checkout & Order Flow`, `Order Scheduling Rules`?**
  _High betweenness centrality (0.035) - this node is a cross-community bridge._
- **Are the 25 inferred relationships involving `Order` (e.g. with `.index()` and `.reorder()`) actually correct?**
  _`Order` has 25 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _78 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Canteen CRUD & Model` be split into smaller, more focused modules?**
  _Cohesion score 0.053410893707033315 - nodes in this community are weakly interconnected._
- **Should `Controllers & Auth Flow` be split into smaller, more focused modules?**
  _Cohesion score 0.07773664727657324 - nodes in this community are weakly interconnected._