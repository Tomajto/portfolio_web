<?php
$host = '185.180.3.139';
$dbname = getenv('DB_NAME'); 
$username = getenv('DB_USER');   
$password = getenv('DB_PASS');
$conn = "";

try {
    $conn = mysqli_connect($host, $username, $password, $dbname);
    echo "Connection Successful to the database: {$dbname} with host {$host}, logged in as {$username}";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>