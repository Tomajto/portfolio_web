<?php
$host = 'localhost'; // Database host
$dbname = getenv('DB_NAME'); 
$username = getenv('DB_USER');   
$password = getenv('DB_PASS');
$pdo = "";

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Removed echo for production use
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>