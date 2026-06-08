<?php
function checkDB($name) {
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=$name", "root", "");
        echo "=== DB: $name ===\n";
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $t) {
            echo "Table: $t\n";
            $cols = $pdo->query("DESCRIBE `$t`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cols as $c) {
                echo "  - " . $c['Field'] . " (" . $c['Type'] . ")\n";
            }
        }
    } catch (Exception $e) {
        echo "Could not connect to $name: " . $e->getMessage() . "\n";
    }
}
checkDB('db_drivora');
checkDB('rental');
