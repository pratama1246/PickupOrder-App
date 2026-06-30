---
version: alpha
name: Sistem Pickup Order PNC
description: Design system untuk platform pemesanan makanan kantin Politeknik Negeri Cilacap

colors:
  # Primary - Main brand color (hijau fern)
  primary:          "#306939"   # fern-700
  primary-hover:    "#204626"   # fern-800
  primary-light:    "#dcefdf"   # fern-100
  primary-subtle:   "#eef7ef"   # fern-50

  # Accent - Highlight & hero element
  accent:           "#76bc84"   # emerald-400 custom

  # Surface - Card backgrounds
  surface-warm:     "#f6f8ec"   # vanilla-custard-50 (card riwayat, bg highlight)
  surface-base:     "#ffffff"   # inner card, form bg
  surface-muted:    "#ddeee0"   # emerald-100 custom (badge bg)

  # Dark - Navbar background
  navbar-bg:        "#131720"   # shadow-grey-900

  # Text
  text-primary:     "#1f2937"   # base-content (DaisyUI)
  text-muted:       "rgba(var(--bc)/0.6)"  # base-content/60
  text-subtle:      "rgba(var(--bc)/0.5)"  # base-content/50

  # Status colors
  status-process-bg:   "#ffbd59"
  status-process-text: "#000000"
  status-done-bg:      "#bbddc2"   # emerald-200 custom
  status-done-text:    "#112214"   # emerald-900 custom
  status-cancel-bg:    "#fecaca"   # red-200
  status-cancel-text:  "#7f1d1d"   # red-900
  status-wait-bg:      "#bfdbfe"   # blue-200
  status-wait-text:    "#1e3a5f"   # blue-900

typography:
  font-family: "'Poppins', ui-sans-serif, system-ui, sans-serif"

  # Heading levels
  h1-page:
    fontSize: "2.25rem"    # text-4xl
    fontWeight: "800"      # extrabold
    lineHeight: "tight"
  h1-page-sm:
    fontSize: "1.5rem"     # text-2xl
    fontWeight: "700"

  h2-section:
    fontSize: "1.5rem"     # text-2xl
    fontWeight: "700"

  card-title:
    fontSize: "1.25rem"    # text-xl
    fontWeight: "700"

  body:
    fontSize: "1rem"       # text-base
    fontWeight: "500"      # medium

  body-sm:
    fontSize: "0.875rem"   # text-sm
    fontWeight: "500"

  label:
    fontSize: "0.75rem"    # text-xs
    fontWeight: "700"
    textTransform: "uppercase"

rounded:
  button-pill:   "9999px"   # rounded-full (filter, load more)
  button-soft:   "1rem"     # rounded-2xl (CTA button)
  button-default: "0.375rem" # rounded-md (action button di card)
  card-outer:    "1.5rem"   # rounded-3xl
  card-inner:    "1rem"     # rounded-2xl
  image-card:    "1rem"     # rounded-2xl (gambar di kantin header)
  badge:         "0.375rem" # rounded-md (status badge)
  tag:           "0.5rem"   # rounded-lg (rating, info chip)
  input:         "9999px"   # rounded-full (search bar, select filter)

spacing:
  page-x-mobile:  "1rem"    # px-4
  page-x-sm:      "2.5rem"  # px-10
  page-x-md:      "4rem"    # px-16
  page-x-lg:      "6rem"    # px-24
  section-y:      "2.5rem"  # py-10
  card-padding:   "2rem"    # p-8 (desktop)
  card-padding-sm: "1rem"   # p-4 (mobile)

