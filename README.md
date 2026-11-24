<div align="center">

<img src="docs/banner.jpg" alt="CateringKu, platform pemesanan katering online" width="100%" />

<br/>
<br/>

**Pesan katering untuk acara apa pun, dari memilih vendor sampai struk, dalam satu platform.**
Tiga peran (Pelanggan, Vendor, Admin), kode promo, ulasan, dan pembayaran multi-provider.

<br/>

[![CI](https://img.shields.io/github/actions/workflow/status/zynxx24/CateringKu-ZynxWira-/laravel.yml?branch=main&style=for-the-badge&label=CI&logo=githubactions&logoColor=white)](https://github.com/zynxx24/CateringKu-ZynxWira-/actions/workflows/laravel.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)](LICENSE)
[![Last commit](https://img.shields.io/github/last-commit/zynxx24/CateringKu-ZynxWira-?style=for-the-badge)](https://github.com/zynxx24/CateringKu-ZynxWira-/commits/main)

[![PHP](https://img.shields.io/badge/PHP-8.3%20%7C%208.4-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3-4FC08D?style=flat-square&logo=vuedotjs&logoColor=white)](https://vuejs.org)
[![Inertia](https://img.shields.io/badge/Inertia.js-3-9553E9?style=flat-square)](https://inertiajs.com)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?style=flat-square&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![Tailwind](https://img.shields.io/badge/Tailwind-4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Pest](https://img.shields.io/badge/tested%20with-Pest%204-F28D1A?style=flat-square)](https://pestphp.com)

</div>

---

## Daftar Isi

- [Tampilan](#-tampilan)
- [Fitur](#-fitur)
- [Mulai Cepat](#-mulai-cepat)
- [Perintah](#-perintah)
- [Akun Demo](#-akun-demo)
- [Arsitektur](#-arsitektur)
- [Desain & Aksesibilitas](#-desain--aksesibilitas)
- [Keamanan](#-keamanan)
- [Testing & CI](#-testing--ci)
- [Struktur Proyek](#-struktur-proyek)
- [Kontribusi](#-kontribusi)
- [Lisensi](#-lisensi)

---

## 📸 Tampilan

Semua gambar di bawah adalah **tangkapan layar aplikasi yang berjalan** (data dari seeder demo).

<table>
  <tr>
    <td width="50%"><img src="docs/screenshots/01-beranda.webp" alt="Beranda"/><br/><sub><b>Beranda</b>: pencarian vendor dan kategori</sub></td>
    <td width="50%"><img src="docs/screenshots/02-cari-vendor.webp" alt="Cari vendor"/><br/><sub><b>Cari vendor</b>: filter kota, urut rating/terbaru/nama</sub></td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/03-detail-vendor.webp" alt="Detail vendor"/><br/><sub><b>Detail vendor</b>: menu, paket, dan ulasan</sub></td>
    <td><img src="docs/screenshots/08-checkout.webp" alt="Checkout"/><br/><sub><b>Checkout</b>: kode promo, pembayaran multi-provider</sub></td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/07-keranjang.webp" alt="Keranjang"/><br/><sub><b>Keranjang</b>: satu keranjang per vendor</sub></td>
    <td><img src="docs/screenshots/09-pesanan-saya.webp" alt="Pesanan saya"/><br/><sub><b>Pesanan saya</b>: status, struk, ulasan</sub></td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/10-dashboard-admin.webp" alt="Dashboard admin"/><br/><sub><b>Dashboard admin</b>: statistik, pembayaran tertunda</sub></td>
    <td><img src="docs/screenshots/12-dashboard-vendor.webp" alt="Dashboard vendor"/><br/><sub><b>Dashboard vendor</b>: pesanan, pendapatan, rating</sub></td>
  </tr>
</table>

<details>
<summary><b>Lihat semua halaman</b> (login, kontak, tentang, admin, menu vendor)</summary>
<br/>

| | |
|---|---|
| ![Login](docs/screenshots/06-login.webp) | ![Tentang](docs/screenshots/04-tentang.webp) |
| ![Kontak](docs/screenshots/05-kontak.webp) | ![Admin pesanan](docs/screenshots/11-admin-pesanan.webp) |
| ![Menu vendor](docs/screenshots/13-vendor-menu.webp) | |

</details>

<details>
<summary><b>Desain awal (mockup)</b>: sebelum diimplementasikan</summary>
<br/>

| | |
|---|---|
| ![](docs/design/beranda.webp) | ![](docs/design/login.webp) |
| ![](docs/design/keranjang.webp) | ![](docs/design/data-pemesanan.webp) |
| ![](docs/design/admin-dashboard.webp) | ![](docs/design/vendor-crud-menu.webp) |

</details>

---

## ✨ Fitur

| Peran | Yang bisa dilakukan |
|---|---|
| 👤 **Pelanggan** | Cari vendor & menu, keranjang per vendor, checkout dengan tanggal/jam/alamat acara, kode promo, pilih pembayaran (Transfer Bank, E-Wallet, QRIS, Virtual Account), lihat struk, batalkan pesanan *pending*, beri ulasan setelah pesanan selesai |
| 🏪 **Vendor** | Dashboard pendapatan & rating, kelola pesanan (konfirmasi → persiapan → pengiriman → selesai), CRUD menu, balas ulasan |
| 🛡️ **Admin** | Statistik platform, semua pesanan, **verifikasi pembayaran**, kotak pesan kontak |

Lainnya: autentikasi lengkap dengan **2FA** (Laravel Fortify), mode gelap, UI responsif, harga dihitung ulang **di server** (lihat [Keamanan](#-keamanan)).

---

## 🚀 Mulai Cepat

**Prasyarat:** PHP 8.3+ (`pdo_sqlite`, `mbstring`, `zip`, `bcmath`, `curl`), Composer 2, Node.js 20+ (CI memakai 24).
> Windows: PHP 8.3 portabel sudah ada di folder `php83/`; `run.bat` memakainya otomatis.

```bash
git clone https://github.com/zynxx24/CateringKu-ZynxWira-.git
cd CateringKu-ZynxWira-

bash run.sh setup      # Windows: .\run.bat setup
bash run.sh dev        # Windows: .\run.bat dev
```

`setup` memasang dependensi, membuat `.env` dari `.env.example`, membuat `APP_KEY`, migrasi, dan mengisi data demo.
Buka **http://localhost:8000**.

**Kontak & media sosial** di footer dan halaman Kontak diambil dari `.env`. Selama kosong, bagian itu disembunyikan, jadi situs tidak pernah menampilkan nomor telepon karangan atau ikon sosial yang mengarah ke `#`.

```dotenv
CATERINGKU_CONTACT_EMAIL=halo@domainanda.id
CATERINGKU_CONTACT_PHONE="0812-xxxx-xxxx"
CATERINGKU_CONTACT_ADDRESS="Jl. ..., Kota"
CATERINGKU_CONTACT_HOURS="Senin sampai Sabtu, 08.00 sampai 17.00"
CATERINGKU_INSTAGRAM_URL=https://instagram.com/akunanda
CATERINGKU_WHATSAPP_URL=https://wa.me/62812xxxxxxx
```

<details>
<summary>Tanpa script (manual)</summary>

```bash
cd CateringKu
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed --seeder=FullTestSeeder
npm install && npm run build      # atau: composer dev  (server + queue + vite)
php artisan serve
```
</details>

---

## 📜 Perintah

Jalankan dari root repo.

| Tujuan | Linux/macOS | Windows |
|---|---|---|
| Setup awal | `bash run.sh setup` | `run.bat setup` |
| Dev server (PHP + Vite) | `bash run.sh dev` | `run.bat dev` |
| Migrasi / seed | `bash run.sh migrate` / `seed` | `run.bat migrate` / `seed` |
| Reset database ⚠️ | `bash run.sh fresh` | `run.bat fresh` |
| Build produksi | `bash run.sh build` | `run.bat build` |
| Jalankan tes | `bash run.sh test` | `run.bat test` |
| Bersihkan cache | `bash run.sh clear` | `run.bat clear` |
| Hentikan server | `bash run.sh stop` | `run.bat stop` |

Di dalam `CateringKu/`: `composer lint` (Pint), `npm run types:check`, `npm run build`.

---

## 🔑 Akun Demo

Dibuat oleh `FullTestSeeder`. Detail lengkap ada di [`CateringKu/test-accounts.md`](CateringKu/test-accounts.md).

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@cateringku.com` | `admin123` |
| Vendor | `nusantara@cateringku.com` (juga `sarirasa@`, `berkah@`, `royal@`, `padang@`) | `vendor123` |
| Pelanggan | `ahmad_fauzi@gmail.com` (15 akun) | `user123` |

Kode promo untuk dicoba: `WELCOME10` (10%, min. Rp 100.000), `HEMAT20K` (Rp 20.000, min. Rp 200.000), `NUSANTARA15`, `ROYAL25`, `PADANG10`, `WEEKEND30`.

> ⚠️ Akun-akun ini hanya untuk pengembangan. Jangan seed di produksi.

---

## 🧱 Arsitektur

```mermaid
flowchart LR
    B[Browser<br/>Vue 3 + Inertia] -->|halaman & form| R[Routes + Middleware<br/>auth · verified · role · throttle]
    R --> C[Controllers]
    C --> S[OrderPricing<br/>harga, diskon, PPN]
    C --> M[(Eloquent Models<br/>SQLite / MySQL)]
    S --> M
```

```mermaid
erDiagram
    USERS ||--o| VENDORS : "memiliki"
    USERS ||--o{ ORDERS : "memesan"
    USERS ||--o{ CART : "punya"
    VENDORS ||--o{ MENU_ITEMS : "menjual"
    VENDORS ||--o{ PACKAGES : "menawarkan"
    VENDORS ||--o{ ORDERS : "menerima"
    CATEGORIES ||--o{ MENU_ITEMS : "mengelompokkan"
    CART ||--o{ CART_ITEMS : "berisi"
    ORDERS ||--|{ ORDER_ITEMS : "terdiri dari"
    ORDERS ||--o{ PAYMENTS : "dibayar via"
    ORDERS ||--o| REVIEWS : "diulas"
    PROMOS ||--o{ PROMO_USAGE : "dipakai"
    ORDERS ||--o| PROMO_USAGE : "memakai"
```

**Alur harga:** keranjang menyimpan *jumlah per tamu*; saat checkout jumlah dikali `num_people`.
`subtotal − diskon promo → PPN 11% → + ongkir Rp 15.000 = total`. Semua dihitung di `app/Services/OrderPricing.php`, dipakai bersama oleh pratinjau promo dan checkout.

---

## 🎨 Desain & Aksesibilitas

Arah desain mengikuti mockup awal di [`docs/design/`](docs/design): oranye CateringKu, Poppins untuk judul, dan kartu vendor yang hangat. UI diaudit dengan gerbang **antislop** (tanpa klaim palsu, angka tanpa sumber, link mati, atau em dash) dan **WCAG 2.1 AA**:

| Keputusan | Alasan |
|---|---|
| Oranye brand untuk teks dan tombol digelapkan ke karat `#B8461F` | Putih di atas `#F5845C` hanya 2.5:1; `#B8461F` memberi 5.3:1 tanpa keluar dari keluarga warna mockup |
| `#F5845C` tetap dipakai untuk teks di mode gelap | 6.5:1 di atas latar gelap |
| Statistik di halaman Tentang dihitung langsung dari database | Tidak ada angka marketing tanpa sumber |
| Placeholder vendor berupa monogram inisial | Tidak menulis klaim apa pun atas nama vendor |
| Petak ikon dashboard memakai satu tint brand | Sebelumnya tiap kartu punya gradien pelangi berbeda |

Diperiksa dengan browser otomatis di 22 halaman × mode terang/gelap × lebar 1440 px dan 360 px: tidak ada overflow horizontal, tidak ada error console, semua teks lolos kontras AA (kecuali tautan pagination nonaktif, yang dikecualikan WCAG). Semua kontrol punya nama aksesibel, semua field punya label, dan fokus keyboard selalu terlihat. Animasi berhenti saat `prefers-reduced-motion` aktif.

---

## 🔒 Keamanan

- **Harga tidak pernah dipercaya dari browser.** Subtotal, diskon, pajak, dan total dihitung ulang dari database saat checkout.
- **Promo**: validasi di server (masa berlaku, vendor, minimal order, satu kali per pelanggan), penghitung `used_count` atomik sehingga batas pemakaian tidak bisa dilampaui checkout paralel.
- **Keranjang**: item harus milik vendor yang sama dan masih tersedia; jumlah dibatasi.
- **Otorisasi**: pelanggan hanya melihat pesanannya, vendor hanya pesanan & menunya sendiri (diuji di `AccessControlTest`).
- Registrasi selalu membuat peran `customer`; kolom peran tidak bisa dikirim dari form.
- **Header keamanan** (CSP tanpa `unsafe-eval` di luar mode lokal, `X-Frame-Options`, HSTS di produksi, dll.) dan **rate limit** pada kontak, promo, dan checkout.
- Proxy tepercaya hanya aktif jika `TRUSTED_PROXIES` diisi. `.env` tidak pernah di-commit.

Menemukan celah? Buka [issue](https://github.com/zynxx24/CateringKu-ZynxWira-/issues) atau hubungi pemilik repo secara langsung untuk hal sensitif.

---

## 🧪 Testing & CI

```bash
cd CateringKu && php artisan test
```

**72 tes** (Pest): autentikasi, 2FA, pengaturan akun, keranjang, checkout, promo, kontrol akses antar-peran, filter kategori, statistik halaman Tentang, dan kontak dari konfigurasi.

GitHub Actions ([`laravel.yml`](.github/workflows/laravel.yml)) menjalankan di setiap push/PR ke `main`:

| Job | Isi |
|---|---|
| **Pest** | PHP 8.3 dan 8.4, build aset Vite, `php artisan test` |
| **Style, types, audit** | `pint --test`, `vue-tsc`, `composer audit` + `npm audit` (informasional) |

---

## 📁 Struktur Proyek

```
.
├── CateringKu/                  # aplikasi Laravel
│   ├── app/
│   │   ├── Http/Controllers/    # Home, Vendor, Cart, Checkout, Order, Admin, VendorDashboard…
│   │   ├── Http/Middleware/     # SecurityHeaders, EnsureUserIsAdmin/Vendor, ShareCartCount
│   │   ├── Models/              # Order, Vendor, MenuItem, Promo, Payment, …
│   │   ├── Services/            # OrderPricing  ← sumber tunggal perhitungan harga
│   │   └── Support/             # PaymentOptions (metode & provider yang diterima)
│   ├── database/                # migrasi, seeder demo
│   ├── resources/js/            # Vue 3 + TypeScript (pages, layouts, components)
│   ├── routes/                  # web.php, settings.php
│   └── tests/                   # Pest (Feature + Unit)
├── docs/                        # banner, screenshot, mockup desain
├── php83/                       # PHP 8.3 portabel untuk Windows
├── run.sh · run.bat             # pintu masuk setup/dev/test
└── .github/workflows/           # CI
```

---

## 🤝 Kontribusi

1. Fork, lalu buat branch: `git checkout -b fitur/nama-fitur`
2. Pastikan lolos sebelum commit: `composer lint` · `php artisan test` · `npm run types:check`
3. Buka Pull Request ke `main`; CI akan berjalan otomatis.

---

## 📄 Lisensi

[MIT](LICENSE) © 2026 Wira & Team Zynx

<div align="center">
<sub>Dibuat oleh <b>Wira</b> dan <b>Team Zynx</b> 🍽️</sub>
</div>
