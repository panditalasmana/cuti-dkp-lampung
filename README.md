# 🏛️ Si-Cuti DKP Lampung — Sistem Informasi Pengajuan Cuti Pegawai

Sistem Informasi Pengajuan Cuti Pegawai Berbasis Web resmi untuk **Dinas Kelautan dan Perikanan (DKP) Provinsi Lampung**.

---

## 📌 Ringkasan Audit & Perbaikan Terbaru (Refactoring Log)

Dokumen ini mencatat seluruh perbaikan, audit arsitektur, dan penyelarasan kode yang telah diselesaikan untuk memastikan aplikasi **100% bersih, aman, akurat, dan siap disidangkan/digunakan di dinas**:

### 1. 🧹 Pembersihan & Penyelarasan Migrasi Database (`database/migrations/`)
* **Folder Migrasi Bersih:** Menggabungkan 26 file patch berantakan menjadi **12 file migrasi utama** yang rapi dan terstruktur.
* **Standarisasi NIP:** Mengubah `users.nip` menjadi `varchar(20)` selaras dengan `pegawai.nip`.
* **Perbaikan Nama Kolom (`no_telepon`):** Mengubah kolom `no_hp` pada migration `pegawai` menjadi `no_telepon` agar 100% serasi dengan Model `Pegawai`, Controller, Service, dan Form Blade.
* **Baku Enum Status:** Menyesuaikan enum status `pengajuan_cuti` menjadi `['menunggu', 'disetujui', 'ditolak', 'dibatalkan']` dengan nilai default `'menunggu'`.
* **Integritas Relasi (`onDelete restrict`):** Mengembalikan relasi `pegawai_id` & `jenis_cuti_id` ke `restrict` agar data transaksi pengajuan cuti tidak hilang secara sengaja/tidak sengaja.

### 2. 👥 Standar Baku Format Nama & Gelar Pegawai (EYD V / PUEBI / BKN)
* Seluruh **161 Data Pegawai ASN DKP Lampung** (142 PNS + 19 PPPK) pada `database/data/pegawai_format_resmi_duk.csv` telah dirapikan 100% mengikuti aturan EYD Edisi V / PUEBI / BKN (presisi koma sebelum/antar gelar dan titik di tiap singkatan gelar).

### 3. 🛡️ Keamanan & Evaluasi Dospem
* **Rate Limiting Login (`throttle:5,1`):** Membatasi maksimal 5 kali percobaan login salah per menit per IP address di `routes/web.php`.
* **Mandiri Ubah Password & Rekap Admin Excel:** Pegawai dapat mengubah password secara mandiri di menu Profil, dan Admin dapat mengunduh rekap password aktif seluruh pegawai dalam format Excel ringkas 4 kolom (`NO`, `NIP (ID AKUN)`, `NAMA PEGAWAI`, `PASSWORD`).
* **Alur Login Langsung:** Login langsung mengarahkan pengguna ke Dashboard sesuai role tanpa hambatan alur tambahan.
* **Panduan Production & Backup Database:** Tersedia panduan deployment `APP_ENV=production` & `APP_DEBUG=false` serta skrip Cron Job `mysqldump` harian di `hosting_guide.md`.
* **Automated Feature Testing:** Tersedia pengujian otomatis di `tests/Feature/AuthTest.php` dan `tests/Feature/PengajuanCutiTest.php`.

### 4. 🖨️ Presisi Form PDF Surat Cuti (Standar A4 Kedinasan)
* **Ukuran Kertas A4 Portrait:** Dikonfigurasi presisi `@page { size: A4 portrait; margin: 28pt 64.5pt 24pt 64.5pt; }` dengan font Times New Roman dan border tabel 0.5px.
* **Header Dinamis:** Header permohonan "Kepada Yth." dirender otomatis berdasarkan Pejabat Berwenang yang dipilih pegawai (Gubernur, Sekda, BKD, atau Kepala Dinas).
* **Alasan Penolakan Transparan:** Jika pengajuan ditolak, alasan penolakan dari admin tampil menonjol dengan kotak merah (`alert-danger`).

---

## 🛠️ Teknologi & Arsitektur

* **Framework:** Laravel 10 (PHP 8.1+)
* **Architecture:** Service-Repository Pattern (`App\Services` & `App\Repositories`)
* **Database:** MySQL / MariaDB
* **PDF Engine:** DomPDF (A4 Portrait)
* **Frontend:** Blade, Bootstrap 5.3, Vanilla CSS & JavaScript

---

## 🚀 Perintah Dasar Penggunaan

### 1. Inisialisasi / Refresh Database
```bash
php artisan migrate:fresh --seed
```

### 2. Jalankan Dev Server
```bash
php artisan serve
```

### 3. Jalankan Automated Tests
```bash
php artisan test
```

---

## 🔐 Akun Login Default Demo

| Role | NIP | Password | Halaman Login |
|---|---|---|---|
| **Administrator** | `198501012010011001` | `Admin@DKP2026` | `/admin/login` |
| **Pegawai (Demo)** | `199111152025211022` | `1991` | `/login` |

*(Password default seluruh 161 pegawai adalah 4-digit NIP awal masing-masing).*

---
 Hak Cipta © 2026 Dinas Kelautan dan Perikanan Provinsi Lampung.
