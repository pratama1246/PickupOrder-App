# AGENTS.md - Sistem Pickup Order PNC

> Baca file ini sebelum menyentuh satu baris kode pun.
> Ini adalah "briefing hari pertama" untuk AI yang bekerja di project ini.

---

## Project Overview

**Sistem Pickup Order** adalah platform pemesanan makanan kantin kampus untuk
Politeknik Negeri Cilacap (PNC). Mahasiswa bisa order makanan dari kantin tanpa
harus antri langsung. Ada tiga role utama: **mahasiswa**, **vendor (pemilik kantin)**,
dan **admin**.

- Stack: **Laravel + Blade**, **Tailwind CSS v4**, **DaisyUI**, **Alpine.js**, **MySQL**
- Frontend: Blade components, bukan React/Vue
- CSS utility: Tailwind v4 dengan custom theme di `app.css`, DaisyUI untuk base components
- Font: Poppins (semua weight, via Google Fonts)

---

## Tech Stack & Conventions

### Laravel
- Gunakan **Blade components** untuk UI yang reusable (`resources/views/components/`)
- Layout utama: `resources/views/layouts/app.blade.php`
- Halaman user ada di: `resources/views/user/`
- Penamaan file: `kebab-case.blade.php`
- Gunakan `@props`, `$attributes->merge()`, dan `@php` block di dalam komponen

### Tailwind CSS v4
- Custom color tokens didefinisikan di `resources/css/app.css` dalam `@theme {}`
- Gunakan custom color class (contoh: `fern-700`, `vanilla-custard-50`) BUKAN warna Tailwind default untuk UI utama
- Tailwind v4 menggunakan `@import 'tailwindcss'` bukan `@tailwind base/components/utilities`
- DaisyUI diimport via `@plugin "daisyui/index.js"`

### Responsive Pattern
- Mobile-first, breakpoint utama: `sm:` (640px), `md:` (768px), `lg:` (1024px)
- Page padding standar: `px-4 sm:px-10 md:px-16 lg:px-24`
- Dock (bottom nav) muncul di mobile: `lg:hidden`
- Navbar muncul di semua ukuran, item nav center hanya di `lg:flex`

---

## Folder Structure

```
resources/
├── css/
│   └── app.css                  # Tailwind config + custom color tokens
├── js/
│   └── app.js
└── views/
    ├── layouts/
    │   └── app.blade.php        # Layout utama (navbar + dock + footer)
    ├── components/
    │   ├── navbar.blade.php     # Global
    │   ├── footer.blade.php     # Global
    │   ├── dock.blade.php       # Global
    │   ├── breadcrumb.blade.php # Global
    │   ├── status-badge.blade.php # Global
    │   ├── foodcard.blade.php   # Global
    │   ├── canteencard.blade.php # Global
    │   └── user/                # Komponen khusus Mahasiswa/User
    │       ├── cart-item.blade.php
    │       ├── cart-summary.blade.php
    │       ├── order-item.blade.php
    │       ├── quantity-control.blade.php
    │       └── info-bar.blade.php
    └── user/
        ├── index.blade.php      # Halaman beranda
        ├── browse.blade.php     # Browse kantin & semua menu
        ├── canteen.blade.php    # Detail satu kantin + menu-nya
        ├── history.blade.php    # Riwayat pesanan user
        └── order-detail.blade.php # Detail satu pesanan
```
---

## Controller Conventions

- Gunakan Eloquent Model, bukan `DB::table()` langsung
- Spasi konsisten di array: `['key' => $value]` bukan `['key'=>$value]`
- Validation wajib sebelum insert/update: `$request->validate([...])`
- Redirect setelah store/update pakai named route, bukan hardcode string URL
- Penamaan method ikuti Laravel resource convention:
  `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`


---

## Component Conventions

### Cara membuat komponen baru
1. Buat file di `resources/views/components/nama-komponen.blade.php` atau `resources/views/components/{role}/nama-komponen.blade.php`.
2. Gunakan `@props([])` untuk mendefinisikan props dengan default value.
3. Gunakan `$attributes->merge(['class' => '...'])` untuk kelas yang bisa di-override.
4. Panggil dengan `<x-nama-komponen />` untuk komponen global, atau `<x-{role}.nama-komponen />` untuk komponen khusus role (dot notation). Contoh: `<x-user.cart-item />`.

### Props pattern yang sudah ada
```blade
{{-- Contoh dari order-item --}}
@props([
    'image' => '',
    'name' => '',
    'description' => null,
    'price' => '',
    'quantity' => 1,
    'variant' => 'card' // 'card' atau 'list'
])
```

### Variant pattern
Gunakan prop `variant` kalau komponen punya dua tampilan berbeda
(contoh: `order-item` punya `card` dan `list`).

---

## Naming Conventions

| Hal | Konvensi | Contoh |
|---|---|---|
| Blade component | kebab-case | `order-item.blade.php` |
| CSS class custom | kebab-case | `fern-700`, `vanilla-custard-50` |
| Route | slash + kebab | `/browse`, `/history` |
| Asset path | `asset()` helper | `asset('assets/food/nama.jpg')` |
| Image fallback | `onerror` attribute | `onerror="this.src='https://ui-avatars.com/...'"`|

---

## Do's and Don'ts

### DO
- Selalu gunakan custom color token (`fern-700`, `vanilla-custard-50`, dll) untuk elemen UI utama
- Gunakan `shadow-sm` atau `shadow-lg` di card dan button, bukan tanpa shadow
- Gunakan `font-bold` atau `font-extrabold` untuk heading, `font-medium` untuk body text
- Gunakan `rounded-3xl` untuk card utama, `rounded-2xl` untuk inner card, `rounded-md`/`rounded-2xl` untuk button
- Selalu tambahkan `hover:` state pada button dan link
- Gunakan `transition-colors` atau `active:scale-95` untuk interactive feedback

### DON'T
- Jangan hardcode warna hex langsung di class (`text-[#123456]`) kecuali tidak ada token yang sesuai
- Jangan pakai `text-green-500` atau Tailwind default green — pakai `fern-*` atau `emerald-*` custom
- Jangan buat halaman baru tanpa extend `layouts.app`
- Jangan tambahkan `<style>` tag inline kecuali sangat terpaksa
- Jangan pakai em dash (—) di dalam kode atau komentar
- Jangan gunakan emoji sebagai ikon, setidaknya gunakan icon svg

---

## Active Routes (User Side)

| Route | View | Deskripsi |
|---|---|---|
| `/` | `user/index` | Beranda: hero, menu populer, pilih kantin |
| `/browse` | `user/browse` | Browse semua kantin & menu |
| `/canteen/{id}` | `user/canteen` | Detail kantin + filter menu |
| `/history` | `user/history` | Daftar riwayat pesanan |
| `/history/{id}` | `user/order-detail` | Detail satu pesanan |

---

## Status Badge Values

Komponen `<x-status-badge>` menerima prop `status` dengan nilai:
`Diproses` | `Selesai` | `Dibatalkan` | `Menunggu`
