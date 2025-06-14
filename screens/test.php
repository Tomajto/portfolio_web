<?php
include_once '../database/datatabase.php';

$query = "SELECT username, email FROM users LIMIT 1";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $username = $user['username'];
    $email = $user['email'];
    
    echo "Username: " . $username . "<br>";
    echo "Email: " . $email . "<br>";
} else {
    echo "No user found in database.";
}


?>