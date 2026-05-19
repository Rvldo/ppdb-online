<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" />
  <img src="https://img.shields.io/badge/Inertia.js-2-9553E9?style=for-the-badge&logo=inertia&logoColor=white" />
  <img src="https://img.shields.io/badge/Claude_AI-Anthropic-6B4FBB?style=for-the-badge&logo=anthropic&logoColor=white" />
</p>

# PPDB Online

Aplikasi **PPDB (Penerimaan Peserta Didik Baru)** modern, lengkap, dan siap pakai untuk sekolah atau instansi manapun. Dibangun dengan **Laravel 10 + Vue 3 + Inertia.js + Tailwind CSS** dan terintegrasi **AI Assistant (Claude by Anthropic)**.

> **Multi-instansi ready** — Super Admin bisa mengubah logo, warna tema, dan seluruh branding tanpa coding.

---

## Fitur Utama

| Fitur | Keterangan |
|-------|-----------|
| Pendaftaran Online | Form multi-step dengan validasi lengkap & upload berkas |
| AI Chatbot (Claude) | Asisten virtual 24/7 yang menjawab pertanyaan seputar PPDB |
| AI Form Tips | Tips otomatis saat mengisi formulir pendaftaran |
| Cek Status | Siswa bisa cek status pendaftaran via nomor registrasi |
| Pengumuman Hasil | Halaman pengumuman per jurusan (bisa di-toggle on/off) |
| Dashboard Admin | Statistik real-time, grafik per jurusan, pendaftar terbaru |
| Manajemen Pendaftar | Filter, search, detail, update status, bulk action, export CSV |
| Manajemen Jurusan | CRUD jurusan dengan kuota & progress bar |
| Super Admin Branding | Upload logo, ganti warna tema (8 preset + custom), favicon, hero background |
| REST API Lengkap | 23 endpoint (publik + admin) dengan dokumentasi |
| Responsive | Mobile-friendly semua halaman |
| Animasi Modern | Particle background, animated counters, glassmorphism, transitions |

---

## Screenshot

### Halaman Utama (Hero)
- Particle network background
- Live countdown timer
- Animated statistics

### Form Pendaftaran
- 4-step wizard dengan progress bar
- AI-assisted form tips

### Admin Panel
- Modern sidebar dengan gradient
- Dashboard statistik real-time

### Branding (Super Admin)
- Color picker + 8 preset tema
- Logo & favicon upload
- Live preview

---

## Persyaratan Sistem

| Software | Versi |
|----------|-------|
| PHP | 8.1+ (dengan ekstensi `pdo_mysql`) |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |
| MySQL | 8.x / MariaDB 10.x |

---

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/YOUR_USERNAME/ppdb.git
cd ppdb
```

### 2. Install Otomatis (Satu Perintah)

```bash
composer setup
```

Script ini secara otomatis menjalankan:
1. `composer install` — Install dependensi PHP
2. Copy `.env.example` → `.env`
3. `php artisan key:generate` — Generate app key
4. Create database MySQL
5. `php artisan migrate:fresh --seed` — Buat tabel & data awal
6. `php artisan storage:link` — Link storage untuk upload file
7. `npm install` — Install dependensi frontend
8. `npm run build` — Build asset Vue/Tailwind

### 3. Konfigurasi Environment

Sebelum menjalankan, edit `.env` sesuai kebutuhan:

```env
# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ppdb_db
DB_USERNAME=root
DB_PASSWORD=

# Anthropic AI (opsional, untuk chatbot)
ANTHROPIC_API_KEY=sk-ant-api03-xxxxx
```

### 4. Jalankan Aplikasi

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server (hot reload)
npm run dev
```

Buka **http://127.0.0.1:8000** di browser.

---

## Akun Default

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Super Admin** | `superadmin@ppdb.test` | `password` | Semua + Tampilan & Branding |
| **Admin** | `admin@ppdb.test` | `password` | Dashboard, Pendaftar, Jurusan, Pengaturan |

---

## Cara Penggunaan

### Untuk Super Admin (Setup Awal)

1. **Login** sebagai Super Admin (`superadmin@ppdb.test`)
2. Buka menu **Tampilan** di sidebar
3. **Upload Logo** sekolah/instansi (PNG/JPG/SVG)
4. **Pilih Warna Tema** — pilih preset atau kustom via color picker
5. Isi **Informasi Instansi** (singkatan, alamat, email, website)
6. Opsional: Upload **background hero** dan custom title
7. Klik **Simpan** — seluruh tampilan langsung berubah

### Untuk Admin (Operasional Harian)

1. **Login** sebagai Admin
2. Buka **Pengaturan** → isi nama sekolah, tahun ajaran, periode pendaftaran, syarat
3. **Buka pendaftaran** (centang "Pendaftaran dibuka")
4. Pantau **Dashboard** untuk statistik real-time
5. Kelola **Pendaftar** — review, terima, tolak, bulk action
6. Kelola **Jurusan** — tambah/edit/nonaktifkan jurusan, atur kuota
7. Saat seleksi selesai: centang **"Tampilkan hasil"** di Pengaturan untuk publish pengumuman

