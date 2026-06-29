# LAPORAN PENGUJIAN BLACKBOX (BLACKBOX TESTING REPORT)
**Aplikasi:** ProjectRental (Drivora Admin System)  
**Teknologi Pengujian:** Manual Functionality Testing & GUI Validation  
**Metodologi:** Equivalence Partitioning (EP) & Boundary Value Analysis (BVA)  
**Tanggal Pengujian:** 25 Juni 2026  

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang
Pengujian Blackbox (Blackbox Testing) difokuskan pada pengujian persyaratan fungsional dari aplikasi tanpa perlu mengetahui detail implementasi internal atau struktur kode program. Dalam sistem **ProjectRental (Drivora)**, pengujian ini dirancang untuk memastikan bahwa seluruh fungsi antarmuka pengguna (GUI), alur input-output, penanganan error validasi, dan integrasi data dengan database berjalan sesuai dengan spesifikasi kebutuhan pengguna (*user requirements*).

### 1.2 Metode Pengujian
Dalam laporan ini, skenario uji dirancang menggunakan dua teknik utama:
1. **Equivalence Partitioning (EP):** Membagi domain input ke dalam kelas-kelas data yang valid dan tidak valid, lalu memilih satu nilai perwakilan dari setiap kelas untuk diuji.
2. **Boundary Value Analysis (BVA):** Menguji nilai-nilai pada batas maksimum dan minimum dari domain input (misalnya input kosong, input angka negatif, format string minimal/maksimal).

---

## 2. RUANG LINGKUP PENGUJIAN

Pengujian Blackbox dilakukan pada 5 modul utama aplikasi:
1. **Modul 1: Registrasi Admin Baru (`Registrasi.java`)**
2. **Modul 2: Login Admin (`Login.java`)**
3. **Modul 3: Tambah Unit Mobil (`tambahunit.java`)**
4. **Modul 4: Konfirmasi Order Sewa Masuk (`order.java`)**
5. **Modul 5: Pengembalian Unit Mobil & Denda (`return1.java`)**

---

## 3. MATRIKS DAN SKENARIO PENGUJIAN (TEST SUITE)

Berikut adalah lembar kerja pengujian fungsional terperinci yang mencakup skenario uji, input, hasil yang diharapkan, hasil aktual, dan status kelulusan (PASS/FAIL).

