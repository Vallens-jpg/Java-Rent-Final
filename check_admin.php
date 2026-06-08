<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_drivora', 'root', '');
$stmt = $pdo->query('SELECT count(*) FROM users WHERE role="admin"');
echo "Admins: " . $stmt->fetchColumn() . "\n";
