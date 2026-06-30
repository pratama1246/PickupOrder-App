# Analisis Struktur Desain Figma - Sistem Pickup Order PNC

> Dokumen ini dibuat berdasarkan 3 screenshot Figma yang mencakup seluruh halaman
> user, vendor, dan admin. Gunakan sebagai referensi sebelum membuat blade view.

---

## RINGKASAN HALAMAN

### User Side (Image 1)
| No | Halaman | Route (usulan) | Status |
|---|---|---|---|
| 1 | Detail Menu | `/kantin/{id}/menu/{id}` | Belum dibuat |
| 2 | Keranjang Belanja | `/keranjang` | Belum dibuat |
| 3 | Checkout Pesanan | `/checkout` | Belum dibuat |
| 4 | Pesanan Dalam Antrian | `/pesanan/antrian/{id}` | Belum dibuat |

### Vendor Side (Image 2)
| No | Halaman | Route (usulan) | Layout |
|---|---|---|---|
| 1 | Dashboard Overview | `/vendor/dashboard` | Sidebar + Content |
| 2 | Daftar Transaksi | `/vendor/transaksi` | Sidebar + Content |
| 3 | Detail Transaksi | `/vendor/transaksi/{id}` | Sidebar + Content |
| 4 | Daftar Menu | `/vendor/menu` | Sidebar + Content |

### Admin Side (Image 3)
| No | Halaman | Route (usulan) | Layout |
|---|---|---|---|
| 1 | Dashboard Overview | `/admin/dashboard` | Sidebar + Content |
| 2 | Daftar Kantin | `/admin/kantin` | Sidebar + Content |
| 3 | Tambah Kantin | `/admin/kantin/tambah` | Sidebar + Content |
| 4 | Daftar Pengguna | `/admin/pengguna` | Sidebar + Content |

---

## DETAIL HALAMAN - USER SIDE

---

### 1. Detail Menu (`/kantin/{id}/menu/{id}`)

**Layout:** 2 kolom (kiri: detail menu, kanan: menu lain dari kantin yang sama)

**Kolom Kiri - Detail Menu:**
- Gambar menu: full width, aspect ratio landscape, `rounded-2xl`
- Info kantin: text kecil `text-sm text-base-content/60` (contoh: "Kantin 1")
- Nama menu: `text-2xl font-bold`
- Harga: `text-lg font-bold` (contoh: "Rp. 10.000")
- Deskripsi: `text-sm text-base-content/70`
- Tags: chip kecil `rounded-full bg-base-200 text-xs px-2 py-1` (contoh: Nasi, Sayur, Ayam)
- Quantity control: tombol `-` angka `+` inline
- Total: label "Total :" + harga `font-bold`
- Button CTA: `btn bg-fern-700 text-white w-full` "Tambah ke Keranjang"
- Info bar bawah: rating ⭐, estimasi waktu 🕒, badge "Lagi Populer" 📈, status "Tersedia" 🟢

**Kolom Kanan - Menu Lain dari Kantin:**
- Section title: "Menu Lain dari Kantin 1"
- Grid: `grid-cols-2` (2 kolom), card vertikal seperti `foodcard` yang sudah ada
- Setiap card: gambar, nama, deskripsi singkat, harga, button "Pesan"

**Komponen baru yang dibutuhkan:**
- `quantity-control.blade.php` (tombol - angka +, reusable)
- `menu-tag.blade.php` (chip tag kategori)
- `info-bar.blade.php` (rating + estimasi + badge + status dalam 1 baris)

---

### 2. Keranjang Belanja (`/keranjang`)

**Layout:** 2 kolom (kiri: list item per kantin, kanan: ringkasan belanja)

**Kolom Kiri - Daftar Item:**
- Section title: "Keranjang Belanja" + subtitle
- Card per kantin: header "Kantin 1" + jumlah pesanan
- Setiap item: gambar + nama + deskripsi + quantity control + harga + icon delete 🗑️
- Catatan untuk kantin: textarea `(Opsional)` di bawah item list
- Bisa ada multiple card kalau pesan dari kantin berbeda

