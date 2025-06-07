<?php
$host = 'localhost';              // leave this as localhost
$dbname = 'portfoliodb';          // your actual database name
$username = 'tomajto';     // the username you created in MySQL
$password = 'petrpavel397'; // the password you set

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
