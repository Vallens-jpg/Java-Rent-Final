<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_drivora', 'root', '');
$stmt = $pdo->query('SELECT email, password FROM users');
foreach($stmt as $row) { echo $row['email'] . ' - ' . substr($row['password'], 0, 15) . "\n"; }
