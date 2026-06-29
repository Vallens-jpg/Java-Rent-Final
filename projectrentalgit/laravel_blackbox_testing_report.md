# LAPORAN PENGUJIAN BLACKBOX - WEB LARAVEL (BLACKBOX TESTING REPORT)
**Aplikasi:** Drivora Rental Mobil (Web Customer Application)  
**Teknologi Pengujian:** Manual Functionality Testing & Web GUI Validation  
**Metodologi:** Equivalence Partitioning (EP) & Boundary Value Analysis (BVA)  
**Tanggal Pengujian:** 25 Juni 2026  

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang
Pengujian Blackbox (Blackbox Testing) bertujuan untuk memverifikasi fungsionalitas aplikasi web dari perspektif pengguna akhir (customer) tanpa perlu mengakses struktur kode program atau database secara langsung. Dalam sistem **Drivora**, antarmuka web Laravel dirancang untuk digunakan oleh customer secara mandiri guna mencari armada mobil, mengisi form pengajuan sewa, dan mengurus administrasi sewa (pembayaran, perpanjangan sewa, dan penyelesaian denda).

Laporan ini memetakan hasil pengujian fungsionalitas UI web customer untuk memastikan antarmuka interaktif, penanganan error input, serta penayangan visual berjalan sesuai spesifikasi kebutuhan produk.

### 1.2 Metode Pengujian
Pengujian dirancang menggunakan teknik berikut:
1. **Equivalence Partitioning (EP):** Mengelompokkan input form ke dalam kategori valid dan tidak valid untuk menguji reaksi penolakan sistem.
2. **Boundary Value Analysis (BVA):** Menguji titik kritis batas input (seperti durasi sewa nol atau negatif, string kosong pada form wajib, dan file upload).

---

## 2. RUANG LINGKUP PENGUJIAN

Pengujian Blackbox difokuskan pada 5 modul utama web customer:
1. **Modul 1: Register Akun Customer (`/register`)**
2. **Modul 2: Login Akun Customer (`/login`)**
3. **Modul 3: Pencarian & Detail Katalog Mobil (`/cars`)**
4. **Modul 4: Formulir Pemesanan Sewa Baru (`/rentals/create/{id}`)**
5. **Modul 5: Manajemen Sewa Aktif - Perpanjang & Denda (`/dashboard`)**

---

## 3. MATRIKS DAN SKENARIO PENGUJIAN (TEST SUITE)

Berikut adalah lembar kerja hasil pengujian fungsionalitas antarmuka web Drivora:

### 3.1 Modul 1: Register Akun Customer (`/register`)
*Tujuan: Memastikan customer baru dapat membuat akun dengan data lengkap dan valid, serta validasi kesalahan berfungsi dengan baik.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TBW-REG-001** | Melakukan register dengan seluruh kolom terisi valid. | Nama: "Budi Santoso"<br>Email: "budi.customer@gmail.com"<br>No Telp: "08123456789"<br>Sandi: "budi123" | Pendaftaran sukses, dialihkan ke halaman login dengan pesan sukses (*flash message*). | Pendaftaran berhasil, muncul notifikasi sukses di halaman login. | **PASS** |
| **TBW-REG-002** | Mengosongkan field wajib Nama Lengkap. | Nama: `""`<br>Email: "budi@gmail.com"<br>No Telp: "0812345" | Registrasi dibatalkan, form kembali terisi kecuali password, muncul pesan error *"Nama lengkap wajib diisi."* | Muncul pesan error validasi tepat di bawah field nama. | **PASS** |
| **TBW-REG-003** | Mendaftar dengan password yang terlalu pendek. | Sandi: `"123"` (kurang dari 6 karakter) | Registrasi ditolak, muncul pesan error *"Password minimal harus 6 karakter."* | Muncul peringatan minimal karakter password. Pendaftaran gagal. | **PASS** |
| **TBW-REG-004** | Menggunakan email yang sudah terdaftar sebelumnya. | Email: "budi.customer@gmail.com" (duplikat) | Sistem mendeteksi email duplikat, pendaftaran ditolak dengan pesan *"Email ini sudah terdaftar."* | Muncul pesan error *"Email ini sudah terdaftar."* di samping kolom email. | **PASS** |

---

### 3.2 Modul 2: Login Akun Customer (`/login`)
*Tujuan: Memastikan keamanan otentikasi customer masuk ke dashboard.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TBW-LOG-001** | Login dengan kredensial yang valid. | Email: "budi.customer@gmail.com"<br>Sandi: "budi123" | Login sukses, dialihkan ke halaman Dashboard, session terbuat. | Pengguna berhasil masuk ke dashboard utama customer. | **PASS** |
| **TBW-LOG-002** | Login dengan password yang salah. | Email: "budi.customer@gmail.com"<br>Sandi: "salahsandi" | Login gagal, kembali ke form login dengan pesan *"Kombinasi email dan password salah."* | Kembali ke halaman login disertai pesan error merah. | **PASS** |
| **TBW-LOG-003** | Menggunakan email dengan format tidak valid. | Email: "budicustomergmailcom" (tanpa @ dan .) | Sistem memicu validasi format HTML5 atau backend, login ditolak. | Browser menuntut pengisian email yang valid. | **PASS** |

---

