<?php
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
                // Generate unique filename
                $newFileName = uniqid('profile_', true) . '.' . $fileExt;
                $fileDestination = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Update database
                    $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE email = ?");
                    $stmt->bind_param("ss", $newFileName, $userEmail);
                    
                    if ($stmt->execute()) {
                        $message = "Profile picture updated successfully!";
                        $messageType = "success";
                    } else {
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

// Get user info from database
$stmt = $conn->prepare("SELECT username, email, profile_pic FROM users WHERE email = ?");
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
        <div class="login-container">
            <div class="login-title">Dashboard</div>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>" style="margin-bottom: 1rem; padding: 0.5rem; border-radius: 5px; <?php echo $messageType === 'error' ? 'background-color: #fee; color: #c33;' : 'background-color: #efe; color: #3c3;'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Profile Picture Section -->
            <div class="upload-section">
                <img src="<?php echo $user['profile_pic'] ? '../uploads/profile_pics/' . htmlspecialchars($user['profile_pic']) : '../assets/default-avatar.png'; ?>" 
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
                        <div class="login-btn">Choose Profile Picture</div>
                    </div>
                    <div id="uploadStatus" style="margin-top: 1rem; display: none;">
                        <span style="color: #6b21a8; font-weight: 500;">Uploading...</span>
                    </div>
                </form>
            </div>
            
            <!-- Account Info Section -->
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
                }, 500); // Small delay to show the preview
            }
        }
    </script>
    <script src="/scripts/hamburger.js"></script>
</body>
</html>