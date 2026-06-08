<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=db_drivora", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert Dummy Customer
    $hash = password_hash("password", PASSWORD_BCRYPT);
    $stmtUser = $pdo->prepare("INSERT INTO users (name, email, password, role, phone, created_at, updated_at) VALUES (?, ?, ?, 'customer', '08123456789', NOW(), NOW())");
    $stmtUser->execute(['Budiono Siregar', 'budiono@example.com', $hash]);
    $userId = $pdo->lastInsertId();
    echo "Customer 'Budiono Siregar' created. ID: $userId\n";

    // Insert Dummy Rental (Pending)
    // We will use Car ID = 1 (Innova Reborn, assuming it exists from previous seeder)
    $stmtRental = $pdo->prepare("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, created_at, updated_at) VALUES (?, 1, NOW(), DATE_ADD(NOW(), INTERVAL 12 HOUR), 900000, 'pending', NOW(), NOW())");
    $stmtRental->execute([$userId]);
    echo "Pending Rental created.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
