# ⚙️ Panduan Setup `rental-admin-java` di Laptop Baru

Dokumen ini berisi panduan spesifik untuk anggota kelompok yang ingin menjalankan **Aplikasi Admin (Java)** dari proyek penyewaan mobil ini di laptop atau lingkungan kerja yang baru.

Pastikan laptop baru tersebut sudah terinstal **Java (JDK) minimal versi 8**, **XAMPP** (untuk menjalankan MySQL), dan **NetBeans IDE**.

---

## 1. Import Proyek ke NetBeans
Karena aplikasi ini dibuat murni (tanpa kerangka bawaan seperti Maven/Gradle), kita harus menggunakan opsi "Existing Sources".

1. Buka aplikasi **NetBeans**.
2. Klik menu **File** -> **New Project...** (atau tekan `Ctrl+Shift+N`).
3. Pada bagian *Categories*, pilih **Java with Ant**.
4. Pada bagian *Projects*, pilih **Java Project with Existing Sources**. Klik *Next*.
5. Di kolom **Project Name**, ketikkan nama bebas, contoh: `RentalAdminDrivora`.
6. Di bagian **Source Package Folders**, klik tombol **Add Folder...**, lalu arahkan dan pilih folder `src` yang ada di dalam folder `rental-admin-java` ini.
7. Jika muncul peringatan *"Do you want to delete the class files now?"*, **klik saja Delete** agar bersih. Lalu klik **Finish**.

---

## 2. Menyambungkan Library (SANGAT PENTING)
Setelah proyek terbuka di sebelah kiri layar NetBeans, jika Anda langsung menjalankannya, pasti akan terjadi ratusan *error* bergaris merah. Hal ini dikarenakan *library* MySQL dan jBCrypt belum terhubung.

Cara menghubungkannya:
1. Di panel *Projects* sebelah kiri NetBeans, cari proyek Anda (`RentalAdminDrivora`).
2. Tepat di bawah nama proyek, **klik kanan** pada folder kuning bernama **Libraries** (bukan *Source Packages*).
3. Pilih menu **Add JAR/Folder...** *(Ingat: Jangan pilih 'Add Library')*.
4. Arahkan kotak pencarian ke folder `lib` yang ada di dalam proyek ini (`rental-admin-java/lib`).
5. Blok dan pilih kedua file `.jar` berikut secara bersamaan:
   - `mysql-connector-j-8.3.0.jar` (Untuk menyambung ke database)
   - `jbcrypt-0.4.jar` (Untuk sistem enkripsi password)
6. Klik **Open**.
7. Tunggu beberapa detik, garis-garis merah *error* di kode akan menghilang dengan sendirinya!

---

## 3. Menghubungkan ke Database
1. Pastikan Anda sudah menyalakan modul **Apache** dan **MySQL** di aplikasi **XAMPP**.
2. Pastikan database bernama `rental_mobil` sudah terbuat dan memiliki isi (jika belum, Anda harus menjalankan *migration* dari proyek Laravel-nya terlebih dahulu).
3. Anda bisa mengecek detail koneksi di dalam kode pada file `src/drivora/Database.java` jika ternyata laptop baru tersebut menggunakan *password* khusus untuk MySQL-nya (standarnya *password* dikosongkan).

---

## 4. Menjalankan Aplikasi
1. Buka folder *Source Packages* -> `drivora` -> klik ganda **`Main.java`**.
2. Klik kanan di mana saja di area tulisan kode file `Main.java` -> pilih **Run File** (atau tekan `Shift + F6`).
3. Aplikasi Admin berhasil berjalan di laptop baru!

---

### Cara Alternatif Cepat (Bagi Pengguna Command Prompt/PowerShell)
Bagi anggota kelompok yang tidak suka membuka NetBeans, aplikasi ini juga bisa langsung dijalankan dengan satu klik selama laptop mereka terpasang Java:
1. Buka PowerShell.
2. Masuk ke dalam folder `rental-admin-java`.
3. Ketik perintah eksekusi berikut:
   `.\run.ps1`
4. Skrip otomatis akan meng-kompilasi (*compile*) dan langsung menjalankan tampilan grafis aplikasinya tanpa membuka IDE sama sekali!
