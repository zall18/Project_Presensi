# Project Presensi (Sistem Manajemen Kehadiran)

**Project Presensi** adalah aplikasi berbasis web yang dirancang untuk memodernisasi dan mempermudah proses pencatatan serta pengelolaan kehadiran (presensi) dalam suatu instansi atau organisasi.

Dibangun dengan teknologi **Laravel**, sistem ini menawarkan solusi *all-in-one* untuk memantau kedatangan peserta, mengatur jadwal kerja yang kompleks, hingga mengelola perangkat keras absensi.

## 📖 Tentang Aplikasi

Sistem ini dibuat untuk mengatasi kendala pencatatan manual atau sistem lama yang kaku. Dengan **Project Presensi**, administrator dapat memantau data kehadiran secara *real-time*, mengelola perpindahan shift kerja dengan mudah, dan menghasilkan laporan kehadiran yang akurat hanya dengan beberapa klik.

Aplikasi ini mendukung integrasi dengan perangkat fisik (seperti mesin RFID/Fingerprint) dan memiliki antarmuka yang responsif serta mudah digunakan (user-friendly).

## ✨ Fitur Unggulan

Berikut adalah modul dan kemampuan utama dari sistem ini:

### 1. Dashboard Analitik
Halaman utama yang menyajikan ringkasan data penting secara visual, seperti:
* Statistik kehadiran harian.
* Jumlah total peserta aktif.
* Status koneksi perangkat presensi.

### 2. Manajemen Peserta (Participant) & Grup
* **Database Terpusat:** Menyimpan data lengkap karyawan atau siswa.
* **Pengelompokan (Grouping):** Mengatur peserta ke dalam divisi, departemen, atau kelas tertentu untuk memudahkan pengelolaan jadwal dan laporan.
* **Import Data:** Mendukung fitur upload data massal untuk efisiensi waktu.

### 3. Sistem Jadwal & Shift Fleksibel
* **Manajemen Jam Kerja:** Pengaturan jam masuk dan pulang yang dapat disesuaikan.
* **Multi-Shift:** Mendukung berbagai pola kerja (Shift Pagi, Siang, Malam, atau Non-Shift).
* **Mapping Jadwal:** Kemudahan menugaskan jadwal shift tertentu ke individu atau satu grup sekaligus.

### 4. Monitoring & Log Presensi
* **Real-time Log:** Data kehadiran (Check-in/Check-out) tercatat langsung saat peserta melakukan presensi.
* **Status Kehadiran:** Sistem otomatis mendeteksi status keterlambatan atau pulang lebih awal.
* **Ekspor Laporan:** Fitur unduh rekapitulasi kehadiran dalam format Excel untuk kebutuhan administrasi.

### 5. Manajemen Perangkat (Device Management)
* Kontrol penuh untuk menambah dan mengelola mesin presensi yang terhubung ke sistem.
* Fitur pemantauan status koneksi perangkat (Online/Offline) untuk memastikan sistem berjalan lancar.

### 6. Pengaturan Hari Libur
* **Libur Nasional:** Menandai tanggal merah agar tidak terhitung sebagai hari kerja/sekolah.
* **Libur Khusus Grup:** Mengatur hari libur spesifik hanya untuk departemen atau kelompok tertentu.

### 7. Hak Akses Bertingkat
* **Admin:** Memiliki akses penuh ke seluruh pengaturan sistem.
* **Operator:** Memiliki akses terbatas untuk pengelolaan operasional harian.

## 💻 Teknologi

Aplikasi ini dikembangkan menggunakan tumpukan teknologi modern untuk memastikan performa dan keamanan:
* **Framework:** Laravel (PHP)
* **Frontend:** Bootstrap 5 & Blade Templates
* **Database:** MySQL
* **Visualisasi Data:** ApexCharts
