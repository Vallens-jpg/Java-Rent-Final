# Penjelasan Kode Menyeluruh Sistem Rental Mobil

Dokumen ini berisi penjelasan dari setiap file penting yang membangun sistem aplikasi Rental Mobil (baik aplikasi Web Laravel maupun aplikasi Admin Java). Dokumen ini bertujuan agar setiap anggota kelompok dapat memahami alur logika dan mampu menjelaskannya secara detail saat presentasi.

---

## BAGIAN 1: APLIKASI ADMIN (JAVA)

Aplikasi Java dikhususkan untuk admin yang mengelola pesanan. File-file ini berada di dalam folder `rental-admin-java/src/drivora/`.

### 1. Database.java
File ini adalah inti dari koneksi antara aplikasi Java dan database MySQL.
- `String URL = "jdbc:mysql://localhost:3306/rental_mobil";`
  Ini adalah alamat tujuan database. "localhost" berarti database ada di komputer yang sama, "3306" adalah port standar MySQL, dan "rental_mobil" adalah nama databasenya.
- `String USER = "root";` dan `String PASS = "";`
  Ini adalah kredensial untuk masuk ke MySQL (secara default di XAMPP, usernya adalah root dan passwordnya kosong).
- `DriverManager.getConnection(URL, USER, PASS);`
  Perintah bawaan Java (JDBC) untuk secara aktif mencoba menyambungkan aplikasi dengan database berdasarkan kredensial di atas. Jika gagal, program akan masuk ke blok `catch` dan menampilkan pesan kesalahan.

### 2. Main.java
File ini adalah titik awal (entry point) aplikasi Java dijalankan.
- `public static void main(String[] args)`
  Setiap program Java harus memiliki metode ini agar bisa berjalan.
- `UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());`
  Baris ini mengubah tampilan standar aplikasi Java yang terkesan kuno menjadi menyesuaikan dengan tampilan sistem operasi (misalnya mirip dengan tampilan Windows asli).
- `SwingUtilities.invokeLater(() -> new LoginFrame().setVisible(true));`
  Memastikan bahwa tampilan grafis (GUI) dimuat secara aman di thread khusus antarmuka. Di sini aplikasi langsung memanggil dan menampilkan halaman LoginFrame.

### 3. LoginFrame.java
File antarmuka untuk masuk ke dalam sistem admin.
- `String email = emailField.getText().trim();`
  Mengambil input email, lalu menggunakan `.trim()` untuk menghapus spasi yang tidak disengaja di awal atau akhir kata.
- `String checkSql = "SELECT id FROM users WHERE email = ?";` (Bagian Sign In)
  Saat mendaftar admin baru, aplikasi akan mengecek terlebih dahulu apakah email sudah digunakan di database.
- `String hashedPw = BCrypt.hashpw(password, BCrypt.gensalt());`
  Jika mendaftar admin baru, password tidak disimpan sebagai teks biasa, melainkan diacak (hashing) menggunakan algoritma BCrypt demi keamanan.
- `String javaHash = dbHash.replace("$2y$", "$2a$");` (Bagian Login)
  Ini adalah teknik penyesuaian. Sistem web (Laravel) membuat hash dengan format awalan `$2y$`, sedangkan library Java (jBCrypt) mendeteksi hash dengan awalan `$2a$`. Oleh karena itu, kita ubah sementara agar bisa dicocokkan.
- `BCrypt.checkpw(password, javaHash)`
  Mengecek kecocokan antara password teks biasa yang diketik admin dengan password acak yang ada di database.

### 4. DashboardFrame.java
Ini adalah kerangka utama jendela admin setelah berhasil login.
- `CardLayout cardLayout = new CardLayout();`
  CardLayout digunakan agar kita bisa menumpuk beberapa halaman (seperti Order, Return, Income) di tempat yang sama, lalu menampilkannya satu per satu seperti membalik halaman kartu, tanpa harus membuka jendela baru.
- `sidebar.add(createNavButton("Pesanan Baru", e -> switchPanel("ORDER")));`
  Membuat tombol navigasi di sebelah kiri. Saat diklik, tombol ini memanggil fungsi penukaran panel yang dikelola oleh CardLayout tadi untuk menampilkan panel "ORDER" (OrderPanel).

