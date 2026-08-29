<?php
$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS connectify CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database 'connectify' created or already exists.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
