<?php
$host = 'localhost';
$db = 'duzensiz_fiiller';  // Veritabanı adınızı buraya yazın
$user = 'root';     // Veritabanı kullanıcı adınızı buraya yazın
$pass = '';     // Veritabanı şifrenizi buraya yazın

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
