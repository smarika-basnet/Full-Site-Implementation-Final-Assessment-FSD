<?php
// config/db.php

$host = "localhost";      
$dbname = "np03cs4a240123";   
$username = "np03cs4a240123";
$password = "rgV79FcYtX";          

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