### 3.1 Modul 1: Registrasi Admin Baru (`Registrasi.java`)
*Tujuan: Memastikan admin baru dapat mendaftar dengan email valid, no telepon, dan password, serta mencegah pendaftaran duplikat atau format email yang salah.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil yang Diharapkan (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TB-REG-001** | Melakukan pendaftaran dengan seluruh kolom terisi lengkap dan valid. | Email: `budi@gmail.com`<br>Password: `budi123`<br>No Telp: `08123456789` | Sistem menampilkan popup *"Registrasi Berhasil!"*, mengenkripsi password dengan BCrypt, menyimpan data admin ke DB, lalu memindahkan pengguna ke form Login. | Muncul dialog sukses, data tersimpan di tabel `users` dengan role 'admin', berpindah ke form Login. | **PASS** |
| **TB-REG-002** | Mengosongkan semua kolom input pendaftaran. | Email: `""`<br>Password: `""`<br>No Telp: `""` | Sistem menampilkan popup peringatan *"Semua kolom wajib diisi!"* dan membatalkan pendaftaran. | Muncul dialog peringatan sesuai pesan yang diharapkan. Pendaftaran dibatalkan. | **PASS** |
| **TB-REG-003** | Mengisi email dan password tetapi mengosongkan Nomor Telepon. | Email: `budi@gmail.com`<br>Password: `budi123`<br>No Telp: `""` | Sistem menampilkan popup peringatan *"Semua kolom wajib diisi!"* dan membatalkan pendaftaran. | Muncul dialog peringatan. Pendaftaran dibatalkan. | **PASS** |
| **TB-REG-004** | Mengisi email tanpa karakter `@`. | Email: `budigmail.com`<br>Password: `budi123`<br>No Telp: `08123456789` | Sistem menampilkan popup peringatan *"Format email tidak valid!"* dan membatalkan pendaftaran. | Muncul dialog peringatan *"Format email tidak valid!"*. | **PASS** |
| **TB-REG-005** | Mengisi email tanpa karakter titik (`.`). | Email: `budi@gmail`<br>Password: `budi123`<br>No Telp: `08123456789` | Sistem menampilkan popup peringatan *"Format email tidak valid!"* dan membatalkan pendaftaran. | Muncul dialog peringatan *"Format email tidak valid!"*. | **PASS** |
| **TB-REG-006** | Mendaftar menggunakan email yang sudah terdaftar di database. | Email: `budi@gmail.com` (sudah ada di DB)<br>Password: `budi999`<br>No Telp: `089999999` | Sistem mendeteksi duplikasi di DB, menampilkan popup *"Email sudah terdaftar! Gunakan email lain."*, dan membatalkan pendaftaran. | Muncul dialog error *"Email sudah terdaftar! Gunakan email lain."*. | **PASS** |

---

### 3.2 Modul 2: Login Admin (`Login.java`)
*Tujuan: Memverifikasi proses otentikasi admin, pencocokan password terenkripsi, serta pembatasan hak akses hanya untuk role 'admin'.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil yang Diharapkan (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TB-LOG-001** | Melakukan login dengan email dan password admin yang benar. | Email: `budi@gmail.com`<br>Password: `budi123` | Sistem menampilkan popup *"Login Berhasil!"*, membuka halaman Dashboard, dan menutup halaman Login. | Muncul dialog sukses selamat datang admin, dashboard terbuka. | **PASS** |
| **TB-LOG-002** | Mengosongkan kolom Email atau Password. | Email: `""`<br>Password: `budi123` | Sistem menampilkan popup peringatan *"Email dan Password tidak boleh kosong!"*. | Muncul dialog peringatan sesuai pesan. | **PASS** |
| **TB-LOG-003** | Memasukkan password yang salah untuk email terdaftar. | Email: `budi@gmail.com`<br>Password: `salahpass` | Sistem mendeteksi ketidakcocokan BCrypt, menampilkan popup *"Password yang Anda masukkan salah!"*. | Muncul dialog error *"Password yang Anda masukkan salah!"*. | **PASS** |
| **TB-LOG-004** | Memasukkan email yang tidak terdaftar di database. | Email: `asing@gmail.com`<br>Password: `sembarang` | Sistem menampilkan popup *"Akun Admin dengan email tersebut tidak ditemukan!"*. | Muncul dialog error *"Akun Admin dengan email tersebut tidak ditemukan!"*. | **PASS** |
| **TB-LOG-005** | Melakukan login dengan akun yang memiliki role bukan 'admin' (contoh: 'customer'). | Email: `customer@gmail.com`<br>Password: `customer123` | Sistem membatasi akses login dan menampilkan popup *"Akun Admin dengan email tersebut tidak ditemukan!"* karena filter query membatasi hanya `role = 'admin'`. | Login ditolak dengan popup admin tidak ditemukan. | **PASS** |

---

### 3.3 Modul 3: Tambah Unit Mobil (`tambahunit.java`)
*Tujuan: Menguji penambahan data unit mobil baru, pengunggahan gambar ke folder penyimpanan lokal, dan konversi format harga.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil yang Diharapkan (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TB-CAR-001** | Menambahkan mobil dengan data lengkap (Merk, Plat, Transmisi, Kursi, Harga) & Foto. | Merk: `Toyota Avanza`<br>Plat: `B 1234 CD`<br>Trans: `Manual`<br>Kursi: `7`<br>Harga: `Rp 50.000` (input text)<br>Foto: `avanza.png` | Sistem menyalin foto ke folder target Laravel, menyimpan unit ke database dengan status `'available'`, menampilkan popup *"Unit berhasil ditambahkan!"*, dan mengosongkan form. | File foto tersalin, record baru terbuat di tabel `cars` dengan status 'available', form dibersihkan. | **PASS** |
| **TB-CAR-002** | Menambahkan unit dengan mengosongkan kolom Merk Mobil. | Merk: `""`<br>Plat: `B 1234 CD`<br>Harga: `50000` | Sistem menampilkan popup peringatan *"Mohon lengkapi data minimal Merk, Plat, dan Harga!"*. | Muncul dialog peringatan. Penambahan dibatalkan. | **PASS** |
| **TB-CAR-003** | Memasukkan harga sewa menggunakan karakter non-angka (seperti huruf/simbol). | Merk: `Toyota Avanza`<br>Plat: `B 1234 CD`<br>Harga: `limapuluhribu` | Sistem memfilter karakter non-angka, mendeteksi string kosong setelah filter, dan memunculkan popup *"Harga tidak valid!"*. | Muncul dialog error *"Harga tidak valid!"*. | **PASS** |
| **TB-CAR-004** | Menambahkan unit tanpa memilih foto mobil. | Data lengkap, Foto: `Tidak dipilih` | Unit berhasil disimpan di database dengan nilai kolom `image` kosong (`""`). | Data tersimpan di DB dengan path gambar kosong. | **PASS** |

---

### 3.4 Modul 4: Konfirmasi Order Sewa Masuk (`order.java`)
*Tujuan: Menguji verifikasi kelengkapan berkas customer (NIK, Alamat, Upload KTP) sebelum mengubah status order menjadi aktif.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil yang Diharapkan (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TB-ORD-001** | Mengonfirmasi order dengan Alamat, NIK, dan Foto KTP terisi lengkap. | NIK: `3201234567890001`<br>Alamat: `Jl. Merdeka No. 10`<br>Foto KTP: `ktp_customer.jpg` | Sistem mengupdate tabel `users`, mengubah status rental menjadi `'active'`, mencatat transaksi baru tipe `'Sewa Baru'` ke DB, mengubah status mobil menjadi `'rented'`, dan menampilkan popup sukses. | Data rental berubah aktif, data transaksi bertambah, status mobil di DB berubah menjadi 'rented'. | **PASS** |
| **TB-ORD-002** | Mengonfirmasi order tetapi lupa mengunggah Foto KTP. | NIK: `3201234567890001`<br>Alamat: `Jl. Merdeka No. 10`<br>Foto KTP: `Belum diupload` | Sistem membatasi aksi dan menampilkan popup *"Mohon lengkapi Alamat, NIK, dan unggah Foto KTP!"*. | Muncul dialog peringatan kelengkapan data. | **PASS** |
| **TB-ORD-003** | Menolak pesanan sewa masuk dengan alasan penolakan. | Klik "Tolak"<br>Alasan: `"Mobil sedang diservis"` | Sistem menyimpan alasan penolakan ke database, mengubah status rental menjadi `'rejected'`, dan memunculkan popup *"Pesanan berhasil ditolak."*. | Status rental berubah jadi 'rejected', alasan tercatat di kolom `rejection_reason` di database. | **PASS** |

---

### 3.5 Modul 5: Pengembalian Unit Mobil & Denda (`return1.java`)
*Tujuan: Menguji alur pengembalian mobil, perhitungan otomatis denda jika pengembalian terlambat (overdue), dan pembaharuan status unit menjadi tersedia.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil yang Diharapkan (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TB-RET-001** | Mengonfirmasi pengembalian tepat waktu (Sisa waktu > 0). | Rental ID: `15` (Sisa waktu: `02:30:15`) | Panel pengembalian berwarna putih. Tidak ada biaya denda yang ditambahkan. Total tagihan sama dengan harga asli. | Sisa waktu positif hijau, total bayar normal, konfirmasi sukses tanpa denda. | **PASS** |
| **TB-RET-002** | Mengonfirmasi pengembalian terlambat (Sisa waktu < 0 / Negatif). | Rental ID: `16` (Sisa waktu: `-01:15:00`) | Panel pengembalian otomatis berubah warna menjadi merah muda/merah. Biaya denda ditambahkan ke tagihan akhir. | Sisa waktu negatif merah, denda ditambahkan pada label total tagihan akhir secara otomatis. | **PASS** |
| **TB-RET-003** | Menyelesaikan pengembalian terlambat dengan konfirmasi bayar denda. | Klik "Konfirmasi"<br>Pilih "Yes" | Status rental di-update menjadi `'completed'`, status denda di-update menjadi `'paid'`, status mobil kembali `'available'`, dan tercatat transaksi tipe `'Denda'` di DB. | Database ter-update: rental completed, mobil available, transaksi denda tersimpan. | **PASS** |

---

## 4. ANALISIS DAN KESIMPULAN

Berdasarkan pengujian Blackbox yang menyeluruh terhadap 5 modul utama pada aplikasi **ProjectRental**:
1. Seluruh validasi input antarmuka pengguna telah berfungsi dengan baik untuk mencegah data kosong, duplikasi, atau format yang tidak didukung masuk ke dalam database.
2. Penanganan pesan error menggunakan `JOptionPane` berjalan lancar dan memberikan informasi yang jelas kepada admin jika terjadi kesalahan pengisian form.
3. Transisi status objek di database (misalnya status mobil dari `available` -> `rented` -> `available` dan status rental dari `pending` -> `active` -> `completed`) terintegrasi secara akurat dengan antarmuka visual admin.
4. Fitur perhitungan denda otomatis sangat responsif dalam mendeteksi keterlambatan waktu sewa berdasarkan timestamp sistem saat ini.

Secara keseluruhan, aplikasi **ProjectRental** dinyatakan **LULUS (100% SUCCESS)** pengujian fungsionalitas Blackbox.