**Kolom Kanan - Ringkasan Belanja:**
- Card sticky: `sticky top-24`
- Title: "Ringkasan Belanja"
- List kantin + subtotal per kantin
- Total keseluruhan: `font-bold text-xl`
- Button: `btn bg-fern-700 text-white w-full` "Bayar Sekarang"

**Komponen baru yang dibutuhkan:**
- `cart-item.blade.php` (item di keranjang, ada quantity control + delete)
- `cart-summary.blade.php` (ringkasan belanja sticky sidebar)

---

### 3. Checkout Pesanan (`/checkout`)

**Layout:** 2 kolom (kiri: form pilihan, kanan: detail order + total)

**Kolom Kiri - Form Checkout:**

*Section 1: Pilih Jam Pengambilan*
- Box title: "Pilih Jam Pengambilan"
- Grid 2x2 tombol jam: style toggle button (selected: `bg-fern-700 text-white`, unselected: `bg-base-200`)
- Contoh slot: "Sekarang", "09.20", "11.30", "Atur Jam Lainnya"
- Setiap slot ada sub-label kecil (contoh: "Siap dalam 5-15 menit", "Istirahat Pertama")

*Section 2: Pilih Metode Pembayaran*
- Box title: "Pilih Metode Pembayaran"
- Radio-style list: QRIS + Bayar Di Warung
- QRIS: ada icon QR + label + deskripsi kecil
- Bayar Di Warung: ada icon kasir + label

*Section 3: QRIS (conditional, muncul kalau pilih QRIS)*
- Box title: "QRIS Kantin 1"
- Area QR code: placeholder kotak abu-abu
- Button: "Unduh QRIS" (outline) + "Upload Bukti Pembayaran" (outline)

*Footer Kolom Kiri:*
- Button "Batalkan": `btn bg-red-500 text-white`
- Button "Konfirmasi Sekarang": `btn bg-fern-700 text-white`

**Kolom Kanan - Detail Order:**
- Section title: "Detail Order"
- Card: nama kantin + jumlah pesanan
- List item: seperti `order-item` variant list yang sudah ada
- Catatan untuk kantin (opsional)
- Card Total Belanja: harga besar `text-2xl font-bold`

**Komponen baru yang dibutuhkan:**
- `time-slot-picker.blade.php` (grid pilih jam pengambilan)
- `payment-method-picker.blade.php` (radio pilih metode bayar)
- `qris-box.blade.php` (tampil QR + unduh + upload bukti)

---

### 4. Pesanan Dalam Antrian (`/pesanan/antrian/{id}`)

**Layout:** Full width, 2 bagian (atas: progress tracker, bawah: 2 kolom detail)

**Bagian Atas:**
- Header: "Pesananmu Dalam Antrian" + subtitle
- Estimasi waktu: pojok kanan atas, "Estimasi Waktu : 5 Menit"
- Progress tracker horizontal: 4 step dengan icon lingkaran + label
  - Step 1 (active): Menunggu Konfirmasi (icon: dokumen, warna `fern-700`)
  - Step 2 (active): Dalam Antrian (icon: orang antri, warna `fern-700`)
  - Step 3 (inactive): Sedang Di masak (icon: kompor, abu-abu)
  - Step 4 (inactive): Siap Di Ambil (icon: bag, abu-abu)
- Koneksi antar step: garis horizontal, warna sesuai status

**Bagian Bawah - 2 Kolom:**

*Kolom Kiri - Detail Order:*
- No. Order + nama kantin + jumlah pesanan
- Info: lokasi kantin (📍), status antrian (⚡)
- List item pesanan (seperti `order-item` list variant)
- Catatan opsional

*Kolom Kanan - Total + Action:*
- Card: "Total Belanja" + harga besar
- Info: Metode Pembayaran + Waktu Pickup (dengan icon ■)
- Button: "Ambil Sekarang" `btn bg-base-200 text-base-content w-full`

**Komponen baru yang dibutuhkan:**
- `order-progress.blade.php` (progress tracker 4 step horizontal)

