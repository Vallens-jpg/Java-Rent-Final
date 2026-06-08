<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_drivora', 'root', '');
$stmt = $pdo->query('DESCRIBE rentals');
foreach($stmt as $row) { echo $row['Field'] . "\n"; }
