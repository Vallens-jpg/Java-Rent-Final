# 📖 Contekan Penjelasan Kode (Line-by-Line)

Dokumen ini dibuat khusus untuk anggota kelompok agar bisa memahami dan menjelaskan bagaimana kode di balik layar bekerja saat ditanya oleh dosen. Tidak perlu menghafal semuanya, cukup pahami alur ceritanya!

Di sini kita akan membedah **3 Fitur Utama** baris-demi-baris dengan bahasa manusia.

---

## 1. Bagaimana Fitur Login Admin Bekerja? (Java)
**Lokasi File:** `rental-admin-java/src/drivora/LoginFrame.java` (pada bagian `performLogin()`)

Ini adalah urutan kode yang berjalan ketika admin mengetikkan email, password, dan menekan tombol *Login*.

```java
// 1. Mengambil teks email dan password yang diketik oleh admin di layar aplikasi
String email = emailField.getText().trim();
String password = new String(passwordField.getPassword());

// 2. Membuka jembatan koneksi ke database MySQL kita
try (Connection conn = Database.getConnection()) {
    
    // 3. Menyiapkan kalimat tanya (Query) untuk mencari admin di database berdasarkan emailnya
    String sql = "SELECT id, name, password FROM users WHERE email = ? AND role = 'admin'";
    try (PreparedStatement stmt = conn.prepareStatement(sql)) {
        
        // 4. Memasukkan email yang diketik tadi ke bagian tanda tanya (?) di atas
        stmt.setString(1, email);
        
        // 5. Menjalankan pencarian di database
        ResultSet rs = stmt.executeQuery();
        
        // 6. Jika datanya ketemu (berarti emailnya memang terdaftar sebagai admin)...
        if (rs.next()) {
            int adminId = rs.getInt("id"); // Ambil ID si admin
            String adminName = rs.getString("name"); // Ambil nama admin
            
            // Ambil data password acak (hash enkripsi) yang tersimpan di database
            String dbHash = rs.getString("password");
            
            // 7. TRIK KHUSUS: Karena web Laravel menggunakan format enkripsi $2y$, 
            // sedangkan library Java kita (jBCrypt) membacanya sebagai $2a$, 
            // maka kita harus akali dengan menukar huruf 'y' menjadi 'a' sementara.
            String javaHash = dbHash.replace("$2y$", "$2a$");
            
            // 8. Mengecek apakah password yang diketik sama dengan password acak di database
            if (BCrypt.checkpw(password, javaHash)) {
                // Jika cocok, tutup halaman login ini, dan buka Halaman Dashboard!
                DashboardFrame dashboard = new DashboardFrame(adminId, adminName);
                dashboard.setVisible(true);
                this.dispose(); 
            }
        }
    }
}
```

---

## 2. Bagaimana Admin Menerima Pesanan? (Java)
**Lokasi File:** `rental-admin-java/src/drivora/OrderPanel.java` (pada bagian `confirmOrder()`)

Ini yang terjadi saat ada pesanan masuk dan Admin mengklik tombol **"Konfirmasi Pesanan"**.

```java
// Memulai koneksi ke database
try (Connection conn = Database.getConnection()) {
    
    // LANGKAH 1: Mengubah status pesanan dari 'pending' menjadi 'active' (berjalan)
    String rentalSql = "UPDATE rentals SET status = 'active', admin_id = ? WHERE id = ?";
    try (PreparedStatement stmt = conn.prepareStatement(rentalSql)) {
        stmt.setInt(1, currentAdminId);   // Mencatat siapa admin yang mengklik persetujuan ini
        stmt.setInt(2, selectedRentalId); // Mencatat ID pesanan mana yang disetujui
        stmt.executeUpdate();             // Simpan ke database!
    }

    // LANGKAH 2: Mencatat uang yang masuk ke laporan "Income Sheet" (tabel transactions)
    String transSql = "INSERT INTO transactions (rental_id, car_id, transaction_type, amount, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
    try (PreparedStatement stmt = conn.prepareStatement(transSql)) {
        stmt.setInt(1, selectedRentalId); // Hubungkan dengan ID pesanannya
        stmt.setInt(2, selectedCarId);    // Hubungkan dengan ID mobilnya
        stmt.setString(3, "Sewa Baru");   // Beri label bahwa ini uang "Sewa Baru"
        stmt.setDouble(4, originalPrice); // Catat nominal harganya
        stmt.executeUpdate();             // Simpan sebagai riwayat keuangan!
    }

    // LANGKAH 3: Mengunci mobil agar tidak bisa disewa orang lain di website
    String carSql = "UPDATE cars SET status = 'rented' WHERE id = (SELECT car_id FROM rentals WHERE id = ?)";
    try (PreparedStatement stmt = conn.prepareStatement(carSql)) {
        stmt.setInt(1, selectedRentalId);
        stmt.executeUpdate();
    }
}
```

---

## 3. Bagaimana Pelanggan Memesan Mobil? (Web Laravel PHP)
**Lokasi File:** `rental-mobil/app/Http/Controllers/RentalController.php` (pada fungsi `store()`)

Berbeda dengan Java, ini adalah kode PHP yang berjalan ketika tombol **"Sewa Sekarang"** diklik oleh pelanggan di Website.

```php
public function store(Request $request, Car $car)
{
    // 1. Memeriksa ulang apakah mobil ini memang belum disewa orang lain
    if ($car->status !== 'available') {
        return back()->with('error', 'Mobil ini sudah tidak tersedia.');
    }

    // 2. Menerima tanggal mulai dan selesai dari kalender yang dipilih pelanggan, 
    // lalu menghitung total selisihnya dalam satuan "Jam".
    $startTime = \Carbon\Carbon::parse($request->start_time);
    $endTime = \Carbon\Carbon::parse($request->end_time);
    $durationHours = $startTime->diffInHours($endTime);

    // 3. Rumus Matematika: Total Harga = Durasi Jam x Harga Mobil Per Jam
    $totalPrice = $durationHours * $car->price_per_hour;

    // 4. Menyimpan data pesanan baru ke dalam laci database 'rentals'
    Rental::create([
        'user_id' => auth()->id(), // Mencatat pelanggan mana yang sedang login
        'car_id' => $car->id,      // Mencatat mobil mana yang dipilih
        'start_time' => $startTime,
        'end_time' => $endTime,
        'total_price' => $totalPrice,
        'status' => 'pending'      // Beri status 'pending' agar muncul di layar aplikasi Admin
    ]);

    // 5. Pindahkan layar pelanggan ke halaman Dashboard Pelanggan dan beri pesan Sukses
    return redirect()->route('dashboard')->with('success', 'Pesanan berhasil dibuat!');
}
```

---

### Tips Presentasi / Tanya Jawab Dosen:
- Jika Dosen bertanya: *"Gimana cara website (PHP) dan admin (Java) bisa sinkron?"*
  **Jawaban:** *"Karena keduanya menembak (query) ke satu database MySQL yang sama, Pak/Bu. Saat web mengubah status jadi 'pending' di database, aplikasi Java seketika membaca status 'pending' tersebut lewat Query `SELECT` dan menampilkannya di layar Admin."*
- Jika Dosen bertanya: *"Darimana laporan pemasukan kalian didapat?"*
  **Jawaban:** *"Kami membuat tabel terpisah bernama `transactions`. Setiap kali tombol konfirmasi diklik oleh admin, Java menggunakan perintah `INSERT INTO transactions` untuk merekam uang tersebut secara spesifik, apakah itu dari Sewa Baru, Perpanjangan, atau Denda."*
