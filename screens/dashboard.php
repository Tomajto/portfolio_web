<?php
session_start();
// Check if the user is logged in, if
// not then redirect them to the login page
if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
} else {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
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
        <div class="login-container" style="margin-top: 120px;">
            <div class="login-title">Welcome!</div>
            <p style="font-size:1.1rem; margin-bottom:1.5rem;">
                You are logged in as <strong><?php echo htmlspecialchars($userEmail); ?></strong>.
            </p>
            <div class="login-links">
                <a href="./logout.php" type="submit">Log out</a>
            </div>
        </div>
    </main>
    <script src="/scripts/hamburger.js"></script>
</body>
</html>