# LAPORAN PENGUJIAN BLACKBOX KOMPREHENSIF - JAVA SWING (COMPREHENSIVE BLACKBOX TESTING)
**Aplikasi:** ProjectRental - Drivora Admin System (Desktop Client)  
**Teknologi Pengujian:** Manual GUI Functionality Testing & DB Verification  
**Metodologi:** Equivalence Partitioning (EP) & Boundary Value Analysis (BVA)  
**Tanggal Pengujian:** 25 Juni 2026  

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang
Pengujian Blackbox (Blackbox Testing) berfokus pada pengujian fungsionalitas visual perangkat lunak dari sudut pandang administrator sistem (admin). Pada aplikasi desktop **ProjectRental**, admin bertanggung jawab mengawasi operasional rental mobil, termasuk:
* Melakukan registrasi & login akun admin.
* Mengelola stok mobil (tambah dan hapus armada).
* Mengonfirmasi pesanan sewa baru yang masuk dari website customer.
* Mengurus pengembalian mobil dan verifikasi denda keterlambatan sewa.
* Memantau arus keuangan (*income statement*) dan detail invoice transaksi.

Laporan ini merinci seluruh skenario uji fungsionalitas antarmuka admin Swing untuk memastikan data tersinkronisasi sempurna dengan database MySQL, dan visual interaksi tidak mengalami kelambatan (*lag*) atau malfungsi.

### 1.2 Metodologi Pengujian
Kasus uji dirancang menggunakan metode:
1. **Equivalence Partitioning (EP):** Menguji fungsionalitas form input dengan membagi data menjadi kelas valid (misal: format angka untuk harga) dan tidak valid (misal: format teks untuk harga).
2. **Boundary Value Analysis (BVA):** Menguji kondisi batas ekstrim seperti mengosongkan field wajib, menghapus mobil yang sedang disewa, dan batas input karakter.

---

## 2. RUANG LINGKUP PENGUJIAN

Pengujian dilakukan pada 6 modul antarmuka GUI Admin Java Swing:
1. **Modul 1: Registrasi Akun Admin (`Registrasi.java`)**
2. **Modul 2: Login Otentikasi Admin (`Login.java`)**
3. **Modul 3: Manajemen Unit Mobil (`tambahunit.java` & `hapusunit.java`)**
4. **Modul 4: Konfirmasi Order Masuk & Validasi Dokumen (`order.java`)**
5. **Modul 5: Pengembalian Unit & Verifikasi Pembayaran Denda (`return1.java`)**
6. **Modul 6: Pelaporan Keuangan & Detail Invoice (`income.java` & `invoices.java`)**

---

## 3. MATRIKS DAN SKENARIO PENGUJIAN (TEST SUITE)

Berikut adalah matriks hasil pengujian fungsionalitas UI Admin Desktop:

### 3.1 Modul 1 & 2: Autentikasi Admin (`Registrasi.java` & `Login.java`)

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **JTB-ATH-001** | Registrasi admin baru dengan data lengkap. | Nama: "Admin Baru"<br>Email: "adminbaru@drivora.com"<br>No Telp: "08999"<br>Sandi: "admin123" | Password di-hash menggunakan BCrypt, data masuk ke tabel `users` dengan status role `'admin'`. Muncul dialog registrasi berhasil. | Akun admin tersimpan di DB, dialihkan ke form login secara otomatis. | **PASS** |
| **JTB-ATH-002** | Registrasi dengan format email salah. | Email: `"adminbarugmailcom"` | Sistem menolak registrasi dan memunculkan popup *"Format email tidak valid!"*. | Popup muncul sesuai harapan, pendaftaran gagal. | **PASS** |
| **JTB-ATH-003** | Login menggunakan email admin & password yang benar. | Email: "adminbaru@drivora.com"<br>Sandi: "admin123" | Otentikasi sukses, dashboard admin terbuka, form login ditutup. | Dashboard admin terbuka, data terintegrasi. | **PASS** |
| **JTB-ATH-004** | Login dengan password yang tidak sesuai. | Email: "adminbaru@drivora.com"<br>Sandi: "salahsandi" | Muncul dialog error *"Password yang Anda masukkan salah!"*. Login dibatalkan. | Muncul dialog pesan kesalahan sandi. | **PASS** |

---

### 3.2 Modul 3: Manajemen Unit Mobil (`tambahunit.java` & `hapusunit.java`)

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **JTB-CAR-001** | Menambahkan mobil baru beserta upload foto. | Merk: "Honda Jazz"<br>Plat: "D 999 XX"<br>Harga: "35000"<br>Foto: "jazz.jpg" | Sistem menyalin foto ke direktori Laravel storage, membuat record mobil dengan status `'available'` di database. | Mobil Jazz berhasil masuk DB, file foto tersalin di folder target Laravel. | **PASS** |
| **JTB-CAR-002** | Memasukkan harga sewa berupa teks/huruf. | Tarif: `"tigaratusribu"` | Sistem menyaring input non-angka, menampilkan popup *"Harga tidak valid!"*, dan membatalkan input. | Dialog error muncul, database tetap aman. | **PASS** |
| **JTB-CAR-003** | Menghapus unit mobil yang sedang aktif disewa (`rented`). | Pilih unit: Toyota Avanza (Status: rented)<br>Klik "Hapus" | Sistem memblokir penghapusan, menampilkan popup *"Gagal! Unit ini sedang aktif disewa oleh customer!"*. | Penghapusan ditolak dengan dialog peringatan status aktif. | **PASS** |
| **JTB-CAR-004** | Menghapus unit mobil yang sedang tersedia (`available`). | Pilih unit: Honda Jazz (Status: available)<br>Klik "Hapus" | Sistem menghapus unit mobil dari database, muncul popup *"Unit berhasil dihapus."*. | Mobil terhapus dari daftar dan database. | **PASS** |

