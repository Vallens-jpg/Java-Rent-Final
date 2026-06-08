<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_drivora', 'root', '');
$pdo->query("ALTER TABLE rentals ADD COLUMN admin_id bigint(20) unsigned NULL");
$pdo->query("UPDATE rentals SET admin_id = 1");
echo "admin_id added successfully";
