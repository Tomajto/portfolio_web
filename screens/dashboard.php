<?php
// filepath: c:\PROJEKTY\portfolio_web\screens\dashboard.php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include '../database/db_connection.php';
$userEmail = $_SESSION['email'];

$message = "";
$messageType = "";

// Handle daily coin collection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['collect_coins'])) {
    // Get user's last collection time and current coins
    $stmt = $conn->prepare("SELECT last_coin_collection, coins FROM users WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $lastCollection = $userData['last_coin_collection'];
    $stmt->close();
    
    $now = new DateTime();
    $canCollect = true;
    
    if ($lastCollection) {
        $lastCollectionTime = new DateTime($lastCollection);
        $timeDiff = $now->diff($lastCollectionTime);
        $hoursSinceLastCollection = $timeDiff->days * 24 + $timeDiff->h;
        
        if ($hoursSinceLastCollection < 20) {
            $hoursLeft = 20 - $hoursSinceLastCollection;
            $minutesLeft = 60 - $timeDiff->i;
            if ($minutesLeft == 60) {
                $minutesLeft = 0;
                $hoursLeft--;
            }
            
            $message = "You can collect coins again in {$hoursLeft}h {$minutesLeft}m";
            $messageType = "error";
            $canCollect = false;
        }
    }
    
    if ($canCollect) {
        // Add 50 coins and update last collection time
        $stmt = $conn->prepare("UPDATE users SET coins = coins + 50, last_coin_collection = NOW() WHERE email = ?");
        $stmt->bind_param("s", $userEmail);
        
        if ($stmt->execute()) {
            $message = "🎉 You collected 50 coins! Come back in 20 hours for more.";
            $messageType = "success";
        } else {
            $message = "Error collecting coins. Please try again.";
            $messageType = "error";
        }
        $stmt->close();
        
        // Redirect to prevent form resubmission
        header("Location: dashboard.php?collected=1");
        exit();
    }
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $uploadDir = '../uploads/profile_pics/';

    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['profile_pic'];
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];

    // Get file extension
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

    if ($fileError === 0) {
        if (in_array($fileExt, $allowedExt)) {
            if ($fileSize < 5000000) { // 5MB limit
                
                // Get current profile picture before updating
                $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE email = ?");
                $stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $result = $stmt->get_result();
                $currentUser = $result->fetch_assoc();
                $oldProfilePic = $currentUser['profile_pic'];
                $stmt->close();
                
                // Generate unique filename
                $newFileName = uniqid('profile_', true) . '.' . $fileExt;
                $fileDestination = "{$uploadDir}{$newFileName}";

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Update database with new profile picture
                    $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE email = ?");
                    $stmt->bind_param("ss", $newFileName, $userEmail);

                    if ($stmt->execute()) {
                        // Delete old profile picture if it exists and it's not the default
                        if ($oldProfilePic && $oldProfilePic !== 'default-avatar.png') {
                            $oldFilePath = "{$uploadDir}{$oldProfilePic}";
                            if (file_exists($oldFilePath)) {
                                unlink($oldFilePath);
                            }
                        }
                        
                        // Redirect to prevent form resubmission
                        header("Location: dashboard.php?uploaded=1");
                        exit();
                    } else {
                        // If database update fails, remove the newly uploaded file
                        if (file_exists($fileDestination)) {
                            unlink($fileDestination);
                        }
                        $message = "Database error occurred.";
                        $messageType = "error";
                    }
                    $stmt->close();
                } else {
                    $message = "Failed to upload file.";
                    $messageType = "error";
                }
            } else {
                $message = "File size too large (max 5MB).";
                $messageType = "error";
            }
        } else {
            $message = "Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.";
            $messageType = "error";
        }
    } else {
        $message = "Error uploading file.";
        $messageType = "error";
    }
}

// Check for success messages from redirects
if (isset($_GET['collected'])) {
    $message = "🎉 You collected 50 coins! Come back in 20 hours for more.";
    $messageType = "success";
}
if (isset($_GET['uploaded'])) {
    $message = "Profile picture updated successfully!";
    $messageType = "success";
}

