<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_drivora', 'root', '');

// Disable foreign key checks for clean wiping
$pdo->query("SET FOREIGN_KEY_CHECKS = 0");

// Wipe tables
$pdo->query("TRUNCATE TABLE rentals");
// Delete all customers (keep admin)
$pdo->query("DELETE FROM users WHERE role = 'customer'");
// Delete all cars
$pdo->query("TRUNCATE TABLE cars");

$pdo->query("SET FOREIGN_KEY_CHECKS = 1");

// 1. Insert 5 Cars
$cars = [
    ['brand' => 'Toyota Avanza', 'size' => 7, 'transmission' => 'Manual', 'plate_number' => 'B 1234 ABC', 'price_per_hour' => 25000, 'status' => 'available'],
    ['brand' => 'Honda Brio', 'size' => 4, 'transmission' => 'Automatic', 'plate_number' => 'D 5678 DEF', 'price_per_hour' => 20000, 'status' => 'rented'],
    ['brand' => 'Mitsubishi Pajero', 'size' => 7, 'transmission' => 'Automatic', 'plate_number' => 'L 9012 GHI', 'price_per_hour' => 50000, 'status' => 'rented'],
    ['brand' => 'Suzuki Ertiga', 'size' => 7, 'transmission' => 'Manual', 'plate_number' => 'AB 3456 JKL', 'price_per_hour' => 25000, 'status' => 'available'],
    ['brand' => 'Toyota Alphard', 'size' => 6, 'transmission' => 'Automatic', 'plate_number' => 'B 7890 MNO', 'price_per_hour' => 100000, 'status' => 'available']
];

$carIds = [];
foreach ($cars as $car) {
    $stmt = $pdo->prepare("INSERT INTO cars (brand, size, transmission, plate_number, price_per_hour, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([$car['brand'], $car['size'], $car['transmission'], $car['plate_number'], $car['price_per_hour'], $car['status']]);
    $carIds[] = $pdo->lastInsertId();
}

// 2. Insert 5 Customers
$users = [
    ['name' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'phone' => '081234567890'],
    ['name' => 'Siti Aminah', 'email' => 'siti@gmail.com', 'phone' => '082345678901'],
    ['name' => 'Andi Wijaya', 'email' => 'andi@gmail.com', 'phone' => '083456789012'],
    ['name' => 'Rina Melati', 'email' => 'rina@gmail.com', 'phone' => '084567890123'],
    ['name' => 'Dewi Lestari', 'email' => 'dewi@gmail.com', 'phone' => '085678901234']
];

$userIds = [];
foreach ($users as $user) {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone, created_at, updated_at) VALUES (?, ?, 'dummy', 'customer', ?, NOW(), NOW())");
    $stmt->execute([$user['name'], $user['email'], $user['phone']]);
    $userIds[] = $pdo->lastInsertId();
}

// Fetch Admin ID
$stmt = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
$adminId = $stmt->fetchColumn() ?: 1;

// 3. Insert 5 Rentals (Transactions)
// Transaction 1: Pending (Order Panel) - Avanza
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, created_at, updated_at) VALUES ({$userIds[0]}, {$carIds[0]}, DATE_ADD(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL 13 HOUR), 300000, 'pending', NOW(), NOW())");

// Transaction 2: Pending (Order Panel) - Ertiga
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, created_at, updated_at) VALUES ({$userIds[1]}, {$carIds[3]}, DATE_ADD(NOW(), INTERVAL 2 HOUR), DATE_ADD(NOW(), INTERVAL 26 HOUR), 600000, 'pending', NOW(), NOW())");

// Transaction 3: Active (Return Panel - On Time) - Brio (Rented 2 hours ago, due in 5 hours)
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, admin_id, created_at, updated_at) VALUES ({$userIds[2]}, {$carIds[1]}, DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_ADD(NOW(), INTERVAL 5 HOUR), 140000, 'active', {$adminId}, NOW(), NOW())");

// Transaction 4: Active (Return Panel - OVERDUE!) - Pajero (Rented 24 hours ago, due 2 hours ago)
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, admin_id, created_at, updated_at) VALUES ({$userIds[3]}, {$carIds[2]}, DATE_SUB(NOW(), INTERVAL 24 HOUR), DATE_SUB(NOW(), INTERVAL 2 HOUR), 1100000, 'active', {$adminId}, NOW(), NOW())");

// Transaction 5: Completed (Income Sheet) - Alphard (Rented yesterday, returned today)
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, admin_id, created_at, updated_at) VALUES ({$userIds[4]}, {$carIds[4]}, DATE_SUB(NOW(), INTERVAL 24 HOUR), DATE_SUB(NOW(), INTERVAL 12 HOUR), 1200000, 'completed', {$adminId}, DATE_SUB(NOW(), INTERVAL 12 HOUR), DATE_SUB(NOW(), INTERVAL 12 HOUR))");

echo "Seeding Final Test Berhasil! 5 Data Mobil, 5 User, dan 5 Transaksi telah dibuat.";
?>
