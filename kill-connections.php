<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=skill_connect', 'root', '');
    $stmt = $pdo->query('SHOW PROCESSLIST');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $currentId = $pdo->query('SELECT CONNECTION_ID()')->fetchColumn();
    foreach ($rows as $row) {
        $id = $row['Id'];
        if ($id != $currentId) {
            echo "KILL $id (User: {$row['User']}, Info: " . substr($row['Info'] ?? '', 0, 50) . ")\n";
            try {
                $pdo->exec("KILL $id");
            } catch (Exception $ex) {
                echo "Failed to kill $id: " . $ex->getMessage() . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
