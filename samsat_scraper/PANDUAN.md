# PANDUAN PENGGUNAAN — Cek Samsat Otomatis
# ==========================================

## 📁 File dalam Folder Ini
| File | Keterangan |
|------|-----------|
| `cek_samsat.py` | **Skrip utama** — jalankan ini untuk cek otomatis |
| `buat_contoh_excel.py` | Pembuat file Excel template (jalankan sekali di awal) |
| `daftar_nopol.xlsx` | File input berisi daftar NOPOL yang ingin dicek |
| `hasil_samsat_[tanggal].xlsx` | File output hasil cek (dibuat otomatis) |

---

## 🚀 Cara Pakai (Pertama Kali)

### Langkah 1 — Install Python
Jika belum punya Python, download di: https://www.python.org/downloads/
Pastikan centang ✅ "Add Python to PATH" saat instalasi.

### Langkah 2 — Buka Terminal / Command Prompt
Klik kanan di dalam folder `samsat_scraper` → "Open in Terminal"
atau buka CMD, lalu ketik:
```
cd "C:\Users\pandi\Downloads\Cuti-DKP-Lampung\samsat_scraper"
```

### Langkah 3 — Buat File Excel Template
```
python buat_contoh_excel.py
```
File `daftar_nopol.xlsx` akan muncul.

### Langkah 4 — Isi Data NOPOL
Buka `daftar_nopol.xlsx`, hapus contoh, isi dengan NOPOL Anda.
Format NOPOL: **B5834BWN** (tanpa spasi)

### Langkah 5 — Jalankan Skrip Utama
```
python cek_samsat.py
```

---

## ⚙️ Cara Kerja Program

```
Program baca Excel
       ↓
Browser Chrome terbuka otomatis
       ↓
Program isi NOPOL di form Samsat
       ↓
⚠️ TUGAS ANDA: Klik "Saya bukan robot"
       ↓
Program otomatis klik tombol "Cari"
       ↓
Program ambil semua data hasil
       ↓
Data tersimpan ke Excel hasil
       ↓
Lanjut ke NOPOL berikutnya...
```

---

## 📊 Data yang Diambil Otomatis
- ✅ Nama Pemilik
- ✅ Alamat
- ✅ No. Rangka
- ✅ No. Mesin
- ✅ Merek / Type
- ✅ Warna Kendaraan
- ✅ Bahan Bakar / Cylinder
- ✅ Nilai Jual
- ✅ Masa Berlaku STNK
- ✅ Jatuh Tempo Pajak
- ✅ PKB Pokok & Denda
- ✅ SWDKLLJ & Denda
- ✅ Total Tagihan
- ✅ Status Pajak
- ✅ No BPKB
- ✅ Warna TNKB

---

## ❓ Pertanyaan Umum

**Q: Format NOPOL seperti apa?**
A: Tulis tanpa spasi, huruf kapital. Contoh: `B5834BWN`, `B1234ABC`

**Q: Program membutuhkan internet?**
A: Ya, karena mengakses website Samsat Jakarta.

**Q: Apakah Chrome harus terinstall?**
A: Ya. Download di: https://www.google.com/chrome/

**Q: Berapa lama per NOPOL?**
A: Tergantung seberapa cepat Anda klik CAPTCHA (~10-30 detik per NOPOL).

---

## ⚠️ Catatan Penting
- Program ini hanya membaca data yang **sudah tampil publik** di website resmi Samsat.
- Gunakan hanya untuk keperluan resmi dan sesuai ketentuan yang berlaku.
