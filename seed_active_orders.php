<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_drivora', 'root', '');

// Car 5 (Daihatsu Ayla) rented 10 hours ago, due in 2 hours (On-Time)
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, created_at, updated_at) VALUES (1, 5, DATE_SUB(NOW(), INTERVAL 10 HOUR), DATE_ADD(NOW(), INTERVAL 2 HOUR), 400000, 'active', NOW(), NOW())");
$pdo->query("UPDATE cars SET status = 'rented' WHERE id = 5");

// Car 6 (Toyota Fortuner) rented 24 hours ago, due 2 hours ago (Overdue!)
$pdo->query("INSERT INTO rentals (user_id, car_id, start_time, end_time, total_price, status, created_at, updated_at) VALUES (1, 6, DATE_SUB(NOW(), INTERVAL 24 HOUR), DATE_SUB(NOW(), INTERVAL 2 HOUR), 1300000, 'active', NOW(), NOW())");
$pdo->query("UPDATE cars SET status = 'rented' WHERE id = 6");

echo "Seeded 2 active rentals (1 normal, 1 overdue).";