---

## DETAIL HALAMAN - VENDOR SIDE

**Layout Global Vendor:** Sidebar kiri (fixed, dark) + Content area kanan (white)

**Sidebar Vendor:**
- Header: "Kantin 1 Dashboard" (di navbar, bukan sidebar)
- Navbar: tombol "Ke Halaman Utama" + avatar
- Sidebar item: Overview, Order, Menu
- Active state: `bg-fern-700 text-white rounded-lg`
- Inactive state: `text-white`
- Width sidebar: sekitar `w-16` atau icon only (terlihat sangat narrow di desain)

**Layout file yang dibutuhkan:**
- `resources/views/layouts/vendor.blade.php`

---

### 1. Vendor - Dashboard Overview (`/vendor/dashboard`)

**Konten:**
- Title: "Statistik Penjualan"
- Grid statistik `grid-cols-3` baris pertama:
  - "Pesanan Baru" → angka besar + label
  - "Sedang Dimasak" → angka besar + label
  - "Siap Pickup" → angka besar + label
- Grid statistik `grid-cols-2` baris kedua:
  - "Total Pendapatan" → nominal rupiah
  - "Menu Habis" → angka, text merah jika ada (`text-red-500`)
- Setiap stat card: `bg-base-200 rounded-xl p-4`, no border

**Komponen baru yang dibutuhkan:**
- `stat-card.blade.php` (props: label, value, valueColor)

---

### 2. Vendor - Daftar Transaksi (`/vendor/transaksi`)

**Konten:**
- Title: "Daftar Transaksi"
- List card transaksi (full width):
  - No. Orderan + "Jenis Pesanan :" label
  - Status badge (kiri bawah)
  - Button "Detail" (kanan, `btn btn-sm bg-base-200`)
- Background card: putih dengan border tipis
- Bisa scroll panjang

---

### 3. Vendor - Detail Transaksi (`/vendor/transaksi/{id}`)

**Konten:**
- Title: "Detail Transaksi"
- Card utama dengan status badge di pojok kanan atas
- Header: nama kantin + No. Order + jumlah pesanan
- List item pesanan (mirip `order-item` list variant)
- Catatan opsional
- Footer card:
  - Kiri: "Total Belanja" + harga + info (lokasi, antrian, metode bayar, waktu pickup)
  - Kanan: Button "Ubah Status" (`btn bg-fern-700`) + "Batalkan Pesanan" (`btn bg-red-500`)

---

### 4. Vendor - Daftar Menu (`/vendor/menu`)

**Konten:**
- Title: "Daftar Menu" + Filter By + Search input + Button "Tambah Kantin" (`btn bg-fern-700`)
- Grid `grid-cols-2` card menu:
  - Gambar menu (atas, full width card)
  - Nama menu
  - Deskripsi singkat
  - Harga
  - Icon ✏️ edit + 🗑️ hapus (pojok kanan bawah)
- Card tanpa button "Pesan" (beda dengan `foodcard` user)

**Komponen baru yang dibutuhkan:**
- `vendor-menu-card.blade.php` (mirip foodcard tapi ada edit/delete, tanpa tombol Pesan)

---

## DETAIL HALAMAN - ADMIN SIDE

**Layout Global Admin:** Sidebar kiri (fixed, dark) + Content area kanan (white)

**Sidebar Admin:**
- Item: Overview, Kantin, Pengguna
- Sama persis polanya dengan vendor sidebar
- Active state: `bg-fern-700 text-white rounded-lg`

**Layout file yang dibutuhkan:**
- `resources/views/layouts/admin.blade.php`

---

### 1. Admin - Dashboard Overview (`/admin/dashboard`)

**Konten:**
- Title: "Statistik Penjualan"
- Grid `grid-cols-2` baris pertama:
  - "Pengguna" → angka
  - "Total Kantin" → angka
  - "Total Order" → angka
- Baris kedua `grid-cols-2`:
  - "Total Transaksi" → nominal
  - "Total Menu" → angka