components:
  # --- BUTTON PRIMARY ---
  button-primary:
    backgroundColor: "{colors.primary}"
    backgroundColorHover: "{colors.primary-hover}"
    textColor: "#ffffff"
    borderRadius: "{rounded.button-soft}"
    fontWeight: "700"
    shadow: "shadow-lg"
    note: "Gunakan untuk CTA utama. HANYA satu per section."

  # --- BUTTON SECONDARY ---
  button-secondary:
    backgroundColor: "bg-base-300"
    backgroundColorHover: "bg-base-400"
    textColor: "text-base-content"
    borderRadius: "{rounded.button-default}"
    fontWeight: "700"
    note: "Untuk aksi sekunder seperti tombol Detail."

  # --- BUTTON GHOST / LOAD MORE ---
  button-ghost:
    backgroundColor: "#d9d9d9"
    backgroundColorHover: "#9ca3af"
    textColor: "#000000"
    borderRadius: "{rounded.button-pill}"
    fontWeight: "700"
    note: "Hanya untuk Load More atau aksi non-destructive netral."

  # --- CARD KANTIN ---
  card-canteen:
    backgroundColor: "bg-base-100"
    borderRadius: "{rounded.card-outer}"
    shadow: "shadow-sm"
    padding: "{spacing.card-padding-sm} desktop: {spacing.card-padding}"
    layout: "flex-col md:flex-row"
    note: "Card untuk list kantin di halaman pesanan dan beranda."

  # --- CARD MAKANAN ---
  card-food:
    backgroundColor: "bg-base-100"
    borderRadius: "{rounded.card-outer}"
    shadow: "shadow-sm"
    imageAspect: "square, object-cover"
    note: "Grid 1-2-3-4 kolom tergantung breakpoint."

  # --- CARD RIWAYAT ---
  card-history:
    backgroundColor: "{colors.surface-warm}"
    border: "1px solid rgba(var(--bc)/0.3)"
    borderRadius: "{rounded.card-outer}"
    padding: "p-4 sm:p-10"
    shadow: "shadow-sm"
    note: "Wrapper card untuk riwayat pesanan. Selalu pakai vanilla-custard-50."

  # --- INNER CARD (di dalam card-history) ---
  card-inner:
    backgroundColor: "{colors.surface-base}"
    border: "1px solid rgba(var(--bc)/0.3)"
    borderRadius: "rounded-2xl sm:rounded-3xl"
    padding: "p-4 sm:p-6"
    note: "Inner card selalu bg-white dengan border tipis."

  # --- INFO BOX (di order-detail) ---
  info-box:
    backgroundColor: "{colors.surface-base}"
    border: "1px solid rgba(var(--bc)/0.1)"
    borderRadius: "{rounded.card-inner}"
    padding: "p-4"
    shadow: "shadow-sm"
    labelStyle: "text-xs uppercase font-bold text-base-content/50"
    valueStyle: "font-bold text-base-content text-sm"

  # --- STATUS BADGE ---
  status-badge:
    borderRadius: "{rounded.badge}"
    fontWeight: "700"
    padding: "px-3 sm:px-6 py-1.5 sm:py-2.5"
    fontSize: "text-xs sm:text-sm"
    shadow: "shadow-sm"
    values:
      Diproses: "bg-[#ffbd59] text-black"
      Selesai:  "bg-emerald-200 text-emerald-900"
      Dibatalkan: "bg-red-200 text-red-900"
      Menunggu: "bg-blue-200 text-blue-900"

  # --- NAVBAR ---
  navbar:
    backgroundColor: "{colors.navbar-bg}"
    height: "h-20"
    position: "sticky top-0 z-50"
    logoStyle: "bg-fern-700 text-white font-bold px-3 py-1 rounded-lg"
    activeLink: "bg-fern-50 text-fern-700 font-medium rounded-lg"
    inactiveLink: "text-white font-medium rounded-lg"

  # --- SEARCH BAR ---
  search-input:
    borderRadius: "{rounded.input}"
    border: "input-bordered border-base-content/40"
    focusBorder: "focus-within:border-base-content"
    iconColor: "text-base-content/50"
    note: "Selalu rounded-full. Jangan pakai rounded-lg untuk search."

  # --- BREADCRUMB ---
  breadcrumb:
    fontSize: "text-xs sm:text-sm"
    fontWeight: "font-bold"
    activeColor: "text-base-content"
    inactiveColor: "text-base-content/50"
    separator: "» (karakter, bukan icon SVG)"

  # --- RATING CHIP ---
  rating-chip:
    backgroundColor: "bg-base-200"
    borderRadius: "{rounded.tag}"
    padding: "px-2 sm:px-3 py-1"
    content: "⭐ + angka font-semibold"

  # --- ORDER ITEM (list variant) ---
  order-item-list:
    wrapper: "border-b border-base-content/10 last:border-0 py-3"
    imageSize: "w-12 h-12 sm:w-16 sm:h-16 rounded-lg sm:rounded-xl"
    titleSize: "text-base sm:text-lg font-bold"
    priceSize: "text-base sm:text-lg font-bold text-right"

  # --- ORDER ITEM (card variant) ---
  order-item-card:
    wrapper: "border border-base-content/30 rounded-xl p-3 mb-3 sm:mb-4"
    imageSize: "w-12 h-12 sm:w-20 sm:h-20 rounded-lg sm:rounded-xl"
    titleSize: "text-base sm:text-xl font-bold"
    priceSize: "text-base sm:text-xl font-bold text-right"
