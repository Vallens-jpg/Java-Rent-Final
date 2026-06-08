<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=db_drivora", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert Admin
    $hash = password_hash("password", PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone, created_at, updated_at) VALUES (?, ?, ?, 'admin', '08123456789', NOW(), NOW())");
    $stmt->execute(['Admin Drivora', 'admin@drivora.com', $hash]);
    echo "Admin created.\n";

    // Insert Cars
    $cars = [
        ['Toyota Innova Reborn', '7 Seat', 'Automatic', 75000, 'B 1234 RBN', 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?q=80&w=800&auto=format&fit=crop'],
        ['Honda Brio Satya', '5 Seat', 'Manual', 45000, 'B 5678 SAT', 'https://images.unsplash.com/photo-1590362891991-f776e747a588?q=80&w=800&auto=format&fit=crop'],
        ['Toyota Avanza Veloz', '7 Seat', 'Automatic', 60000, 'B 9012 VLZ', 'https://images.unsplash.com/photo-1609521263047-f8f205293f24?q=80&w=800&auto=format&fit=crop'],
        ['Mitsubishi Pajero Sport', '7 Seat', 'Automatic', 120000, 'B 3456 PJR', 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=800&auto=format&fit=crop'],
        ['Daihatsu Ayla', '5 Seat', 'Manual', 40000, 'B 7890 AYL', 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?q=80&w=800&auto=format&fit=crop'],
        ['Toyota Fortuner GR', '7 Seat', 'Automatic', 130000, 'B 1122 FTN', 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?q=80&w=800&auto=format&fit=crop']
    ];

    $stmtCar = $pdo->prepare("INSERT INTO cars (brand, size, transmission, price_per_hour, plate_number, image, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 'available', NOW(), NOW())");
    
    foreach ($cars as $car) {
        $stmtCar->execute($car);
    }
    echo "Cars inserted successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
