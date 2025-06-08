<?php 
include 'database.php';
$user_name = $_POST['username'];
$user_email = $_POST['email'];
$user_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
    echo "<h2>✅ Registration successful</h2><a href='/screens/login.html'>Login here</a>";
} else {
    echo "<h3>❌ Error: " . $stmt->error . "</h3>";
}
?>