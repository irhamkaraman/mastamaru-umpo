<div align="center">
  <img src="public/img/logo_Universitas-Muhammadiyah-Ponorogo-1.png" alt="Logo UMPO" width="150"/>
  <h1>🎓 Presensi MASTAMARU UMPO 2026</h1>
  <p>Sistem Presensi Modern & Sistem Penilaian Kehadiran untuk kegiatan Masa Ta'aruf Mahasiswa Baru Universitas Muhammadiyah Ponorogo</p>
  
  ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
  ![Filament](https://img.shields.io/badge/Filament-FFA611?style=for-the-badge&logo=filament&logoColor=white)
  ![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
</div>

---

## 🌟 Fitur Unggulan
#TEST

- 🛡️ **Admin Panel Dinamis**: Antarmuka responsif yang dibangun dengan Filament v3, dilengkapi dengan sistem *Role & Permission* (Filament Shield).
- 🔗 **Integrasi & Sinkronisasi API Cepat (Smart Sync)**: 
  - Mendukung pemetaan dinamis (*dynamic mapping*) dari API UMPO.
  - **Tarik Mahasiswa Aktif UMPO**: Menarik data mahasiswa tahun 2026 super cepat dengan *batch chunking* (~4 detik untuk ribuan data), dilengkapi nomor WhatsApp, penerjemahan nama Fakultas & Prodi resmi, dan realtime progress bar.
- 🏆 **Sistem Penilaian Poin Presensi (Sesuai SOP Sertifikat)**:
  - Perhitungan poin otomatis per sesi (Sesi Datang: Hadir=10, Terlambat=8, Sakit=6, Izin=5; Sesi Pulang: Hadir=10, Sakit=7, Izin=5).
  - Matriks kehadiran 5 hari kegiatan (Total Poin / 100, Persentase Nilai, Grade A/B/C/D, dan Status Kelulusan).
  - Halaman terpisah khusus **Riwayat Poin & Nilai Peserta** yang elegan dengan dukungan Dark Mode penuh.
- 🎲 **Distribusi Kelompok Otomatis**: Fitur "Bagi Kelompok Acak" yang mendistribusikan peserta yang belum punya kelompok secara rata dan adil ke semua pendamping.
- 📱 **QR Code / Barcode Presensi**: Pembuatan kode unik otomatis untuk mempercepat proses absensi via scan kamera pendamping.
- 📊 **Manajemen Master Data Lengkap**: Pengelolaan Peserta, Pendamping (termasuk nomor WhatsApp & akses mandiri), dan Kelompok.
- 📥 **Import & Export Excel Canggih**:
  - Export Excel/CSV Peserta lengkap dengan Nomor WA, Total Poin, Grade, dan otomatis menyesuaikan Filter aktif (Fakultas, Prodi, Kelompok, Pendamping).
  - Export & Import Pendamping lengkap dengan Nomor WhatsApp.
  - Form Pendaftaran Manual `/remake` yang fleksibel tanpa batasan perangkat (*no device lock*).

---

## ⌨️ Perintah Konsol Artisan (CLI Commands)

Tersedia beberapa perintah CLI khusus untuk mempercepat sinkronisasi dan pencocokan data:

### 1. Sinkronisasi Data Mahasiswa Baru 2026 dari API UMPO
Menghubungkan ke API UMPO, mengambil token otentikasi dinamis, dan melakukan *upsert batch chunking* data mahasiswa tahun 2026 lengkap dengan nomor telepon:
```bash
php artisan umpo:sync-mahasiswa
```

### 2. Pencocokan & Sinkronisasi Data Mentah API (`ApiDataRecord`)
Menarik data mentah dari endpoint API konfigurasi ke tabel penampungan perbandingan:
```bash
php artisan api:sync-records
```

### 3. Sinkronisasi & Reset Hak Akses (Shield)
Memperbarui seluruh permission Filament Shield saat ada resource atau permission baru, membersihkan cache secara total, dan memastikan super_admin mendapat akses (sangat disarankan saat deploy ke production):
```bash
php artisan app:sync-permissions
```

---

## 📸 Cuplikan Layar (Screenshots)

### 1. Dashboard Admin Panel
Beranda utama yang bersih dan elegan untuk memantau status presensi dan mengelola aplikasi.
![Dashboard Admin Panel](public/img/dashboard_panel_admin.png)

### 2. Generate Barcode & Presensi
Fitur pembuatan barcode/QR untuk setiap peserta guna mempercepat antrean presensi.
![QR Code Attendance](public/img/get_qr_code_attendance.png)

### 3. Informasi Detail Presensi
Laporan komprehensif mengenai riwayat dan detail kehadiran mahasiswa.
![Information Detail Attendance](public/img/infromation_detail_attandance.png)

---

## 🚀 Panduan Instalasi (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di mesin lokal Anda:

### Persyaratan Sistem
- **PHP** >= 8.2
- **Composer** (Package Manager)
- **Node.js & NPM**
- **MySQL / MariaDB**

### Langkah Instalasi

```bash
# 1. Clone Repositori
git clone https://github.com/irhamkaraman/mastamaru-umpo.git
cd mastamaru-umpo

# 2. Install Dependensi PHP
composer install

# 3. Setup File Environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi Database
# Buka file .env dan ubah pengaturan DB_DATABASE, DB_USERNAME, dan DB_PASSWORD sesuai dengan server lokal Anda.

# 5. Jalankan Migrasi Data
php artisan migrate --seed

# 6. Install Dependensi Frontend
npm install
npm run build

# 7. Jalankan Server
php artisan serve
```

### Akses Aplikasi
Buka browser Anda dan kunjungi:
- **URL**: `http://localhost:8000/admin`
- **Email**: `admin@admin.com`
- **Password**: `password`

---

## 🛠️ Catatan Penting & Troubleshooting

### 1. Hak Akses (Role & Permission) / Tombol Tidak Muncul?
Jika menu baru disembunyikan karena permission belum diperbarui atau tombol tidak muncul di server production, jalankan perintah sapu bersih berikut:
```bash
php artisan app:sync-permissions
```
*Perintah di atas akan secara otomatis membersihkan semua layer cache Laravel, reset cache Spatie Permission, men-generate ulang Shield untuk semua pages/resources/widgets, dan memaksa sinkronisasi hak akses ke role `super_admin`.*

---

## 👨‍💻 Pengembang
- **Irham Karaman** 

*Dibuat dengan ❤️ untuk Universitas Muhammadiyah Ponorogo.*
