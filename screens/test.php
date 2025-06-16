<?php
session_start();
include '../database/db_connection.php';

if (!isset($_SESSION['email'])) {
    echo "<h1>Not logged in</h1>";
    echo "<p><a href='login.php'>Please log in first</a></p>";
    exit();
}

$userEmail = $_SESSION['email'];

$stmt = $conn->prepare("SELECT username, email, coins, profile_pic FROM users WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    echo "<h1>Logged In User Information</h1>";
    echo "<hr>";
    echo "<p><strong>Username:</strong> " . htmlspecialchars($user['username']) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
    echo "<p><strong>Coins:</strong> " . (int)$user['coins'] . " 🪙</p>";
} else {
    echo "<h1>User not found in database</h1>";
    echo "<p>Session email: " . htmlspecialchars($userEmail) . "</p>";
}

$stmt->close();
$conn->close();
?>