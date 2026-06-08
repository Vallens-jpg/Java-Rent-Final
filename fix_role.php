<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_drivora', 'root', '');
$pdo->query("UPDATE users SET role = 'admin' WHERE email = 'areta@gmail.com'");
echo "Role updated";
