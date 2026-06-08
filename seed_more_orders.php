<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_drivora', 'root', '');

// Insert dummy users if needed or just use random ones
$pdo->query("INSERT INTO users (name, email, password, role, phone, created_at, updated_at) VALUES ('Ahmad Fikri', 'ahmad@example.com', 'pwd', 'customer', '081234', NOW(), NOW())");
$u1 = $pdo->lastInsertId();

$pdo->query("INSERT INTO users (name, email, password, role, phone, created_at, updated_at) VALUES ('Siti Aminah', 'siti@example.com', 'pwd', 'customer', '08999', NOW(), NOW())");
$u2 = $pdo->lastInsertId();

$pdo->query("INSERT INTO users (name, email, password, role, phone, created_at, updated_at) VALUES ('Budi Santoso', 'budi@example.com', 'pwd', 'customer', '08777', NOW(), NOW())");
$u3 = $pdo->lastInsertId();

// Insert pending rentals with Car IDs 2, 3, 4
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, created_at, updated_at) VALUES ($u1, 2, NOW(), DATE_ADD(NOW(), INTERVAL 24 HOUR), 750000, 'pending', NOW(), NOW())");
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, created_at, updated_at) VALUES ($u2, 3, NOW(), DATE_ADD(NOW(), INTERVAL 6 HOUR), 200000, 'pending', NOW(), NOW())");
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, created_at, updated_at) VALUES ($u3, 4, NOW(), DATE_ADD(NOW(), INTERVAL 12 HOUR), 600000, 'pending', NOW(), NOW())");

echo "Inserted 3 pending orders.\n";
