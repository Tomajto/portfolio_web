<?php
include 'database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Protect against SQL injection
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            echo "<h2>✅ Login successful</h2>";
        } else {
            echo "<h3>❌ Invalid password</h3>";
        }
    } else {
        echo "<h3>❌ User not found</h3>";
    }

    $stmt->close();
}

$conn->close();
?>
