<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=db_drivora", "root", "");
    
    echo "=== ADMIN USERS ===\n";
    $admins = $pdo->query("SELECT * FROM users WHERE role='admin'")->fetchAll(PDO::FETCH_ASSOC);
    print_r($admins);
    
    echo "\n=== CARS ===\n";
    $cars = $pdo->query("SELECT * FROM cars LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    print_r($cars);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