- Reuse komponen `stat-card` dari vendor

---

### 2. Admin - Daftar Kantin (`/admin/kantin`)

**Konten:**
- Title: "Daftar Kantin" + Filter By + Search + Button "Tambah Kantin" + "Hapus Kantin"
- List card kantin (layout horizontal):
  - Gambar kantin (kiri, kotak abu-abu)
  - Nama, rating ⭐, deskripsi, info jumlah menu + jam
  - Button "Detail" (kanan bawah, `btn bg-fern-700`)
- Mirip `canteencard` tapi ada aksi tambah/hapus di header

---

### 3. Admin - Tambah Kantin (`/admin/kantin/tambah`)

**Konten:**
- Title: "Tambah Kantin"
- Form fields:
  - Nama Kantin (input text)
  - Nama Pengelola (input text)
  - No. HP (input text)
  - Email (input text, opsional)
  - Lokasi (textarea)
  - Upload Gambar Kantin (file upload / area klik)
- Footer: Button "Tambah Kantin" (`btn bg-fern-700`) + "Batal" (`btn bg-red-500`)

---

### 4. Admin - Daftar Pengguna (`/admin/pengguna`)

**Konten:**
- Title: "Daftar Pengguna" + Filter By + Search + Button "Tambah Kantin" + "Hapus Kantin"
- Tabel dengan kolom: Nama | Nomor ID | Aksi
- Setiap baris: nama pengguna, NIM/NIP, tombol "Detail" + "Nonaktif"
- "Detail": `btn btn-sm bg-base-200`
- "Nonaktif": `btn btn-sm bg-red-500 text-white`
- Tabel dengan border tipis, row hover effect

---

## KOMPONEN BARU YANG PERLU DIBUAT

### User Components
| Komponen | File | Dipakai di |
|---|---|---|
| Quantity Control | `quantity-control.blade.php` | Detail Menu, Keranjang |
| Menu Tag | `menu-tag.blade.php` | Detail Menu |
| Info Bar | `info-bar.blade.php` | Detail Menu |
| Cart Item | `cart-item.blade.php` | Keranjang |
| Cart Summary | `cart-summary.blade.php` | Keranjang |
| Time Slot Picker | `time-slot-picker.blade.php` | Checkout |
| Payment Method Picker | `payment-method-picker.blade.php` | Checkout |
| QRIS Box | `qris-box.blade.php` | Checkout |
| Order Progress | `order-progress.blade.php` | Halaman Antrian |

### Vendor + Admin Components (Shared)
| Komponen | File | Dipakai di |
|---|---|---|
| Stat Card | `stat-card.blade.php` | Vendor Dashboard, Admin Dashboard |
| Sidebar Item | `sidebar-item.blade.php` | Semua halaman vendor & admin |
| Vendor Menu Card | `vendor-menu-card.blade.php` | Vendor Daftar Menu |
| Transaction Card | `transaction-card.blade.php` | Vendor Daftar Transaksi |

### Layout Baru
| Layout | File | Dipakai di |
|---|---|---|
| Vendor Layout | `layouts/vendor.blade.php` | Semua halaman vendor |
| Admin Layout | `layouts/admin.blade.php` | Semua halaman admin |

---

## POLA DESAIN YANG KONSISTEN (lintas semua role)

1. **Navbar dark** (`bg-shadow-grey-900`) dipakai di semua role — user, vendor, admin
2. **Status badge** sama persis: Diproses (kuning), Selesai (hijau), Dibatalkan (merah), Menunggu (biru)
3. **Button primary** selalu `bg-fern-700 text-white hover:bg-fern-800`
4. **Button danger** selalu `bg-red-500 text-white`
5. **Card inner** selalu `bg-white border border-base-content/30 rounded-2xl`
6. **Order item** reuse komponen yang sama di semua role (user, vendor, admin)
7. **Search + Filter** pola sama: `input rounded-full` + `select rounded-full`
8. **Sidebar vendor & admin** identik polanya, beda menu item saja
9. **Stat card** reusable antara vendor dan admin dashboard