### Untuk Calon Siswa

1. Buka website PPDB
2. Klik **Daftar Sekarang**
3. Isi form 4 langkah: Data Pribadi → Akademik → Orang Tua → Upload Berkas
4. Simpan **Nomor Pendaftaran** (format: `PPDB-2026-00001`)
5. Cek status kapan saja di halaman **Cek Status**
6. Butuh bantuan? Klik tombol **AI Chat** di pojok kanan bawah

### Menggunakan AI Chatbot

Chatbot otomatis mengetahui:
- Jurusan yang tersedia beserta sisa kuota
- Syarat dan prosedur pendaftaran
- Periode dan deadline
- Informasi kontak

Tambahkan `ANTHROPIC_API_KEY` di `.env` untuk mengaktifkan fitur AI.

---

## REST API

API tersedia di `/api/v1/` dengan 23 endpoint. Dokumentasi lengkap ada di file [`API-DOCUMENTATION.md`](API-DOCUMENTATION.md).

### Endpoint Publik (Tanpa Auth)

```
GET    /api/v1/info                           → Info PPDB
GET    /api/v1/jurusan                        → Daftar jurusan
POST   /api/v1/pendaftaran                    → Submit pendaftaran
GET    /api/v1/pendaftaran/status/{no}        → Cek status
GET    /api/v1/pengumuman                     → Hasil pengumuman
POST   /api/v1/chat                           → AI Chatbot
```

### Endpoint Admin (Bearer Token)

```
POST   /api/v1/auth/login                     → Login, dapat token
GET    /api/v1/admin/dashboard                → Statistik
GET    /api/v1/admin/pendaftar                → List pendaftar
PUT    /api/v1/admin/pendaftar/{id}           → Update status
POST   /api/v1/admin/pendaftar/bulk           → Bulk action
... dan lainnya (lihat API-DOCUMENTATION.md)
```

---

## Script Composer

| Perintah | Fungsi |
|----------|--------|
| `composer setup` | Bootstrap penuh dari nol |
| `composer fresh` | Reset DB + migrate + seed |
| `composer db:create` | Buat database MySQL saja |

---

## Tech Stack

- **Backend:** Laravel 10, PHP 8.1+
- **Frontend:** Vue 3, Inertia.js v2, Tailwind CSS 3
- **Database:** MySQL 8 / MariaDB
- **AI:** Claude (Anthropic API) — model `claude-haiku-4-5`
- **Auth:** Laravel Sanctum (API tokens)
- **Build:** Vite 5

---

## Struktur Data Awal (Seeder)

- 1 Super Admin + 1 Admin
- 5 Jurusan: RPL, TKJ, MM, AKL, BDP
- Konfigurasi PPDB default
- Dummy pendaftar untuk testing

---

## Deploy ke Production

```bash
# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set environment
# APP_ENV=production
# APP_DEBUG=false
```

---

## Donate

Jika project ini bermanfaat, kamu bisa mendukung pengembangan lebih lanjut:

<p align="center">
  <a href="https://saweria.co/jemmymongi">
    <img src="https://img.shields.io/badge/Saweria-Donate-FFD700?style=for-the-badge&logo=buy-me-a-coffee&logoColor=black" />
  </a>
  <a href="https://trakteer.id/jemmymongi">
    <img src="https://img.shields.io/badge/Trakteer-Donate-C02433?style=for-the-badge&logo=buy-me-a-coffee&logoColor=white" />
  </a>
  <a href="https://paypal.me/jemmymongi">
    <img src="https://img.shields.io/badge/PayPal-Donate-003087?style=for-the-badge&logo=paypal&logoColor=white" />
  </a>
</p>

<p align="center">
  <b>Atau transfer langsung:</b><br/>
  BCA: <code>0123456789</code> a.n. Jemmy Mongi<br/>
  Dana/OVO/GoPay: <code>081234567890</code>
</p>

---

## Butuh Bantuan Setup?

Kalau kamu butuh bantuan instalasi, kustomisasi, atau deploy ke server, hubungi saya langsung:

<table>
  <tr>
    <td><b>Nama</b></td>
    <td>Jemmy Mongi</td>
  </tr>
  <tr>
    <td><b>WhatsApp</b></td>
    <td><a href="https://wa.me/6287848513665">+62 878-4851-3665</a></td>
  </tr>
  <tr>
    <td><b>Email</b></td>
    <td><a href="mailto:jemz.mongi27@gmail.com">jemz.mongi27@gmail.com</a></td>
  </tr>
</table>

> Saya bisa bantu: instalasi di VPS/hosting, konfigurasi domain, setup Anthropic API, kustomisasi fitur tambahan, dan lain-lain.

---

## Lisensi

MIT License — bebas digunakan, dimodifikasi, dan didistribusikan.

---

<p align="center">
  <b>Made with ❤️ by Jemmy Mongi</b><br/>
  <sub>Powered by Laravel, Vue.js, dan Claude AI</sub>
</p>