### 5. OrderPanel.java
Halaman ini mengatur semua pesanan baru yang baru saja dibuat pelanggan di website.
- `SELECT r.id, r.user_id, c.id as car_id, ... WHERE r.status = 'pending'`
  Mengambil data dari database dengan status 'pending'. Aplikasi hanya akan menampilkan pelanggan yang baru memesan dan belum disetujui.
- Blok kode `confirmOrder()`:
  - `UPDATE rentals SET status = 'active', admin_id = ? WHERE id = ?`
    Mengubah status pemesanan dari menunggu menjadi aktif (berjalan). Kita juga mencatat admin siapa yang menyetujuinya.
  - `INSERT INTO transactions ... VALUES (?, ?, 'Sewa Baru', ...)`
    Setelah disetujui, uang sewa akan langsung dicatat ke tabel laporan keuangan (transactions) sebagai pemasukan "Sewa Baru".
  - `UPDATE cars SET status = 'rented' WHERE id = ...`
    Mengubah status mobil dari 'available' menjadi 'rented' agar mobil tersebut tidak bisa disewa orang lain di website.

### 6. ReturnPanel.java
Halaman ini melacak pelanggan yang sedang menyewa mobil (status 'active') dan mengurus denda atau perpanjangan waktu.
- `TIMESTAMPDIFF(SECOND, NOW(), r.end_time) as time_diff_sec`
  Menggunakan fungsi waktu MySQL untuk menghitung selisih waktu antara waktu saat ini dengan batas akhir waktu sewa (dalam detik). Jika hasilnya negatif, berarti waktu sewa sudah lewat (terlambat).
- Blok kode Pengembalian (Return):
  - `UPDATE rentals SET status = 'completed'`
    Menandakan bahwa sewa telah selesai secara keseluruhan.
  - `UPDATE cars SET status = 'available'`
    Membebaskan mobil kembali ke katalog agar bisa disewa pelanggan lain.
- Blok kode Perpanjangan (Extend):
  - `UPDATE rentals SET end_time = DATE_ADD(end_time, INTERVAL ? DAY)`
    Menambahkan batas waktu sewa sebanyak jumlah hari yang disetujui admin.
  - `INSERT INTO transactions ... VALUES (?, ?, 'Perpanjangan', ...)`
    Mencatat uang perpanjangan ke laporan keuangan terpisah.
- Blok kode Denda (Penalty):
  - `UPDATE rentals SET penalty_status = 'paid'`
    Mengubah status denda menjadi lunas di database.
  - `INSERT INTO transactions ... VALUES (?, ?, 'Denda', ...)`
    Mencatat uang denda ke dalam laporan transaksi harian.

### 7. IncomeSheetPanel.java
Halaman ini hanya bertugas untuk menampilkan laporan riwayat keuangan.
- `SELECT t.rental_id, t.car_id, c.brand, t.transaction_type, t.amount ...`
  Mengambil data secara spesifik dari tabel `transactions` yang berisi riwayat pasti dari uang masuk (Sewa Baru, Perpanjangan, Denda).
- `DefaultTableModel tableModel = new DefaultTableModel(columns, 0)`
  Membuat struktur tabel kosong di tampilan Java yang tidak bisa diedit secara manual oleh pengguna (read-only) untuk mencegah manipulasi laporan secara visual.
- Blok kode Ekspor CSV:
  - `FileWriter csvWriter = new FileWriter(file);`
    Fitur ini membuat file teks berekstensi .csv. Program akan membaca seluruh baris dan kolom yang ada di tabel Java, memisahkannya dengan tanda koma (,), dan menyimpannya ke dalam file fisik di komputer admin.

---

## BAGIAN 2: APLIKASI WEB PELANGGAN (LARAVEL PHP)

Aplikasi web dikhususkan untuk pelanggan berinteraksi (memilih mobil, mendaftar, memesan). File-file ini berada di dalam folder `rental-mobil/`.

### 1. routes/web.php
File ini adalah peta jalan atau penghubung utama dari sistem web.
- `Route::get('/cars', [CarController::class, 'index']);`
  Ketika ada pengguna yang mengetikkan alamat `/cars` di browser, sistem Laravel akan memerintahkan `CarController` pada fungsi `index` untuk bekerja.
- `Route::middleware('auth')`
  Ini adalah penjaga pintu keamanan. Rute-rute yang ada di dalam grup ini (seperti pemesanan, melihat riwayat) hanya bisa diakses jika pengguna sudah melakukan login. Jika belum, mereka akan dialihkan secara otomatis ke halaman login.

