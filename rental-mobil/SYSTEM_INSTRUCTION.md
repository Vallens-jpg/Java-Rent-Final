# Proyek Web Rental Mobil (Sisi Customer) - Dokumen Panduan Utama AI

Dokumen ini adalah instruksi absolut dan panduan konteks untuk pengembangan sistem aplikasi rental mobil. AI pendamping (Vibe Coding Partner) **WAJIB** membaca, memahami, dan mematuhi seluruh batasan dan rencana kerja yang tertulis di bawah ini sebelum dan selama proses penulisan kode dilakukan.

---

## 🚫 BATASAN UTAMA (GUARDRAILS) - JANGAN DILANGGAR
1. **FOKUS 100% PADA WEBSITE CUSTOMER:** Proyek ini hanya membahas dan membangun sisi website Customer saja. **Jangan pernah melibatkan, menulis, atau menyarankan kode untuk aplikasi Admin berbasis Java sedikit pun di sini.**
2. **STRATEGI EKSEKUSI:** Fokus utama pada setiap *step* adalah **Fungsionalitas & Struktur Logika Terlebih Dahulu**. Perbaikan estetika, konsistensi warna, dan detail kosmetik desain secara menyeluruh hanya akan dilakukan di tahap akhir (Final Polish).

---

## 🔗 RENCANA INTEGRASI DATABASE TERPUSAT (LARAVEL ↔ JAVA)
Meskipun kita hanya mengoding sisi Laravel PHP, database yang kita buat akan menjadi **Single Source of Truth** yang nantinya akan diakses langsung oleh aplikasi Java desktop/web milik Admin. Oleh karena itu, skema database harus dirancang dengan aturan ketat berikut:

* **DBMS Bersama:** Menggunakan MySQL atau PostgreSQL yang didukung penuh oleh Laravel (Eloquent) dan Java (JDBC / Hibernate / JPA).
* **Konvensi Penamaan Tabel & Kolom:** Gunakan format `snake_case` standar Laravel (contoh: `plate_number`, `created_at`). AI harus memastikan dokumentasi kolom ini bersih agar developer Java tidak kesulitan melakukan *mapping* Object-Relational (ORM) di masa mendatang.
* **Format Waktu & Auto-Increment:** Kolom ID harus menggunakan tipe data `BigInteger` (`unsignedBigInteger` / `bigIncrements` di Laravel) agar sinkron dengan tipe `Long` di Java. Format waktu `timestamp` untuk `created_at` dan `updated_at` harus dijaga standarnya agar tidak terjadi kegagalan *parsing* tanggal di sisi Java.
* **Status Sinkronisasi Real-Time:** Interaksi antar kedua sistem terjadi via database. Contoh: Saat customer mengubah status pesanan ke *pending* di website PHP, aplikasi Java harus bisa membaca perubahan tersebut saat itu juga (lewat query atau pooling).

---

## 📅 RENCANA AWAL & STRUKTUR PENGEMBANGAN

Pengembangan akan dibagi menjadi 3 fase terstruktur. Jangan melompat ke fase berikutnya sebelum fase saat ini selesai sepenuhnya.

### TAHAP 1: Pondasi Arsitektur (Database & Global Layout)
* Membuat skema migrasi database Laravel yang ramah integrasi Java, mencakup tabel: `users`, `cars`, `rentals`, dan `penalties`.
* Membuat rute global di `web.php` dan pengontrol terkait (`AuthController`, `CarController`, `RentalController`, `DashboardController`).
* Membuat satu file Master Layout utama (`layouts/app.blade.php`) agar komponen berulang (Header, Nama User, Search Bar global, dan Ikon Profil) bersifat modular dan konsisten.

### TAHAP 2: Pengembangan 10 Halaman Flow (Balsamiq Wireframe)
AI akan dipandu menggunakan prompt spesifik untuk membangun struktur halaman berikut secara bertahap:
1.  **Halaman Sign In / Register** (`0.png`) & **Login** (`0.0.png`)
2.  **Homepage / Katalog Grid Mobil** (`1.png`)
3.  **Detail Spesifikasi Unit** (`2.png`)
4.  **Formulir Pemesanan Serta Hitung Otomatis** (`3.png`)
5.  **Halaman Instruksi Pembayaran Utama via QRIS/Transfer** (`4.png`)
6.  **Dashboard Customer "Informasi Sewa" (Countdown Sisa Waktu & Denda)** (`5.png`)
7.  **Form Perpanjangan Waktu Sewa** (`6.1.png`)
8.  **Halaman Detail Bayar Denda Keterlambatan** (`6.2.png`)

### TAHAP 3: Final Polish & Refinement Desain
* Penyelarasan visual CSS menggunakan Tailwind secara global setelah seluruh fungsi *routing* dan data *binding* dari database ke interface Blade berjalan 100% tanpa bug.

---

## 🧠 CARA MERESPONS PROMPT USER
* **Tetap Berada di Konteks:** Jika user memberikan potongan kode atau prompt halaman baru, pastikan rute dan variabel yang digunakan sinkron dengan tabel database yang sudah disepakati di Tahap 1.
* **Modular & Bersih:** Berikan kode Laravel yang bersih, manfaatkan fitur Eloquent ORM dengan baik, dan tulis komponen Blade yang mudah dirawat.
* **Jangan Berasumsi:** Jika ada alur data yang kurang jelas pada mockup Balsamiq yang diberikan user, tanyakan konfirmasinya terlebih dahulu sebelum menulis fungsi logikanya.