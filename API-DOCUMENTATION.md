# PPDB Online - API Documentation

Base URL: `http://your-domain.com/api/v1`

Semua response menggunakan format JSON dengan struktur:

```json
{
    "success": true|false,
    "message": "Pesan opsional",
    "data": { ... }
}
```

---

## Daftar Isi

1. [Autentikasi](#1-autentikasi)
2. [Public Endpoints](#2-public-endpoints)
3. [Admin Endpoints](#3-admin-endpoints)
4. [Error Handling](#4-error-handling)

---

## 1. Autentikasi

API menggunakan **Laravel Sanctum** (Bearer Token).

Untuk endpoint **admin**, sertakan header:
```
Authorization: Bearer {token}
Accept: application/json
```

### 1.1 Login

```
POST /api/v1/auth/login
```

**Body (JSON):**

| Field       | Type   | Required | Keterangan           |
|-------------|--------|----------|----------------------|
| email       | string | Ya       | Email admin          |
| password    | string | Ya       | Password             |
| device_name | string | Tidak    | Nama device/app      |

**Request:**
```json
{
    "email": "admin@ppdb.test",
    "password": "password",
    "device_name": "mobile-app"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Login berhasil.",
    "data": {
        "user": {
            "id": 1,
            "name": "Administrator",
            "email": "admin@ppdb.test",
            "role": "admin"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

**Response Error (422):**
```json
{
    "message": "Email atau password salah.",
    "errors": {
        "email": ["Email atau password salah."]
    }
}
```

### 1.2 Logout

```
POST /api/v1/auth/logout
```

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "message": "Logout berhasil."
}
```

### 1.3 Get Current User

```
GET /api/v1/auth/user
```

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Administrator",
        "email": "admin@ppdb.test",
        "role": "admin"
    }
}
```

---

## 2. Public Endpoints

Endpoint publik **tidak memerlukan autentikasi**.

### 2.1 Informasi PPDB

```
GET /api/v1/info
```

Menampilkan informasi umum PPDB (nama sekolah, periode pendaftaran, status, dsb).

**Response (200):**
```json
{
    "success": true,
    "data": {
        "nama_sekolah": "SMK Negeri 1",
        "tahun_ajaran": "2026/2027",
        "tanggal_buka": "2026-05-01",
        "tanggal_tutup": "2026-07-01",
        "pendaftaran_dibuka": true,
        "pengumuman": "Selamat datang di PPDB Online...",
        "syarat_pendaftaran": "1. Fotocopy ijazah\n2. ...",
        "contact_person": "081234567890",
        "hasil_diumumkan": false,
        "tanggal_pengumuman": null
    }
}
```

### 2.2 Daftar Jurusan (Publik)

```
GET /api/v1/jurusan
```

Menampilkan jurusan yang **aktif** beserta sisa kuota.

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "kode": "RPL",
            "nama": "Rekayasa Perangkat Lunak",
            "deskripsi": "Jurusan yang mempelajari...",
            "kuota": 72,
            "diterima": 30,
            "sisa_kuota": 42
        }
    ]
}
```

### 2.3 Detail Jurusan (Publik)

```
GET /api/v1/jurusan/{id}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "kode": "RPL",
        "nama": "Rekayasa Perangkat Lunak",
        "deskripsi": "Jurusan yang mempelajari...",
        "kuota": 72,
        "diterima": 30,
        "sisa_kuota": 42
    }
}
```

### 2.4 Submit Pendaftaran

```
POST /api/v1/pendaftaran
```

**Content-Type:** `multipart/form-data` (jika upload berkas) atau `application/json`

**Body:**

| Field           | Type    | Required | Keterangan                         |
|-----------------|---------|----------|------------------------------------|
| nama_lengkap    | string  | Ya       | Maks 150 karakter                  |
| nisn            | string  | Tidak    | Maks 20, unik                      |
| nik             | string  | Tidak    | Maks 20                            |
| jenis_kelamin   | string  | Ya       | `L` atau `P`                       |
| tempat_lahir    | string  | Ya       | Maks 100                           |
| tanggal_lahir   | date    | Ya       | Format `YYYY-MM-DD`, sebelum hari ini |
| agama           | string  | Ya       | Maks 30                            |
| alamat          | string  | Ya       | Alamat lengkap                     |
| no_hp           | string  | Ya       | Maks 20                            |
| email           | string  | Tidak    | Format email, maks 150             |
| asal_sekolah    | string  | Ya       | Maks 150                           |
| tahun_lulus     | string  | Ya       | 4 digit (contoh: 2026)             |
| nilai_rata_rata | number  | Tidak    | 0-100                              |
| nama_ayah       | string  | Ya       | Maks 150                           |
| nama_ibu        | string  | Ya       | Maks 150                           |
| pekerjaan_ayah  | string  | Tidak    | Maks 100                           |
| pekerjaan_ibu   | string  | Tidak    | Maks 100                           |
| no_hp_ortu      | string  | Ya       | Maks 20                            |
| jurusan_id      | integer | Ya       | ID jurusan yang aktif               |
| berkas          | file    | Tidak    | PDF/JPG/PNG, maks 5MB              |

**Request (JSON):**
```json
{
    "nama_lengkap": "Ahmad Fauzi",
    "nisn": "0012345678",
    "nik": "3201234567890001",
    "jenis_kelamin": "L",
    "tempat_lahir": "Jakarta",
    "tanggal_lahir": "2008-05-15",
    "agama": "Islam",
    "alamat": "Jl. Merdeka No. 10, Jakarta",
    "no_hp": "081234567890",
    "email": "ahmad@email.com",
    "asal_sekolah": "SMP Negeri 1 Jakarta",
    "tahun_lulus": "2026",
    "nilai_rata_rata": 85.5,
    "nama_ayah": "Budi Santoso",
    "nama_ibu": "Siti Aminah",
    "pekerjaan_ayah": "Wiraswasta",
    "pekerjaan_ibu": "Ibu Rumah Tangga",
    "no_hp_ortu": "081298765432",
    "jurusan_id": 1
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Pendaftaran berhasil dikirim.",
    "data": {
        "id": 1,
        "no_pendaftaran": "PPDB-2026-00001",
        "nama_lengkap": "Ahmad Fauzi",
        "status": "pending",
        "jurusan": {
            "id": 1,
            "nama": "Rekayasa Perangkat Lunak"
        },
        "created_at": "2026-05-07T10:30:00.000000Z"
    }
}
```

**Response Error - Pendaftaran Ditutup (403):**
```json
{
    "success": false,
    "message": "Pendaftaran sedang ditutup."
}
```

**Response Error - Validasi (422):**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "nisn": ["NISN ini sudah terdaftar."],
        "nama_lengkap": ["The nama lengkap field is required."]
    }
}
```

### 2.5 Cek Status Pendaftaran

```
GET /api/v1/pendaftaran/status/{no_pendaftaran}
```

**Contoh:** `GET /api/v1/pendaftaran/status/PPDB-2026-00001`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "no_pendaftaran": "PPDB-2026-00001",
        "nama_lengkap": "Ahmad Fauzi",
        "jurusan": "Rekayasa Perangkat Lunak",
        "status": "pending",
        "catatan_admin": null,
        "tanggal_daftar": "2026-05-07 10:30:00"
    }
}
```

