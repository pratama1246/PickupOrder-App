# 📗 Brand Identity & Visual Guideline — PickupOrder PNC

Dokumentasi standar identitas visual, kumpulan aset logo vector SVG resmi, palet warna, tipografi, dan panduan penggunaan untuk platform **PickupOrder Politeknik Negeri Cilacap (PNC)**.

---

## 📂 Kumpulan Aset Logo Resmi (`public/assets/brand/`)

Semua file logo telah di-standarisasi dalam format vector SVG murni beresolusi tinggi, siap pakai, dan mudah di-export/dikonversi ke format PNG/ICO sesuai kebutuhan:

| Nama File | Format / Ukuran | Kegunaan Utama | Link Preview Browser |
|---|---|---|---|
| `brand-icon.svg` | SVG (512×512) | Master Logomark Transparan | [Lihat Asset](http://localhost:8000/assets/brand/brand-icon.svg) |
| `brand-icon-rounded.svg` | SVG (512×512) | App Icon Squircle (PWA, Android APK, App Store) | [Lihat Asset](http://localhost:8000/assets/brand/brand-icon-rounded.svg) |
| `brand-logo-horizontal.svg` | SVG (520×120) | Master Lockup Horizontal (Light Background) | [Lihat Asset](http://localhost:8000/assets/brand/brand-logo-horizontal.svg) |
| `brand-logo-horizontal-dark.svg` | SVG (520×120) | Master Lockup Horizontal (Dark Background) | [Lihat Asset](http://localhost:8000/assets/brand/brand-logo-horizontal-dark.svg) |
| `brand-logo-vertical.svg` | SVG (360×420) | Master Lockup Vertikal / Stacked (Light) | [Lihat Asset](http://localhost:8000/assets/brand/brand-logo-vertical.svg) |
| `brand-logo-vertical-dark.svg` | SVG (360×420) | Master Lockup Vertikal / Stacked (Dark) | [Lihat Asset](http://localhost:8000/assets/brand/brand-logo-vertical-dark.svg) |
| `favicon.svg` | SVG (64×64) | Browser Tab Favicon (Sharp Vector) | [Lihat Asset](http://localhost:8000/assets/brand/favicon.svg) |
| `apple-touch-icon.svg` | SVG (180×180) | iOS Apple Home Screen Icon | [Lihat Asset](http://localhost:8000/assets/brand/apple-touch-icon.svg) |
| `brand-monochrome-black.svg` | SVG (520×120) | Solid Black Monochrome (Cetak, Stempel, Struk) | [Lihat Asset](http://localhost:8000/assets/brand/brand-monochrome-black.svg) |
| `brand-monochrome-white.svg` | SVG (520×120) | Solid White Monochrome (Overlay Gelap/Foto) | [Lihat Asset](http://localhost:8000/assets/brand/brand-monochrome-white.svg) |

---

## 🎨 Visual Showcase & Variasi Logo

````carousel
![Master Lockup Horizontal](/home/pputra/.gemini/antigravity-cli/brain/4d51a517-aba7-467a-87e9-614787ebd196/brand-logo-horizontal.svg)
<!-- slide -->
![Master Lockup Horizontal Dark](/home/pputra/.gemini/antigravity-cli/brain/4d51a517-aba7-467a-87e9-614787ebd196/brand-logo-horizontal-dark.svg)
<!-- slide -->
![Master Logomark Standalone](/home/pputra/.gemini/antigravity-cli/brain/4d51a517-aba7-467a-87e9-614787ebd196/brand-icon.svg)
<!-- slide -->
![App Icon Squircle 512x512](/home/pputra/.gemini/antigravity-cli/brain/4d51a517-aba7-467a-87e9-614787ebd196/brand-icon-rounded.svg)
<!-- slide -->
![Master Lockup Vertikal](/home/pputra/.gemini/antigravity-cli/brain/4d51a517-aba7-467a-87e9-614787ebd196/brand-logo-vertical.svg)
<!-- slide -->
![Apple Touch Icon 180x180](/home/pputra/.gemini/antigravity-cli/brain/4d51a517-aba7-467a-87e9-614787ebd196/apple-touch-icon.svg)
<!-- slide -->
![Monochrome Black](/home/pputra/.gemini/antigravity-cli/brain/4d51a517-aba7-467a-87e9-614787ebd196/brand-monochrome-black.svg)
````

---

## 🎨 Palet Warna Resmi (Color Tokens)

Identitas warna dibangun dari perpaduan hijau botol kampus yang berwibawa (*Fern*), kesegaran makanan siap saji (*Fresh Lime*), dan aksen kehangatan (*Warm Gold*):

```
+-----------------------------------------------------------------------------------------+
|  Primary Fern Green   |  Vibrant Lime Fresh   |  Charcoal Dark Text   |  Warm Gold Accent|
|  #306939 / #347B42    |  #84CC16 / #558B0E    |  #131720              |  #FFBF4A         |
|  Tailwind: fern-700   |  Tailwind: lime-500   |  Tailwind: shadow-900 |  Accent Sparkle  |
+-----------------------------------------------------------------------------------------+
```

* **Primary Dark Green (`#306939` / `#347B42` / `fern-700`):** Mewakili almamater PNC, stabilitas sistem, dan teks sekunder `Order`.
* **Fresh Lime Green (`#84CC16` / `#558B0E`):** Mewakili kecepatan, kesegaran makanan, dan sifat modern aplikasi.
* **Charcoal Slate (`#131720` / `text-shadow-grey-900`):** Teks utama `Pickup` untuk keterbacaan tajam & kontras tinggi.
* **Warm Gold (`#FFBF4A`):** Aksen titik hangat di dalam ikon untuk menggugah selera makan (*appetizing accent*).
* **Soft Mint Background (`#EEF7EF` / `bg-fern-50`):** Latar belakang navbar & sidebar agar sejuk di mata.

---

## ✍️ Tipografi Resmi (Typography)

* **Font Family:** `Poppins`, sans-serif (Google Fonts)
* **Wordmark Construction:**
  - **`Pickup`:** Font-weight `800 / 900 (ExtraBold/Black)`, tracking `-1.5px / tracking-tight`, Color: `#131720` (atau `#FFFFFF` pada mode gelap).
  - **`Order`:** Font-weight `800 / 900 (ExtraBold/Black)`, tracking `-1.5px / tracking-tight`, Color: `#306939` (atau `#4ADE80` pada mode gelap).

---

## 📐 Panduan Ukuran & Clear Space

```
                     [ Clear Space Minimum = 1/2 Lebar Ikon P ]
    +-------------------------------------------------------------------------+
    |                                                                         |
    |      [ ICON ]   (Clear Gap)    PickupOrder                              |
    |                                Politeknik Negeri Cilacap                |
    |                                                                         |
    +-------------------------------------------------------------------------+
```

* **Ukuran Minimum Ikon:**
  - **Favicon Browser:** 16×16px / 32×32px (Gunakan `favicon.svg`)
  - **Mobile Navbar / Dock:** 28px - 32px (`w-8 h-8`)
  - **Desktop Navbar:** 40px - 44px (`w-10 h-10` / `w-11 h-11`)
  - **Hero Section / Landing:** 64px - 120px
  - **App Icon Mobile:** 512×512px (`brand-icon-rounded.svg`)

---

## 🖼️ Panduan Export / Konversi ke PNG

Jika kamu ingin mengonversi aset SVG ke file raster PNG (misal untuk `favicon.ico`, `apple-touch-icon.png`, atau Android splash screen):

1. **Favicon Multi-Size (`favicon.ico`):**
   - Source: `public/assets/brand/favicon.svg`
   - Convert ke PNG ukuran: `16x16`, `32x32`, `48x48`.
2. **Apple Touch Icon (`apple-touch-icon.png`):**
   - Source: `public/assets/brand/apple-touch-icon.svg`
   - Convert ke PNG ukuran: `180x180` (300 DPI, no alpha cut).
3. **Android App Icon & PWA Manifest:**
   - Source: `public/assets/brand/brand-icon-rounded.svg`
   - Convert ke PNG ukuran: `192x192` dan `512x512` (`icon-192.png`, `icon-512.png`).
4. **Social Share / OpenGraph Banner:**
   - Source: `public/assets/brand/brand-logo-horizontal.svg`
   - Tempatkan di tengah kanvas `1200x630` px dengan background `#EEF7EF`.

---

## 🚫 Brand Do's & Don'ts

### ✅ DO:
* Gunakan versi horizontal di navbar utama dan footer.
* Gunakan versi square (`brand-icon.svg` atau `brand-icon-rounded.svg`) untuk avatar, profil, favicon, atau launcher icon.
* Pertahankan rasio aspek 1:1 untuk ikon mark.
* Gunakan versi dark (`-dark.svg`) jika diletakkan di latar belakang berwarna hijau tua / hitam / foto malam.

### ❌ DON'T:
* Jangan mengubah warna gradien menjadi warna di luar palet resmi (misal merah, ungu, oranye).
* Jangan meregangkan (*stretch*) atau mendistorsi bentuk huruf P.
* Jangan menambahkan teks atau badge lain di atas ikon mark.
* Jangan mengganti jenis huruf teks `PickupOrder` selain font resmi Poppins.
