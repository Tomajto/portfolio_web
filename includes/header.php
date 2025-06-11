<?php
// filepath: c:\PROJEKTY\portfolio_web\includes\header.php
session_start();
$isLoggedIn = isset($_SESSION['email']);
$userProfilePic = null;

if ($isLoggedIn) {
    // Get user profile pic from database
    include_once 'database/db_connection.php';
    $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $userProfilePic = $user['profile_pic'];
    }
    $stmt->close();
}
?>

<header>
    <div class="container navbar">
        <div class="mobile-header-left">
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <a href="/index.php" class="logo">Richtr</a>
        </div>
        
        <nav class="nav-links">
            <a href="/index.php#overview">Overview</a>
            <a href="/index.php#features">Features</a>
            <a href="/index.php#photos">Photos</a>
            <a href="/index.php#aboutme">About me</a>
        </nav>
        
        <?php if ($isLoggedIn): ?>
            <div class="profile-container">
                <img src="<?php echo $userProfilePic ? '/uploads/profile_pics/' . htmlspecialchars($userProfilePic) : '/assets/default-avatar.png'; ?>" 
                     alt="Profile" 
                     class="profile-pic-nav" 
                     onclick="location.href='<?php echo strpos($_SERVER['PHP_SELF'], '/screens/') !== false ? '' : 'screens/'; ?>dashboard.php'">
            </div>
        <?php else: ?>
            <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/screens/') !== false ? '' : 'screens/'; ?>login.php" class="btn-order">Log in</a>
        <?php endif; ?>
    </div>
    
    <div class="mobile-menu" id="mobileMenu">
        <ul>
            <?php if ($isLoggedIn): ?>
                <li class="mobile-profile">
                    <img src="<?php echo $userProfilePic ? '/uploads/profile_pics/' . htmlspecialchars($userProfilePic) : '/assets/default-avatar.png'; ?>" 
                         alt="Profile" 
                         class="profile-pic-mobile"
                         onclick="location.href='<?php echo strpos($_SERVER['PHP_SELF'], '/screens/') !== false ? '' : 'screens/'; ?>dashboard.php'">
                    <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                </li>
            <?php endif; ?>
            <li><a href="/index.php#overview">Overview</a></li>
            <li><a href="/index.php#features">Features</a></li>
            <li><a href="/index.php#photos">Photos</a></li>
            <li><a href="/index.php#aboutme">About me</a></li>
            <li>
                <?php if (!$isLoggedIn): ?>
                    <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/screens/') !== false ? '' : 'screens/'; ?>login.php" class="btn-order-mobile" style="padding: 0.5rem 1rem; margin-top: 1rem">Log in</a>
                <?php else: ?>
                    <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/screens/') !== false ? '' : 'screens/'; ?>logout.php" class="btn-order-mobile" style="padding: 0.5rem 1rem; margin-top: 1rem; background-color: #dc3545;">Log out</a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</header>