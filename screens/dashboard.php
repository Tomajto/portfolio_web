<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Get user info from database
include '../database/db_connection.php';
$userEmail = $_SESSION['email'];

$stmt = $conn->prepare("SELECT username, email FROM users WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard | Richtr</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="stylesheet" href="/styles/login.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link rel="icon" href="/assets/icon.png"/>
</head>
<body>
    <header>
        <div class="container navbar">
            <a href="../index.php" class="logo">Richtr</a>
            <nav class="nav-links">
                <a href="../index.php#overview">Overview</a>
                <a href="../index.php#features">Features</a>
                <a href="../index.php#photos">Photos</a>
                <a href="../index.php#aboutme">About me</a>
            </nav>
            <a href="logout.php" class="btn-order">Log out</a>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="mobile-menu" id="mobileMenu">
            <ul>
                <li><a href="../index.php#overview">Overview</a></li>
                <li><a href="../index.php#features">Features</a></li>
                <li><a href="../index.php#photos">Photos</a></li>
                <li><a href="../index.php#aboutme">About me</a></li>
                <li>
                    <a href="logout.php" class="btn-order-mobile" style="padding: 0.5rem 1rem; margin-top: 1rem">Log out</a>
                </li>
            </ul>
        </div>
    </header>
    <main>
        <div class="login-container" style="margin-top: 120px; max-width: 500px;">
            <div class="login-title">Dashboard</div>
            
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                <h3 style="margin-bottom: 1rem; color: #6b21a8;">Your Account Info</h3>
                <p style="margin-bottom: 0.5rem;"><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p style="margin-bottom: 0;"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <div class="login-links">
                <a href="logout.php">Log out</a> | <a href="../index.php">Go to Home</a>
            </div>
        </div>
    </main>
    <script src="/scripts/hamburger.js"></script>
</body>
</html>