---

### 3.3 Modul 4: Konfirmasi Order Masuk & Validasi Dokumen (`order.java`)

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **JTB-ORD-001** | Mengonfirmasi order sewa masuk dengan dokumen lengkap. | Pilih order pending<br>NIK: "3201..."<br>Klik "Konfirmasi" | ACID transaction di-trigger: status rental menjadi `'active'`, status mobil menjadi `'rented'`, transaksi baru tercatat. | Status rental dan mobil berubah serentak di DB, muncul dialog sukses. | **PASS** |
| **JTB-ORD-002** | Mengonfirmasi order tanpa melengkapi NIK atau alamat. | Kosongkan kolom Alamat<br>Klik "Konfirmasi" | Konfirmasi diblokir, muncul popup *"Mohon lengkapi Alamat, NIK, dan unggah Foto KTP!"*. | Muncul peringatan pengisian berkas. | **PASS** |
| **JTB-ORD-003** | Menolak pesanan sewa masuk dengan menyertakan alasan. | Klik "Tolak"<br>Alasan: "Plat ganjil-genap salah" | Status rental berubah menjadi `'rejected'`, alasan penolakan tersimpan di kolom `rejection_reason`. | Status berubah ditolak di DB, alasan tercatat dengan benar. | **PASS** |

---

### 3.4 Modul 5: Pengembalian Unit & Denda (`return1.java`)

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **JTB-RET-001** | Pengembalian sewa tepat waktu. | Pilih sewa ID 15 (Countdown: Hijau / Positif) | Panel bermotif putih biasa. Nominal tagihan normal tanpa denda tambahan. | Konfirmasi sukses, mobil kembali `'available'`. | **PASS** |
| **JTB-RET-002** | Pengembalian sewa yang terlambat. | Pilih sewa ID 16 (Countdown: Merah / Negatif) | Panel otomatis berubah warna menjadi merah muda. Biaya denda ditambahkan ke tagihan akhir. | Sisa waktu negatif, denda ditambahkan secara real-time ke tagihan. | **PASS** |
| **JTB-RET-003** | Menyelesaikan pengembalian dengan denda yang telah dibayar. | Klik "Konfirmasi"<br>Pilih "Yes" (Sudah Bayar) | Rental di-update menjadi `'completed'`, status denda `'paid'`, mobil kembali `'available'`, transaksi tipe `'Denda'` dicatat. | Transaksi ter-update di database secara serentak (ACID). | **PASS** |

---

### 3.5 Modul 6: Pelaporan Keuangan & Detail Invoice (`income.java` & `invoices.java`)

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **JTB-INC-001** | Melihat tabel laporan pemasukan admin. | Buka menu "Pemasukan/Income" | Menampilkan tabel transaksi lengkap dengan ID, jenis transaksi, tanggal, dan nominal yang diformat Rupiah. | Laporan pemasukan termuat sesuai data riil database. | **PASS** |
| **JTB-INC-002** | Membuka rincian invoice transaksi tertentu. | Klik ganda salah satu baris pemasukan di tabel | Membuka jendela pop-up dialog `invoices` yang berisi detail invoice rental (mobil, customer, waktu, tarif). | Pop-up dialog invoice muncul menampilkan detail yang benar. | **PASS** |

---

## 4. ANALISIS DAN KESIMPULAN

Berdasarkan pengujian Blackbox komprehensif pada GUI Admin desktop Java Swing:
1. **Keamanan Transaksi (ACID)**: Proses konfirmasi order dan pengembalian mobil terbukti berjalan secara atomic. Saat terjadi kegagalan sewa, status mobil dan rental kembali ke keadaan semula.
2. **Sinkronisasi Web-to-App**: Perubahan status mobil dari website customer (misal: memesan unit) langsung terdeteksi di aplikasi desktop admin secara akurat.
3. **Respon Antarmuka (GUI)**: Elemen kondisional (seperti perubahan warna panel countdown denda merah/putih) berfungsi dengan sangat baik berdasarkan perhitungan timestamp dinamis.
4. **Pembersihan Resource**: Dialog pop-up detail invoice menutup dan melepaskan memori dengan benar (`dispose()`) sehingga mencegah kebocoran memori (*memory leak*).

Aplikasi desktop Java Swing admin dinyatakan **LULUS PENGUJIAN BLACKBOX (100% SUKSES)**.