### 3.3 Modul 3: Pencarian & Detail Katalog Mobil (`/cars`)
*Tujuan: Menguji filter navigasi dan penayangan spesifikasi unit mobil.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TBW-CAR-001** | Mengakses halaman katalog tanpa filter. | Klik menu "Cari Mobil" | Sistem menampilkan seluruh daftar armada mobil yang berstatus `available` di posisi atas. | Seluruh armada tampil rapi lengkap dengan foto, spesifikasi, dan tarif. | **PASS** |
| **TBW-CAR-002** | Melakukan pencarian menggunakan keyword merk mobil. | Kata Kunci: `"Avanza"` | Hanya menampilkan mobil yang memiliki kata "Avanza" pada kolom merk. | Menampilkan Toyota Avanza, menyembunyikan unit lainnya. | **PASS** |
| **TBW-CAR-003** | Memilih filter ukuran transmisi (Automatic/Manual). | Transmisi: `"Automatic"` | Katalog memfilter secara instan hanya menampilkan mobil matic. | Sistem menyaring list mobil matic secara akurat. | **PASS** |
| **TBW-CAR-004** | Memilih tombol "Detail Mobil" untuk melihat spesifikasi detail. | Klik unit Toyota Avanza | Dialihkan ke halaman `/cars/{id}` yang memuat plat nomor, jenis transmisi, kapasitas kursi, dan deskripsi sewa. | Halaman detail termuat dengan lengkap. | **PASS** |

---

### 3.4 Modul 4: Formulir Pemesanan Sewa Baru (`/rentals/create/{id}`)
*Tujuan: Memastikan pengisian berkas sewa lengkap dan kalkulasi harga final (diskon promo) akurat di antarmuka.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TBW-RNT-001** | Mengisi formulir sewa reguler selama 2 hari. | Durasi: `2` Hari<br>NIK: `32012345`<br>Alamat: `Bogor` | Sistem menghitung tarif normal, setelah submit langsung dialihkan ke halaman instruksi pembayaran. | Berhasil disubmit, dialihkan ke halaman pembayaran sewa. | **PASS** |
| **TBW-RNT-002** | Mengisi durasi sewa promo selama 7 hari. | Durasi: `7` Hari<br>NIK: `32012345` | Sistem mendeteksi durasi sewa 7 hari, secara otomatis memberikan diskon 15% pada total tagihan akhir. | Tagihan akhir terpotong 15% secara otomatis di backend dan ditampilkan di UI pembayaran. | **PASS** |
| **TBW-RNT-003** | Mengosongkan kolom NIK / No KTP saat checkout. | NIK: `""` (kosong) | Validasi gagal, checkout dibatalkan dengan pesan *"Mohon lengkapi NIK!"* | Form checkout menolak submit dan menyoroti kolom NIK yang kosong. | **PASS** |

---

### 3.5 Modul 5: Manajemen Sewa Aktif - Perpanjang & Denda (`/dashboard`)
*Tujuan: Menguji fungsi pengajuan perpanjangan sewa dan pembayaran denda dari sisi antarmuka customer.*

| Test ID | Skenario Pengujian | Data Input (Test Data) | Hasil Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TBW-MNG-001** | Mengajukan perpanjangan durasi sewa aktif. | Klik "Perpanjang"<br>Durasi tambahan: `1` Hari | Menampilkan halaman detail biaya tambahan untuk 1 hari perpanjangan sewa. | Pengguna melihat nominal perpanjangan dan diarahkan ke tombol bayar. | **PASS** |
| **TBW-MNG-002** | Menyelesaikan konfirmasi bayar perpanjangan. | Klik "Konfirmasi Bayar" | Dialihkan ke dashboard, status perpanjangan menjadi *"Menunggu Verifikasi Admin"*. | Status perpanjangan berubah, notifikasi terkirim ke sistem admin. | **PASS** |
| **TBW-MNG-003** | Membayar denda keterlambatan pengembalian. | Klik "Bayar Denda"<br>Klik "Konfirmasi" | Status denda di dashboard customer berubah menjadi *"Sedang Diverifikasi"*, notifikasi merah keterlambatan hilang. | Status denda berubah menjadi pending verifikasi, notifikasi merah tertutup. | **PASS** |

---

## 4. ANALISIS DAN KESIMPULAN

Berdasarkan pengujian Blackbox pada antarmuka Web Customer Drivora (Laravel):
1. **Pengalaman Pengguna (UX)**: Alur pemesanan mobil dari halaman utama hingga pembayaran sewa terintegrasi dengan baik, instruksi pembayaran tersaji dengan jelas.
2. **Keandalan Validasi Form**: Seluruh input teks wajib dilindungi oleh aturan validasi yang memadai (mencegah data sewa tak lengkap atau format salah dikirim ke database).
3. **Kalkulasi Promo Terbuka**: Penerapan diskon sewa promo 7 hari terbukti langsung tersinkronisasi secara akurat di antarmuka pengguna tanpa ada perbedaan angka.
4. **Respon Status**: Perubahan status transaksi (perpanjangan sewa, denda) langsung ter-update di dashboard customer sesaat setelah diverifikasi admin.

Secara keseluruhan, aplikasi Web Customer dinyatakan **LULUS PENGUJIAN BLACKBOX (100% SUKSES)**.
