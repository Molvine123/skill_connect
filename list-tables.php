<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=skill_connect', 'root', '');
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "TABLES:\n" . implode("\n", $tables) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
