<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=skill_connect', 'root', '');
    $stmt = $pdo->query('SHOW PROCESSLIST');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
