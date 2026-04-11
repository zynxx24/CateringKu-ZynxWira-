# 🍽️ CateringKu

> Platform katering online berbasis web — menghubungkan pelanggan dengan vendor katering terpercaya.

[![Laravel Tests](https://github.com/your-username/cateringku/actions/workflows/laravel.yml/badge.svg)](https://github.com/your-username/cateringku/actions/workflows/laravel.yml)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-blue?logo=php)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-red?logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green?logo=vue.js)](https://vuejs.org)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

---

## 📋 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Tech Stack](#tech-stack)
- [Fitur](#fitur)
- [Prasyarat](#prasyarat)
- [Instalasi & Setup](#instalasi--setup)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Perintah Tersedia](#perintah-tersedia)
- [Menjalankan Tes](#menjalankan-tes)
- [Akun Test](#akun-test)
- [Struktur Proyek](#struktur-proyek)
- [CI/CD](#cicd)

---

## Tentang Proyek

**CateringKu** adalah aplikasi web full-stack untuk memesan layanan katering secara online. Platform ini memiliki tiga peran pengguna: **Admin**, **Vendor**, dan **Customer**, dengan fitur manajemen menu, pemesanan, promo, ulasan, dan pembayaran.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend** | Laravel 13, PHP 8.3+ |
| **Frontend** | Vue 3, Inertia.js, TypeScript |
| **Styling** | Tailwind CSS 4 |
| **Build Tool** | Vite 8 |
| **Database** | SQLite (dev) / MySQL (prod) |
| **Auth** | Laravel Fortify (2FA support) |
| **Testing** | Pest PHP 4 |
| **Queue** | Laravel Queue |
| **Linting** | ESLint, Prettier, Laravel Pint |

---

## Fitur

- 🛡️ **Autentikasi lengkap** — Login, register, verifikasi email, reset password, 2FA
- 🏪 **Manajemen Vendor** — CRUD menu, kategori, paket katering
- 🛒 **Keranjang & Pemesanan** — Tambah ke cart, checkout, kelola order
- 🎫 **Promo & Diskon** — Kode promo dengan validasi (persentase & nominal)
- ⭐ **Ulasan** — Rating dan review per order yang selesai
- 💳 **Pembayaran** — Rekap pembayaran per order
- 📬 **Pesan Kontak** — Form kontak untuk calon pelanggan
- 📊 **Dashboard Admin** — Pantau seluruh aktivitas platform

---

## Prasyarat

Pastikan sudah terinstal:

- **PHP** `>= 8.3` dengan ekstensi: `pdo`, `pdo_sqlite`, `mbstring`, `zip`, `bcmath`, `curl`
- **Composer** `>= 2.x`
- **Node.js** `>= 18.x` & **NPM**

> 💡 **Windows**: PHP 8.3 portabel tersedia di folder `php83/` di root repo ini. Tambahkan ke PATH atau gunakan `run.bat`.

---

## Instalasi & Setup

### 1. Clone Repository

```bash
git clone https://github.com/your-username/cateringku.git
cd cateringku
```

### 2. Setup Otomatis (Direkomendasikan)

**Windows:**
```bat
run.bat setup
```

**Linux / macOS:**
```bash
bash run.sh setup
```

Perintah ini akan otomatis:
- Install dependency Composer & NPM
- Membuat file `.env` dari `.env.example`
- Generate `APP_KEY`
- Menjalankan migrasi database
- Seed data test

### 3. Setup Manual (Opsional)

```bash
cd CateringKu

# Install dependency
composer install
npm install

# Konfigurasi environment
cp .env.example .env
php artisan key:generate

# Database
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --class=FullTestSeeder

# Build frontend
npm run build
```

---

## Menjalankan Aplikasi

**Windows:**
```bat
run.bat dev
```

**Linux / macOS:**
```bash
bash run.sh dev
```

Atau masuk ke folder `CateringKu` dan jalankan:
```bash
php artisan serve
npm run dev
```

Akses di browser: **http://localhost:8000**

---

## Perintah Tersedia

Semua perintah dijalankan dari root repo (bukan dari folder `CateringKu`):

| Perintah | Windows | Linux/macOS | Keterangan |
|---|---|---|---|
| Setup awal | `run.bat setup` | `bash run.sh setup` | Install + migrate + seed |
| Dev server | `run.bat dev` | `bash run.sh dev` | PHP + Vite sekaligus |
| Install dependency | `run.bat install` | `bash run.sh install` | Composer + NPM |
| Migrasi | `run.bat migrate` | `bash run.sh migrate` | Jalankan migration |
| Seed data | `run.bat seed` | `bash run.sh seed` | Isi data test |
| Fresh database | `run.bat fresh` | `bash run.sh fresh` | ⚠️ Hapus semua data |
| Build produksi | `run.bat build` | `bash run.sh build` | Build frontend assets |
| Clear cache | `run.bat clear` | `bash run.sh clear` | Hapus semua cache |
| Jalankan tes | `run.bat test` | `bash run.sh test` | Test suite (Pest) |
| Hentikan server | `run.bat stop` | `bash run.sh stop` | Kill PHP & Vite |

---

## Menjalankan Tes

```bash
# Dari dalam folder CateringKu
php artisan test
```

Atau via script:
```bat
run.bat test
```

**Hasil:** `40 tests passed (137 assertions)` ✅

Test mencakup:
- Unit tests
- Feature: Auth (login, register, 2FA, email verification, password reset)
- Feature: Dashboard
- Feature: Settings (profile update, password, security)

---

## Akun Test

Jalankan seeder terlebih dahulu: `run.bat seed`

### 🛡️ Admin
| Email | Password | Akses |
|---|---|---|
| admin@cateringku.com | admin123 | `/admin` |

### 🏪 Vendor (password: `vendor123`)
| Nama | Email | Kota |
|---|---|---|
| Dapur Nusantara | nusantara@cateringku.com | Jakarta |
| Sari Rasa Katering | sarirasa@cateringku.com | Bandung |
| Berkah Jaya Catering | berkah@cateringku.com | Surabaya |
| Royal Feast Catering | royal@cateringku.com | Jakarta |
| Padang Bundo Catering | padang@cateringku.com | Medan |

### 👤 Customer (password: `user123`)
Contoh: `ahmad_fauzi@gmail.com`, `siti_nur@gmail.com`, `budi_s@gmail.com`, dll. (15 akun tersedia)

### 🎫 Kode Promo
| Kode | Diskon | Min. Order |
|---|---|---|
| `WELCOME10` | 10% | Rp 100.000 |
| `HEMAT20K` | Rp 20.000 | Rp 200.000 |
| `NUSANTARA15` | 15% | — |
| `ROYAL25` | 25% | — |
| `PADANG10` | 10% | — |
| `WEEKEND30` | Rp 30.000 | Rp 250.000 |

> ⚠️ Jangan gunakan akun ini di environment production!

---

## Struktur Proyek

```
/
├── CateringKu/              # Aplikasi Laravel utama
│   ├── app/
│   │   ├── Actions/         # Business logic actions
│   │   ├── Http/            # Controllers, Middleware, Requests
│   │   ├── Models/          # Eloquent models (User, Vendor, Order, dll)
│   │   └── Providers/       # Service providers
│   ├── database/
│   │   ├── migrations/      # Database migrations
│   │   ├── seeders/         # Database seeders
│   │   └── database.sqlite  # SQLite dev database
│   ├── resources/
│   │   └── js/              # Vue 3 + TypeScript frontend
│   ├── routes/              # Route definitions
│   ├── tests/               # Pest test suite
│   ├── run.bat              # Windows launcher script
│   └── run.sh               # Linux/macOS launcher script
├── php83/                   # PHP 8.3 portable (Windows)
├── run.bat                  # Entry point Windows
├── run.sh                   # Entry point Linux/macOS
└── README.md
```

---

## CI/CD

GitHub Actions otomatis menjalankan test suite di setiap push/PR ke branch `main`.

**Workflow:** `.github/workflows/laravel.yml`

| Step | Detail |
|---|---|
| PHP | 8.4 via `shivammathur/setup-php` |
| Node.js | 20 via `actions/setup-node` |
| Database | SQLite (in-memory) |
| Test runner | `php artisan test` (Pest) |

---

## 📄 Lisensi

Proyek ini menggunakan lisensi [MIT](LICENSE).
