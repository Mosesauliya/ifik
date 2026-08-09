# IFIK - Sistem Informasi Akademik & Pendaftaran Tugas Akhir

Sistem Informasi Akademik dan Pendaftaran Tugas Akhir berbasis web yang dikembangkan untuk lingkungan kampus **Fakultas Industri Kreatif (IFIK)**. Sistem ini dibangun menggunakan framework **CodeIgniter 3** dengan arsitektur modern yang mendukung alur kerja persetujuan (approval chain) bertingkat, pemetaan geodata mahasiswa, dan manajemen tugas akhir.

---

## 🚀 Fitur Utama

- **Pendaftaran Tugas Akhir (6-Step Wizard)**
  - Pengisian data judul TA (Utama, Alternatif 1 & 2, Judul Bahasa Inggris).
  - Pembagian konsentrasi (misal: Desain Grafis, DKV, dll).
  - Unggah persyaratan berkas PDF (KSM, Transkrip Nilai, Surat Pernyataan, Bebas Lab).

- **Multi-Stage Approval Chain (Alur Persetujuan Bertingkat)**
  - Persetujuan bertahap mulai dari **Dosen Wali** ➔ **Admin LAA** ➔ **Koordinator TA** ➔ **Ketua Kelompok Keahlian (KK)**.
  - Tracking status persetujuan secara real-time dari dashboard mahasiswa.

- **Fitur Geodata Mahasiswa (GIS Mapping)**
  - Pencatatan dan pembaruan lokasi tempat tinggal mahasiswa (Latitude, Longitude, Alamat, Kota, Provinsi).
  - Integrasi visualisasi pemetaan geodata.

- **Panel Dosen Wali & Layanan Akademik**
  - Monitoring daftar mahasiswa bimbingan.
  - Verifikasi dan persetujuan dokumen pendaftaran TA mahasiswa.

- **Manajemen Akun & Autentikasi**
  - Fitur Login & Ganti Password mandiri dengan enkripsi `Bcrypt`.

---

## 🛠️ Teknologi & Stack

- **Framework**: CodeIgniter 3 (PHP 8.1 / 7.4)
- **Database**: MySQL 8.0 / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Containerization**: Docker & Docker Compose (PHP-Apache + MySQL + phpMyAdmin)

---

## 🐳 Cara Menjalankan Menggunakan Docker (Direkomendasikan)

Projek ini sudah dilengkapi dengan konfigurasi Docker Compose siap pakai.

### 1. Prasyarat
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) sudah terinstall dan berjalan.

### 2. Menjalankan Container
Buka terminal pada direktori projek, lalu jalankan:

```bash
docker-compose up -d --build
```

### 3. Akses Layanan
- **Aplikasi Web IFIK**: [http://localhost:8080](http://localhost:8080)
- **phpMyAdmin**: [http://localhost:8081](http://localhost:8081)
  - *Server*: `db`
  - *Username*: `ci3_user` atau `root`
  - *Password*: `ci3_password` atau `rootpassword`

---

## 💻 Cara Menjalankan Lokal (Laragon / XAMPP)

1. Pastikan modul PHP versi 7.4 atau 8.1 dan MySQL sudah aktif di Laragon/XAMPP.
2. Buat database baru bernama `db_ifik` di phpMyAdmin.
3. Import file struktur/data database jika tersedia.
4. Sesuaikan konfigurasi di `application/config/database.php`:
   ```php
   $db['default']['hostname'] = 'localhost';
   $db['default']['username'] = 'root';
   $db['default']['password'] = '';
   $db['default']['database'] = 'db_ifik';
   ```
5. Buka browser dan akses `http://localhost/ifik`.

---

## 📁 Struktur Direktori Projek

```text
ifik/
├── application/
│   ├── config/          # Konfigurasi CodeIgniter (database, routes, autoload, config)
│   ├── controllers/     # Mahasiswa, DosenWali, Login, Welcome
│   ├── models/          # Mahasiswa_model, DosenWali_model
│   ├── views/           # Tampilan UI (mahasiswa, dosen_wali, admin, partials)
│   └── third_party/     # Library pihak ketiga
├── docker/
│   └── apache/          # VirtualHost config Apache (000-default.conf)
├── system/              # Core framework CodeIgniter 3
├── uploads/             # Berkas unggahan persetujuan & TA
├── Dockerfile           # Build spec PHP 8.1 Apache
├── docker-compose.yml   # Orchestration service Web, DB, phpMyAdmin
├── README-DOCKER.md     # Panduan spesifik Docker
└── README.md            # Dokumentasi Projek
```

---

## 📝 Lisensi

Projek ini dikembangkan khusus untuk lingkungan internal **Fakultas Industri Kreatif (IFIK)**.
