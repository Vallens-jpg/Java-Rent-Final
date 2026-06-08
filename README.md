# 🚗 Drivora - Sistem Manajemen Rental Mobil Terpadu

Halo! Selamat datang di proyek kode **Drivora**. Proyek ini adalah sistem penyewaan mobil yang sangat keren karena menggabungkan **dua aplikasi berbeda** yang saling terhubung satu sama lain melalui satu *database* (penyimpanan data) yang sama.

Dokumentasi ini ditulis dengan bahasa yang sangat sederhana agar siapa pun yang baru belajar koding bisa memahami cara kerja sistem ini! 😊

---

## 🏗️ Struktur Utama Proyek

Proyek ini terbagi menjadi dua bagian besar (berada di dalam 2 folder utama):

### 1. `rental-mobil` (Aplikasi Web untuk Pelanggan)
Ini adalah *website* yang dibuat menggunakan teknologi **Laravel (PHP)**. Di sinilah para pelanggan masuk untuk menyewa mobil.
- **Tampilan Katalog:** Pelanggan bisa melihat daftar mobil yang tersedia. Mobil yang sedang disewa orang lain akan berwarna abu-abu gelap dan tidak bisa diklik.
- **Pemesanan:** Pelanggan bisa memilih tanggal mulai dan tanggal selesai sewa.
- **Dashboard Pelanggan:** Setelah memesan, pelanggan bisa melihat status pesanan mereka (apakah masih menunggu konfirmasi admin, ditolak, atau disetujui).
- **Hitung Mundur Waktu (Timer):** Jika disetujui, akan ada hitung mundur waktu sewa (berapa jam/hari lagi mobil harus dikembalikan).
- **Perpanjangan Sewa:** Jika pelanggan ingin memperpanjang sewa, mereka bisa menekan tombol perpanjangan di sini.

### 2. `rental-admin-java` (Aplikasi Desktop untuk Admin)
Ini adalah aplikasi komputer (Desktop) yang dibuat menggunakan **Bahasa Java**. Aplikasi ini dikhususkan bagi para **Admin/Pemilik Rental** untuk mengontrol semuanya dari balik layar.
- **Login Admin:** Hanya admin terdaftar yang bisa masuk ke sini.
- **Panel Pesanan Baru:** Admin bisa melihat pelanggan mana saja yang baru memesan mobil, lalu memencet tombol *Konfirmasi* atau *Tolak*.
- **Panel Pengembalian (Return Panel):** Di sini admin melihat daftar orang yang sedang menyewa mobil saat ini.
  - Jika orang tersebut telat mengembalikan, admin bisa menekan **Konfirmasi Denda**.
  - Jika orang tersebut minta perpanjangan waktu, admin bisa menekan **Konfirmasi Perpanjangan**.
- **Laporan Pendapatan (Income Sheet):** Tempat admin melihat seluruh riwayat uang masuk secara lengkap (baik itu uang Sewa Baru, Perpanjangan, maupun Denda).

---

## ⚙️ Bagaimana Kedua Aplikasi Ini Terhubung?

Kunci utamanya ada pada **Database MySQL**. Bayangkan *database* ini seperti sebuah buku besar catatan yang ditaruh di tengah meja.

1. Saat **Pelanggan** menekan tombol *"Sewa Mobil"* di Website (Laravel), website akan menulis data pesanan baru ke buku catatan besar tersebut dengan status `"pending"`.
2. Di saat yang sama, saat **Admin** membuka Aplikasi Java-nya, aplikasi Java akan membaca buku catatan besar tersebut. Saat melihat ada status `"pending"`, aplikasi Java akan menampilkannya di layar Admin.
3. Saat Admin menekan tombol *"Konfirmasi"*, aplikasi Java akan mencoret tulisan `"pending"` di buku catatan tersebut dan mengubahnya menjadi `"active"`.
4. Pelanggan yang sedang melihat Website akan langsung menyadari perubahan status tersebut menjadi *"Disetujui"*.

Begitulah cara mereka berkomunikasi, padahal bahasanya berbeda (yang satu PHP, yang satu Java)! Ajaib, bukan? ✨

---

## 🗄️ Susunan Database (Tempat Menyimpan Data)

Sistem ini memiliki beberapa "laci penyimpanan" di dalam *database*:

1. **`users`**: Laci untuk menyimpan nama, email, password orang-orang (baik pelanggan maupun admin).
2. **`cars`**: Laci untuk daftar mobil-mobil (nama mobil, transmisi, harga sewa, gambar, dll). Ada kolom status yang menandakan mobil tersebut `"available"` (tersedia) atau `"rented"` (sedang disewa).
3. **`rentals`**: Laci paling sibuk! Ini adalah laci yang mencatat siapa menyewa mobil apa, dari tanggal berapa sampai kapan, total harganya berapa, dan status sewanya.
4. **`transactions`**: Laci **kasir**. Setiap kali admin mengonfirmasi pesanan (Sewa Baru, Perpanjangan, Denda), uangnya akan dicatat di laci ini. Laci ini yang bertugas untuk menampilkan laporan di menu *Income Sheet* admin.
5. **`notifications`**: Laci untuk mengirim pesan notifikasi ke layar pelanggan di website.

---

## 🛠️ Cara Menjalankan Proyek Ini

Jika Anda ingin menjalankan proyek ini di komputer Anda, ikuti langkah-langkah berikut:

### Syarat Wajib:
Komputer Anda harus sudah terpasang **XAMPP** (untuk Database MySQL), **PHP**, **Composer**, **Node.js**, dan **Java (JDK)** beserta **NetBeans**.

### Langkah 1: Jalankan Database
1. Buka XAMPP dan *Start* bagian **Apache** dan **MySQL**.
2. Masuk ke folder web `rental-mobil` melalui terminal/CMD.
3. Ketikkan perintah ini untuk membuat ulang seluruh database beserta isinya secara otomatis:
   `php artisan migrate:fresh --seed`

### Langkah 2: Nyalakan Website (Laravel)
Masih di terminal folder `rental-mobil`:
1. Nyalakan pelayan (server) website: `php artisan serve`
2. Buka terminal baru (satu lagi) di folder yang sama, lalu nyalakan tampilan desain web: `npm run dev`
3. Sekarang Anda bisa buka webnya di browser pada alamat: `http://localhost:8000`

### Langkah 3: Nyalakan Aplikasi Admin (Java)
1. Buka aplikasi **NetBeans** Anda.
2. Buat proyek baru (*New Project*) -> Pilih **Java with Ant** -> **Java Project with Existing Sources**.
3. Arahkan *folder* *source*-nya (sumber) ke folder `src` yang ada di dalam `rental-admin-java`.
4. **Penting:** Setelah proyek terbuka, klik kanan folder **Libraries** di NetBeans, pilih **Add JAR/Folder**, lalu tambahkan file `mysql-connector-j-8.3.0.jar` dan `jbcrypt-0.4.jar` yang ada di dalam folder `lib`.
5. Tekan tombol Play (segitiga hijau) di atas NetBeans untuk menjalankan aplikasinya.

Selesai! Sekarang Anda dapat masuk ke web sebagai pelanggan dan memesan mobil, lalu memprosesnya melalui aplikasi Java sebagai admin.

---

Selamat belajar dan mencoba! Jika Anda merasa kebingungan, ingatlah bahwa kode itu hanyalah serangkaian logika sebab-akibat sederhana. Semangat! 🚀
