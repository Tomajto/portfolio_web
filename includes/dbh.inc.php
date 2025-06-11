<?php
$host = 'localhost'; // Database host
$dbname = getenv('DB_NAME'); 
$username = getenv('DB_USER');   
$password = getenv('DB_PASS');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>