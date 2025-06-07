<?php
$host = 'localhost';
$dbname = getenv('DB_NAME'); 
$username = getenv('DB_USER');   
$password = getenv('DB_PASS');
$conn = "";

$conn = mysqli_connect($host, $username, $password, $dbname);



if ($conn) {
    echo "<h2>✅ Connection Successful</h2>";
    echo "<h3>Connected to database '$dbname' as user '$username'</h3>";
}
else {
    echo "<h2>❌ Connection Failed</h2>";
    echo "<h3>" . mysqli_connect_error() . "</h3>";
}
?>