**Response - Tidak Ditemukan (404):**
```json
{
    "success": false,
    "message": "Nomor pendaftaran tidak ditemukan."
}
```

### 2.6 Pengumuman Hasil Seleksi

```
GET /api/v1/pengumuman
```

**Response - Belum Diumumkan (200):**
```json
{
    "success": true,
    "data": {
        "diumumkan": false,
        "message": "Hasil seleksi belum diumumkan."
    }
}
```

**Response - Sudah Diumumkan (200):**
```json
{
    "success": true,
    "data": {
        "diumumkan": true,
        "tanggal_pengumuman": "2026-07-15",
        "diterima_per_jurusan": [
            {
                "jurusan": "Rekayasa Perangkat Lunak",
                "jumlah": 72,
                "pendaftar": [
                    {
                        "no_pendaftaran": "PPDB-2026-00001",
                        "nama_lengkap": "Ahmad Fauzi",
                        "asal_sekolah": "SMP Negeri 1 Jakarta"
                    }
                ]
            }
        ]
    }
}
```

---

## 3. Admin Endpoints

Semua endpoint admin memerlukan:
- **Header:** `Authorization: Bearer {token}` dan `Accept: application/json`
- **Role:** `admin`

### 3.1 Dashboard

```
GET /api/v1/admin/dashboard
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "stats": {
            "total": 150,
            "pending": 80,
            "diterima": 50,
            "ditolak": 20
        },
        "per_jurusan": [
            {
                "id": 1,
                "kode": "RPL",
                "nama": "Rekayasa Perangkat Lunak",
                "kuota": 72,
                "total_pendaftar": 45,
                "diterima": 30,
                "sisa_kuota": 42
            }
        ],
        "pendaftar_terbaru": [
            {
                "id": 1,
                "no_pendaftaran": "PPDB-2026-00001",
                "nama_lengkap": "Ahmad Fauzi",
                "jurusan_id": 1,
                "status": "pending",
                "created_at": "2026-05-07T10:30:00.000000Z",
                "jurusan": {
                    "id": 1,
                    "nama": "Rekayasa Perangkat Lunak"
                }
            }
        ]
    }
}
```

