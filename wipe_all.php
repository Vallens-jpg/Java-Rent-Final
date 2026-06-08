<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_drivora', 'root', '');

// Matikan sementara foreign key checks agar bisa di-truncate
$pdo->query("SET FOREIGN_KEY_CHECKS = 0");

// Hapus bersih semua isi tabel
$pdo->query("TRUNCATE TABLE rentals");
$pdo->query("TRUNCATE TABLE cars");
$pdo->query("TRUNCATE TABLE users");

$pdo->query("SET FOREIGN_KEY_CHECKS = 1");

// Buat 1 akun Admin agar Anda tetap bisa login ke aplikasi Java Admin
$passwordHash = password_hash('password', PASSWORD_BCRYPT);
$pdo->query("INSERT INTO users (name, email, password, role, phone, created_at, updated_at) VALUES ('Admin Drivora', 'admin@drivora.com', '$passwordHash', 'admin', '08111111111', NOW(), NOW())");

echo "Database berhasil di-reset TOTAL!\n";
echo "Semua mobil, transaksi, dan user (termasuk admin lama) telah dihapus.\n";
echo "\n--- AKUN ADMIN BARU ---\n";
echo "Email: admin@drivora.com\n";
echo "Password: password\n";
echo "-----------------------\n";
echo "Silakan gunakan web Laravel Anda untuk membuat user/mobil baru, dan gunakan Java untuk mengetes fitur adminnya!";
?>
