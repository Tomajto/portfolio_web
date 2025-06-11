<?php
$host = 'localhost'; // Database host
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn) {
    echo "Connected successfully to the database.";
    echo $host . $dbname . $username;

}
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>