### 3.2 List Pendaftar (Admin)

```
GET /api/v1/admin/pendaftar
```

**Query Parameters:**

| Parameter  | Type    | Keterangan                                  |
|------------|---------|---------------------------------------------|
| search     | string  | Cari berdasarkan nama, no pendaftaran, NISN, asal sekolah |
| status     | string  | Filter: `pending`, `diterima`, `ditolak`    |
| jurusan_id | integer | Filter berdasarkan ID jurusan               |
| per_page   | integer | Jumlah per halaman (default: 15, maks: 100) |
| page       | integer | Halaman ke-n                                |

**Contoh:** `GET /api/v1/admin/pendaftar?status=pending&jurusan_id=1&per_page=10&page=1`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "no_pendaftaran": "PPDB-2026-00001",
                "nama_lengkap": "Ahmad Fauzi",
                "nisn": "0012345678",
                "jenis_kelamin": "L",
                "asal_sekolah": "SMP Negeri 1 Jakarta",
                "status": "pending",
                "jurusan": {
                    "id": 1,
                    "nama": "Rekayasa Perangkat Lunak"
                },
                "created_at": "2026-05-07T10:30:00.000000Z"
            }
        ],
        "last_page": 5,
        "per_page": 15,
        "total": 75,
        "first_page_url": "...",
        "last_page_url": "...",
        "next_page_url": "...",
        "prev_page_url": null
    }
}
```

### 3.3 Detail Pendaftar (Admin)

```
GET /api/v1/admin/pendaftar/{id}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "no_pendaftaran": "PPDB-2026-00001",
        "nama_lengkap": "Ahmad Fauzi",
        "nisn": "0012345678",
        "nik": "3201234567890001",
        "jenis_kelamin": "L",
        "tempat_lahir": "Jakarta",
        "tanggal_lahir": "2008-05-15",
        "agama": "Islam",
        "alamat": "Jl. Merdeka No. 10, Jakarta",
        "no_hp": "081234567890",
        "email": "ahmad@email.com",
        "asal_sekolah": "SMP Negeri 1 Jakarta",
        "tahun_lulus": "2026",
        "nilai_rata_rata": "85.50",
        "nama_ayah": "Budi Santoso",
        "nama_ibu": "Siti Aminah",
        "pekerjaan_ayah": "Wiraswasta",
        "pekerjaan_ibu": "Ibu Rumah Tangga",
        "no_hp_ortu": "081298765432",
        "jurusan_id": 1,
        "status": "pending",
        "catatan_admin": null,
        "berkas_path": "berkas/abc123.pdf",
        "berkas_url": "http://your-domain.com/storage/berkas/abc123.pdf",
        "jurusan": {
            "id": 1,
            "kode": "RPL",
            "nama": "Rekayasa Perangkat Lunak"
        },
        "created_at": "2026-05-07T10:30:00.000000Z",
        "updated_at": "2026-05-07T10:30:00.000000Z"
    }
}
```

### 3.4 Update Status Pendaftar

```
PUT /api/v1/admin/pendaftar/{id}
```

**Body (JSON):**

| Field         | Type   | Required | Keterangan                          |
|---------------|--------|----------|-------------------------------------|
| status        | string | Ya       | `pending`, `diterima`, atau `ditolak` |
| catatan_admin | string | Tidak    | Catatan dari admin                  |

**Request:**
```json
{
    "status": "diterima",
    "catatan_admin": "Nilai memenuhi syarat."
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Status pendaftar berhasil diperbarui.",
    "data": {
        "id": 1,
        "status": "diterima",
        "catatan_admin": "Nilai memenuhi syarat."
    }
}
```

### 3.5 Hapus Pendaftar

```
DELETE /api/v1/admin/pendaftar/{id}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Pendaftar berhasil dihapus."
}
```

### 3.6 Bulk Action Pendaftar

```
POST /api/v1/admin/pendaftar/bulk
```

**Body (JSON):**

| Field  | Type   | Required | Keterangan                               |
|--------|--------|----------|------------------------------------------|
| action | string | Ya       | `terima`, `tolak`, `reset`, atau `hapus` |
| ids    | array  | Ya       | Array ID pendaftar                       |

**Request:**
```json
{
    "action": "terima",
    "ids": [1, 2, 3, 5]
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "4 pendaftar diterima.",
    "affected": 4
}
```

### 3.7 List Jurusan (Admin)

```
GET /api/v1/admin/jurusan
```

Menampilkan semua jurusan termasuk yang non-aktif, dengan jumlah pendaftar.

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "kode": "RPL",
            "nama": "Rekayasa Perangkat Lunak",
            "deskripsi": "Jurusan yang mempelajari...",
            "kuota": 72,
            "aktif": true,
            "pendaftar_count": 45,
            "diterima_count": 30,
            "created_at": "2026-01-01T00:00:00.000000Z",
            "updated_at": "2026-01-01T00:00:00.000000Z"
        }
    ]
}
```