---

## Overview

Sistem Pickup Order PNC mengusung nuansa **hijau alami dan hangat** yang mencerminkan
suasana kantin kampus yang ramah, efisien, dan terpercaya. UI dirancang untuk dua
konteks utama: mahasiswa yang lapar dengan waktu terbatas, dan vendor kantin yang
butuh informasi pesanan yang jelas.

Kepribadian visual: **functional-friendly** — tidak terlalu playful, tidak terlalu korporat.
Desain harus terasa seperti aplikasi yang dibuat orang PNC untuk orang PNC.

---

## Colors

Palet utama berbasis **hijau fern** sebagai primary, dengan **vanilla custard** sebagai
warm surface untuk card riwayat/highlight. Navbar menggunakan warna gelap `shadow-grey-900`
untuk kontras maksimal.

Penggunaan:
- `fern-700` hanya untuk: button primary, active nav link, badge "BUKA", icon accent
- `vanilla-custard-50` hanya untuk: background card riwayat, highlight section
- `emerald-400` (custom) hanya untuk: hero text highlight, aksen dekoratif
- Jangan campur `fern-*` dengan `green-*` bawaan Tailwind

---

## Typography

Satu font family saja: **Poppins**. Tidak ada kombinasi typeface.

Hierarki weight:
- `font-extrabold` (800): page heading utama
- `font-bold` (700): card title, button, label, section heading
- `font-semibold` (600): rating, number emphasis
- `font-medium` (500): body text, nav link default, placeholder

---

## Spacing & Layout

Grid system:
- Food/menu grid: `grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4`
- Canteen grid: `grid-cols-1 lg:grid-cols-2`
- Info box grid: `grid-cols-1 sm:grid-cols-2`
- Max width container: `max-w-8xl mx-auto`
- Order detail max width: `max-w-4xl`

Spacing page standar selalu: `px-4 sm:px-10 md:px-16 lg:px-24`

---

## Components

Lihat token `components` di YAML frontmatter untuk nilai detail.
Urutan prioritas ketika memilih komponen:

1. Cek apakah komponen sudah ada di `resources/views/components/`
2. Kalau mirip dengan yang sudah ada, gunakan prop `variant`
3. Baru buat komponen baru kalau benar-benar berbeda

---

## Do's

- Satu button primary (`bg-fern-700`) per section atau card
- Gambar makanan/kantin selalu `object-cover` dengan aspect ratio konsisten
- Card riwayat selalu `bg-vanilla-custard-50` — jangan ganti ke warna lain
- Inner card di dalam card riwayat selalu `bg-white`
- Search bar dan filter dropdown selalu `rounded-full`
- Status badge selalu menggunakan 4 nilai yang sudah terdefinisi

## Don'ts

- Jangan pakai `bg-green-*` atau `text-green-*` bawaan Tailwind — pakai custom tokens
- Jangan pakai `rounded-lg` untuk search bar atau input utama
- Jangan stack dua button primary berdampingan
- Jangan pakai shadow heavy (`shadow-xl`, `shadow-2xl`) di card biasa
- Jangan ubah font dari Poppins ke font lain
- Jangan gunakan em dash (—) di dalam konten UI atau komentar kode
- Jangan gunakan emoji sebagai ikon, setidaknya gunakan icon svg
