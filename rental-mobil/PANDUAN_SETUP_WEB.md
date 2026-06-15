# 🌐 Panduan Setup `rental-mobil` (Website Laravel) di Laptop Baru

Jika Anda baru saja meng-*clone* (mengunduh) proyek ini dari GitHub ke laptop baru, Anda tidak bisa langsung menekan tombol *run*. Ada beberapa langkah "pemanasan" (setup awal) yang wajib dilakukan karena folder-folder berat seperti `vendor` dan `node_modules` tidak ikut ter-upload ke GitHub.

Pastikan laptop Anda sudah terinstal **PHP**, **Composer**, **Node.js (NPM)**, dan **XAMPP**.

---

## Langkah 1: Instalasi Library Pendukung
1. Buka Command Prompt (CMD) atau PowerShell.
2. Masuk ke dalam folder web ini:
   ```bash
   cd rental-mobil
   ```
3. Unduh semua *library* PHP (Laravel) dengan mengetik:
   ```bash
   composer install
   ```
4. Unduh semua *library* Javascript (untuk tampilan) dengan mengetik:
   ```bash
   npm install
   ```

## Langkah 2: Konfigurasi Lingkungan (.env)
File rahasia bernama `.env` tidak ikut masuk ke GitHub demi keamanan. Anda harus membuatnya sendiri:
1. Masih di dalam folder `rental-mobil`, salin file contoh yang sudah disediakan:
   - Jika pakai CMD Windows: `copy .env.example .env`
   - Jika pakai PowerShell: `cp .env.example .env`
   *(Atau Anda bisa me-rename/copy-paste filenya secara manual lewat File Explorer).*
2. Setelah file `.env` tercipta, buat kunci rahasia baru untuk aplikasi Anda dengan mengetik:
   ```bash
   php artisan key:generate
   ```

## Langkah 3: Menyiapkan Database
1. Buka aplikasi **XAMPP**, lalu nyalakan **Apache** dan **MySQL** (Klik tombol *Start*).
2. Kembali ke terminal, ketikkan perintah ini untuk membangun struktur tabel di database MySQL Anda, sekaligus mengisinya dengan data mobil bawaan (dummy):
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Peringatan: Perintah ini akan menghapus database lama Anda jika ada, lalu membuat yang baru. Sangat cocok untuk laptop baru).*

## Langkah 4: Menyalakan Website
Untuk menyalakan website ini, Anda butuh **dua buah Terminal/CMD** yang berjalan bersamaan.

**Di Terminal Pertama (Untuk menyalakan mesin Laravel/Backend):**
```bash
php artisan serve
```

**Di Terminal Kedua (Buka tab terminal baru, arahkan ke folder `rental-mobil` lagi, untuk menyalakan desain/Frontend):**
```bash
npm run dev
```

Selesai! Sekarang Anda tinggal membuka *browser* (Chrome/Edge) dan mengetikkan alamat:
**`http://localhost:8000`**

Website Rental Mobil sudah siap digunakan!