### 3.8 Tambah Jurusan

```
POST /api/v1/admin/jurusan
```

**Body (JSON):**

| Field     | Type    | Required | Keterangan         |
|-----------|---------|----------|--------------------|
| kode      | string  | Ya       | Unik, maks 10      |
| nama      | string  | Ya       | Maks 150           |
| deskripsi | string  | Tidak    | Deskripsi jurusan  |
| kuota     | integer | Ya       | Minimal 0          |
| aktif     | boolean | Tidak    | Default: true      |

**Request:**
```json
{
    "kode": "TBSM",
    "nama": "Teknik Bisnis Sepeda Motor",
    "deskripsi": "Jurusan teknik sepeda motor",
    "kuota": 36,
    "aktif": true
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Jurusan berhasil ditambahkan.",
    "data": {
        "id": 6,
        "kode": "TBSM",
        "nama": "Teknik Bisnis Sepeda Motor",
        "deskripsi": "Jurusan teknik sepeda motor",
        "kuota": 36,
        "aktif": true
    }
}
```

### 3.9 Update Jurusan

```
PUT /api/v1/admin/jurusan/{id}
```

Body sama dengan **Tambah Jurusan**.

**Response (200):**
```json
{
    "success": true,
    "message": "Jurusan berhasil diperbarui.",
    "data": { ... }
}
```

### 3.10 Hapus Jurusan

```
DELETE /api/v1/admin/jurusan/{id}
```

> Tidak bisa dihapus jika sudah ada pendaftar terkait.

**Response (200):**
```json
{
    "success": true,
    "message": "Jurusan berhasil dihapus."
}
```

**Response Error (422):**
```json
{
    "success": false,
    "message": "Jurusan tidak dapat dihapus karena sudah memiliki pendaftar."
}
```

### 3.11 Get Pengaturan PPDB

```
GET /api/v1/admin/pengaturan
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nama_sekolah": "SMK Negeri 1",
        "tahun_ajaran": "2026/2027",
        "tanggal_buka": "2026-05-01",
        "tanggal_tutup": "2026-07-01",
        "pendaftaran_dibuka": true,
        "pengumuman": "...",
        "syarat_pendaftaran": "...",
        "contact_person": "081234567890",
        "hasil_diumumkan": false,
        "tanggal_pengumuman": null
    }
}
```

