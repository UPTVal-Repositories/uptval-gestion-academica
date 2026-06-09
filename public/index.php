<?php

$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_DATABASE') ?: 'gestion_academica';
$user = getenv('DB_USERNAME') ?: 'db_user';
$pass = getenv('DB_PASSWORD') ?: 'db_password';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h1>Conexión exitosa a MySQL 9.0</h1>";
} catch (PDOException $e) {
    echo "<h1>Error: " . $e->getMessage() . "</h1>";
}