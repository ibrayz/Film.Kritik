<?php
$host = "localhost";
$db   = "filmdb";
$user = "root";
$pass = ""; // XAMPP varsayılan

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("DB bağlantı hatası: " . $e->getMessage());
}