// Get user info from database (ALWAYS fetch fresh data)
$stmt = $conn->prepare("SELECT username, email, profile_pic, coins, last_coin_collection FROM users WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Debug: Check what data we actually got
    error_log("User data fetched: " . print_r($user, true));
    
    // Ensure coins is set to 0 if null
    if (!isset($user['coins']) || $user['coins'] === null) {
        $user['coins'] = 0;
    }
} else {
    // If user not found, redirect to login
    error_log("No user found for email: " . $userEmail);
    error_log("Session email: " . $userEmail);
    header("Location: login.php");
    exit();
}
$stmt->close();

// Check if user can collect coins
$canCollectCoins = true;
$timeUntilNextCollection = "";

if ($user['last_coin_collection']) {
    $now = new DateTime();
    $lastCollection = new DateTime($user['last_coin_collection']);
    $timeDiff = $now->diff($lastCollection);
    $hoursSinceLastCollection = ($timeDiff->days * 24) + $timeDiff->h;
    
    if ($hoursSinceLastCollection < 20) {
        $canCollectCoins = false;
        $hoursLeft = 20 - $hoursSinceLastCollection;
        $minutesLeft = 60 - $timeDiff->i;
        if ($minutesLeft >= 60) {
            $minutesLeft = 0;
            $hoursLeft++;
        }
        $timeUntilNextCollection = "{$hoursLeft}h {$minutesLeft}m";
    }
}
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard | Richtr</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link rel="icon" href="/assets/icon.png" />
</head>

<body class="login-page">
    <?php include '../includes/header.php'; ?>
    <main>
        <div class="login-container">
            <div class="login-title">Dashboard</div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>" style="margin-bottom: 1rem; padding: 0.5rem; border-radius: 5px; <?php echo $messageType === 'error' ? 'background-color: #fee; color: #c33;' : 'background-color: #efe; color: #3c3;'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Profile Picture Section -->
            <div class="upload-section">
                <img src="<?php echo !empty($user['profile_pic']) ? '../uploads/profile_pics/' . htmlspecialchars($user['profile_pic']) : '../assets/default-avatar.png'; ?>"
                    alt="Profile Picture"
                    class="profile-pic-dashboard"
                    id="profilePreview">

                <form method="POST" enctype="multipart/form-data" id="profileForm">
                    <div class="file-input-wrapper">
                        <input type="file"
                            name="profile_pic"
                            accept="image/*"
                            class="file-input"
                            id="profileInput"
                            onchange="autoUpload()">
                        <div class="file-input-button">Choose Profile Picture</div>
                    </div>
                    <div id="uploadStatus" style="margin-top: 1rem; display: none;">
                        <span style="color: #6b21a8; font-weight: 500;">Uploading...</span>
                    </div>
                </form>
            </div>

            <!-- Account Info Section -->
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                <h3 style="margin-bottom: 1rem; color: #6b21a8;">Your Account Info</h3>
                <p style="margin-bottom: 0.5rem;"><strong>Username:</strong> <?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></p>
                <p style="margin-bottom: 1rem;"><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                <p style="margin-bottom: 1rem;"><strong>Coins:</strong> <span id="userCoins"><?php echo htmlspecialchars((int)($user['coins'] ?? 0)); ?></span> 🪙</p>
                
                <!-- Daily Coin Collection -->
                <div class="daily-coins-section">
                    <?php if ($canCollectCoins): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="collect_coins" value="1">
                            <button type="submit" class="collect-coins-btn">
                                🎁 Collect 50 Coins
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="collect-coins-btn disabled" disabled>
                            ⏰ Next collection in <?php echo $timeUntilNextCollection; ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="login-links">
                <a href="logout.php">Log out</a> | <a href="../index.php">Go to Home</a> | <a href="slots.php">Play Slots</a>
            </div>
        </div>
    </main>
    <script>
        function autoUpload() {
            const fileInput = document.getElementById('profileInput');
            const form = document.getElementById('profileForm');
            const uploadStatus = document.getElementById('uploadStatus');
            const profilePreview = document.getElementById('profilePreview');

            if (fileInput.files && fileInput.files[0]) {
                // Show upload status
                uploadStatus.style.display = 'block';

                // Preview the selected image
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                };
                reader.readAsDataURL(fileInput.files[0]);

                // Auto-submit the form
                setTimeout(() => {
                    form.submit();
                }, 500);
            }
        }
    </script>
    <script src="/scripts/hamburger.js"></script>
</body>

</html>