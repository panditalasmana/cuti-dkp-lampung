# Panduan Deployment Server Production & Backup Database DKP Lampung

Dokumen ini berisi panduan resmi deployment aplikasi **Si-Cuti DKP Lampung** ke server produksi Dinas Kelautan dan Perikanan Provinsi Lampung, serta panduan keamanan dan pemeliharaan otomatis.

---

## 🛡️ 1. Konfigurasi Keamanan Server Production (.env)

Sebelum aplikasi dibuka untuk seluruh pegawai ASN DKP Lampung di lingkungan server produksi, pastikan berkas `.env` diubah ke mode aman:

```env
APP_NAME="Si-Cuti DKP Lampung"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cuti.dkp.lampungprov.go.id
```

> [!CAUTION]
> Jangan biarkan `APP_DEBUG=true` di server produksi, karena pesan kesalahan dapat menampilkan potongan query SQL dan password ke publik jika terjadi masalah koneksi.

Setelah mengubah file `.env`, jalankan perintah berikut di terminal server:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔒 2. Fitur Keamanan Pengguna yang Aktif

1. **Rate Limiting Login (`throttle:5,1`)**:
   Membatasi maksimal 5 kali percobaan login salah per menit per alamat IP. Jika lebih dari 5 kali, akun/IP akan diblokir sementara selama 60 detik untuk mencegah serangan *brute-force*.

2. **Wajib Ganti Password saat Login Pertama (`must_change_password`)**:
   Setiap pegawai yang baru pertama kali login menggunakan 4-digit NIP awal akan langsung diarahakan ke halaman pengubahan password baru sebelum dapat mengakses fitur dashboard.

---

## 💾 3. Otomatisasi Backup Database Terjadwal (Cron Job)

Untuk menjamin keamanan data pengajuan cuti dan data 161 pegawai ASN, siapkan *Cron Job* otomatis di server Linux DKP dengan perintah `mysqldump` harian:

### Langkah Setup Backup Otomatis:

1. Buat folder backup di server:
   ```bash
   mkdir -p /var/backups/db_cuti_dkp
   ```

2. Buka crontab server:
   ```bash
   crontab -e
   ```

3. Tambahkan baris berikut untuk backup otomatis setiap pukul 01:00 malam:
   ```cron
   0 1 * * * mysqldump -u root -p'PASSWORD_DB_ANDA' db_cuti_dkp | gzip > /var/backups/db_cuti_dkp/backup_cuti_$(date +\%Y\%m\%d).sql.gz
   ```

4. Hapus otomatis backup tua lebih dari 30 hari:
   ```cron
   0 2 * * * find /var/backups/db_cuti_dkp/ -type f -name "*.sql.gz" -mtime +30 -delete
   ```

---

## 🧪 4. Menjalankan Automated Unit & Feature Test

Untuk memastikan seluruh logika pengajuan cuti, perhitungan kuota, dan autentikasi berjalan 100% sempurna tanpa bug, jalankan pengujian otomatis Laravel:

```bash
php artisan test
```
