<?php
$host = 'localhost';
$dbname = getenv('DB_NAME'); 
$username = getenv('DB_USER');   
$password = getenv('DB_PASS');
$conn = "";

try {
    $conn = mysqli_connect($host, $username, $password, $dbname);
} catch (Exception $e) {
    echo "Connection Failed";
    echo "<h3>" . $e->getMessage() . "</h3>";
    exit;
}
?>