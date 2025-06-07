<?php 
include 'database.php';
$user_email = $_POST['email'];
$user_confirm_email = $_POST['confirm-email'];
$user_password = $_POST['password'];

echo $user_email . "<br>";
echo $user_confirm_email . "<br>";
echo $user_password . "<br>";

?>