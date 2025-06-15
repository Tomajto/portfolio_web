<?php
session_start();
require_once __DIR__ . '/../database/db_connection.php';
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
        <a href="/index.php" class="logo">Richtr</a>
        
        <nav class="nav-links">
            <a href="../screens/slots.php">Slots</a>
            <a href="../screens/ride_the_bus.php">Ride the bus</a>
            <a href="../screens/black_jack.php">Black jack</a>
            <a href="../screens/leaderboard.php">Leaderboard</a>
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
        
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    
    <div class="mobile-menu" id="mobileMenu">
        <ul>
            <li><a href="../screens/slots.php">Slots</a></li>
            <li><a href="../screens/ride_the_bus.php">Ride the bus</a></li>
            <li><a href="../screens/black_jack.php">Black jack</a></li>
            <li><a href="../screens/leaderboard.php">Leaderboard</a></li>
            
            <?php if ($isLoggedIn): ?>
                <li class="profile-container-mobile">
                    <img src="<?php echo $userProfilePic ? '/uploads/profile_pics/' . htmlspecialchars($userProfilePic) : '/assets/default-avatar.png'; ?>" 
                         alt="Profile" 
                         class="profile-pic-nav"
                         onclick="location.href='<?php echo strpos($_SERVER['PHP_SELF'], '/screens/') !== false ? '' : 'screens/'; ?>dashboard.php'">
                </li>
            <?php else: ?>
                <li>
                    <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/screens/') !== false ? '' : 'screens/'; ?>login.php" class="btn-order-mobile" style="padding: 0.5rem 1rem; margin-top: 1rem">Log in</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</header>