### 2. AuthController.php
Mengatur pendaftaran dan login pelanggan di web.
- `User::create([... 'password' => Hash::make($request->password)])`
  Fungsi ini menambahkan akun pengguna baru ke database. Sama seperti Java, password dienkripsi menggunakan fungsi bawaan Laravel (Hash::make yang secara default menggunakan Bcrypt) sebelum dimasukkan ke database.
- `if (Auth::attempt($credentials))`
  Memeriksa apakah email dan password yang diketik pengguna di form login web cocok dengan data di database.

### 3. CarController.php
Mengatur tampilan katalog mobil di beranda web.
- `public function index(Request $request)`
  Fungsi ini mengumpulkan daftar mobil dari database untuk ditampilkan ke layar.
- `$query->orderByRaw("FIELD(status, 'available', 'rented')");`
  Alih-alih menyembunyikan mobil yang disewa, kode ini mengurutkan agar mobil yang masih tersedia (available) muncul di urutan teratas, sementara mobil yang sedang disewa (rented) akan turun ke urutan paling bawah katalog.

### 4. RentalController.php
Ini adalah file pengontrol inti untuk semua kegiatan pemesanan pelanggan.
- `store()` (Membuat Pesanan):
  - `$startTime->diffInHours($endTime)`
    Menghitung berapa jam selisih antara waktu mulai dan waktu selesai untuk mendapatkan durasi sewa secara otomatis berdasarkan kalender yang dipilih pelanggan.
  - `$totalPrice = $durationHours * $car->price_per_hour`
    Menghitung total tagihan dengan mengalikan durasi jam dengan tarif sewa mobil per jamnya.
  - `Rental::create([... 'status' => 'pending'])`
    Menyimpan data pesanan tersebut ke tabel `rentals` dengan status menunggu (pending) agar diproses oleh admin Java.
- `requestExtend()` (Meminta Perpanjangan):
  - `if ($rental->status !== 'active')`
    Pencegahan keamanan dasar. Memastikan hanya pesanan yang sedang berjalan yang boleh mengajukan perpanjangan hari.
  - `$rental->update(['extension_status' => 'pending_verification', 'extension_days' => $request->days])`
    Sistem belum akan menambahkan waktunya, melainkan memberi sinyal ke aplikasi Java admin bahwa pelanggan meminta perpanjangan sebanyak sekian hari.

### 5. DashboardController.php
Hanya bertugas memuat data yang relevan dengan pelanggan untuk halaman utama profil pelanggan.
- `$rental = \App\Models\Rental::with('car')->where('user_id', auth()->id())->latest()->first();`
  Mencari satu pesanan paling terakhir (terbaru) yang dimiliki oleh pengguna yang sedang membuka web. Fungsi `with('car')` adalah teknik optimasi untuk langsung menyertakan informasi detail mobil yang disewa tanpa harus memanggil database dua kali.

### 6. Blade Views (resources/views/)
Ini adalah folder yang berisi file HTML (tampilan visual) dengan campuran bahasa PHP (dikenal sebagai Blade).
- `layouts/app.blade.php`: Kerangka utama website. Menampung navigasi bar, menu, dan notifikasi agar konsisten di seluruh halaman web.
- `dashboard/customer.blade.php`: Halaman tempat pelanggan melihat timer dan status.
  - `@if($rental->status == 'active')` ... `@elseif($rental->status == 'pending')`
    Ini adalah kondisi (if-else). Tampilan HTML yang dirender (dimunculkan) akan berbeda 180 derajat tergantung pada status pesanan pelanggan. Jika statusnya aktif, maka HTML berisi waktu hitung mundur akan ditampilkan, menyembunyikan tampilan pesanan diproses.

### 7. Migrations (database/migrations/)
File-file di dalam folder ini bertugas seperti "arsitek bangunan". Mereka mendefinisikan bentuk tabel di dalam MySQL saat perintah `php artisan migrate` dijalankan.
- `create_transactions_table.php` (Tabel Transaksi):
  Membuat kolom seperti `rental_id`, `car_id`, `transaction_type` (Sewa Baru, Perpanjangan, Denda), dan `amount` (nominal uang). Ini yang memastikan bahwa struktur database sudah terbangun sebelum aplikasi mencoba menyimpan data apa pun ke dalamnya.