### 3.12 Update Pengaturan PPDB

```
PUT /api/v1/admin/pengaturan
```

**Body (JSON):**

| Field               | Type    | Required | Keterangan                   |
|---------------------|---------|----------|------------------------------|
| nama_sekolah        | string  | Ya       | Maks 150                    |
| tahun_ajaran        | string  | Ya       | Maks 9 (contoh: 2026/2027)  |
| tanggal_buka        | date    | Ya       | Format `YYYY-MM-DD`         |
| tanggal_tutup       | date    | Ya       | Harus >= tanggal_buka       |
| pendaftaran_dibuka  | boolean | Tidak    | Buka/tutup pendaftaran      |
| pengumuman          | string  | Tidak    | Teks pengumuman             |
| syarat_pendaftaran  | string  | Tidak    | Teks syarat                 |
| contact_person      | string  | Tidak    | Maks 100                    |
| hasil_diumumkan     | boolean | Tidak    | Publish hasil seleksi       |
| tanggal_pengumuman  | date    | Tidak    | Tanggal pengumuman          |

**Response (200):**
```json
{
    "success": true,
    "message": "Pengaturan PPDB berhasil disimpan.",
    "data": { ... }
}
```

---

## 4. Error Handling

### 4.1 Validation Error (422)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field_name": ["Pesan error validasi."]
    }
}
```

### 4.2 Unauthorized (401)

Terjadi ketika token tidak valid atau tidak disertakan.

```json
{
    "message": "Unauthenticated."
}
```

### 4.3 Forbidden (403)

Terjadi ketika user tidak memiliki akses admin.

```json
{
    "message": "Akses ditolak. Halaman ini hanya untuk admin."
}
```

### 4.4 Not Found (404)

```json
{
    "message": "No query results for model [App\\Models\\Pendaftar] 999."
}
```

---

## Contoh Penggunaan (cURL)

### Login & Dapatkan Token

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@ppdb.test","password":"password"}'
```

### Ambil Data Jurusan (Publik)

```bash
curl http://localhost:8000/api/v1/jurusan \
  -H "Accept: application/json"
```

### Submit Pendaftaran

```bash
curl -X POST http://localhost:8000/api/v1/pendaftaran \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nama_lengkap": "Ahmad Fauzi",
    "jenis_kelamin": "L",
    "tempat_lahir": "Jakarta",
    "tanggal_lahir": "2008-05-15",
    "agama": "Islam",
    "alamat": "Jl. Merdeka No. 10",
    "no_hp": "081234567890",
    "asal_sekolah": "SMP Negeri 1",
    "tahun_lulus": "2026",
    "nama_ayah": "Budi Santoso",
    "nama_ibu": "Siti Aminah",
    "no_hp_ortu": "081298765432",
    "jurusan_id": 1
  }'
```

### Submit Pendaftaran dengan File Upload

```bash
curl -X POST http://localhost:8000/api/v1/pendaftaran \
  -H "Accept: application/json" \
  -F "nama_lengkap=Ahmad Fauzi" \
  -F "jenis_kelamin=L" \
  -F "tempat_lahir=Jakarta" \
  -F "tanggal_lahir=2008-05-15" \
  -F "agama=Islam" \
  -F "alamat=Jl. Merdeka No. 10" \
  -F "no_hp=081234567890" \
  -F "asal_sekolah=SMP Negeri 1" \
  -F "tahun_lulus=2026" \
  -F "nama_ayah=Budi Santoso" \
  -F "nama_ibu=Siti Aminah" \
  -F "no_hp_ortu=081298765432" \
  -F "jurusan_id=1" \
  -F "berkas=@/path/to/document.pdf"
```

### Cek Status Pendaftaran

```bash
curl http://localhost:8000/api/v1/pendaftaran/status/PPDB-2026-00001 \
  -H "Accept: application/json"
```

### Ambil Data Dashboard (Admin)

```bash
curl http://localhost:8000/api/v1/admin/dashboard \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Accept: application/json"
```

### Update Status Pendaftar (Admin)

```bash
curl -X PUT http://localhost:8000/api/v1/admin/pendaftar/1 \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status":"diterima","catatan_admin":"Nilai memenuhi syarat."}'
```

