<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->exec('DROP DATABASE IF EXISTS skill_connect');
    $pdo->exec('CREATE DATABASE skill_connect');
    echo "DATABASE RECREATED SUCCESS\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