### Bulk Terima Pendaftar (Admin)

```bash
curl -X POST http://localhost:8000/api/v1/admin/pendaftar/bulk \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"action":"terima","ids":[1,2,3]}'
```

---

## Contoh Penggunaan (JavaScript / Fetch)

### Login

```javascript
const response = await fetch('/api/v1/auth/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    body: JSON.stringify({
        email: 'admin@ppdb.test',
        password: 'password',
    }),
});

const { data } = await response.json();
const token = data.token;
// Simpan token untuk request selanjutnya
localStorage.setItem('ppdb_token', token);
```

### Fetch Data dengan Token

```javascript
const token = localStorage.getItem('ppdb_token');

const response = await fetch('/api/v1/admin/pendaftar?status=pending&page=1', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
    },
});

const { data } = await response.json();
console.log(data.data); // array pendaftar
console.log(data.total); // total records
```

### Submit Pendaftaran dari Frontend

```javascript
const formData = new FormData();
formData.append('nama_lengkap', 'Ahmad Fauzi');
formData.append('jenis_kelamin', 'L');
formData.append('tempat_lahir', 'Jakarta');
formData.append('tanggal_lahir', '2008-05-15');
formData.append('agama', 'Islam');
formData.append('alamat', 'Jl. Merdeka No. 10');
formData.append('no_hp', '081234567890');
formData.append('asal_sekolah', 'SMP Negeri 1');
formData.append('tahun_lulus', '2026');
formData.append('nama_ayah', 'Budi Santoso');
formData.append('nama_ibu', 'Siti Aminah');
formData.append('no_hp_ortu', '081298765432');
formData.append('jurusan_id', 1);
formData.append('berkas', fileInput.files[0]); // optional file

const response = await fetch('/api/v1/pendaftaran', {
    method: 'POST',
    headers: { 'Accept': 'application/json' },
    body: formData,
});

const result = await response.json();
if (result.success) {
    console.log('No Pendaftaran:', result.data.no_pendaftaran);
}
```

---

## Ringkasan Endpoint

| Method | Endpoint                                  | Auth    | Keterangan                   |
|--------|-------------------------------------------|---------|------------------------------|
| POST   | `/api/v1/auth/login`                      | -       | Login, dapatkan token        |
| POST   | `/api/v1/auth/logout`                     | Bearer  | Logout, hapus token          |
| GET    | `/api/v1/auth/user`                       | Bearer  | Data user yang login         |
| GET    | `/api/v1/info`                            | -       | Info PPDB                    |
| GET    | `/api/v1/jurusan`                         | -       | Daftar jurusan aktif         |
| GET    | `/api/v1/jurusan/{id}`                    | -       | Detail jurusan               |
| POST   | `/api/v1/pendaftaran`                     | -       | Submit pendaftaran baru      |
| GET    | `/api/v1/pendaftaran/status/{no}`         | -       | Cek status pendaftaran       |
| GET    | `/api/v1/pengumuman`                      | -       | Hasil pengumuman             |
| GET    | `/api/v1/admin/dashboard`                 | Admin   | Statistik dashboard          |
| GET    | `/api/v1/admin/pendaftar`                 | Admin   | List pendaftar + filter      |
| GET    | `/api/v1/admin/pendaftar/{id}`            | Admin   | Detail pendaftar             |
| PUT    | `/api/v1/admin/pendaftar/{id}`            | Admin   | Update status                |
| DELETE | `/api/v1/admin/pendaftar/{id}`            | Admin   | Hapus pendaftar              |
| POST   | `/api/v1/admin/pendaftar/bulk`            | Admin   | Bulk action                  |
| GET    | `/api/v1/admin/jurusan`                   | Admin   | List semua jurusan           |
| POST   | `/api/v1/admin/jurusan`                   | Admin   | Tambah jurusan               |
| PUT    | `/api/v1/admin/jurusan/{id}`              | Admin   | Update jurusan               |
| DELETE | `/api/v1/admin/jurusan/{id}`              | Admin   | Hapus jurusan                |
| GET    | `/api/v1/admin/pengaturan`                | Admin   | Get pengaturan               |
| PUT    | `/api/v1/admin/pengaturan`                | Admin   | Update pengaturan            |
