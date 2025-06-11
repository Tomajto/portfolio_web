<?php
session_start();
include '../database/db_connection.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "Please fill in all fields!";
        $toastClass = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $toastClass = "error";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Email already exists!";
            $toastClass = "error";
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                // Set session variables and redirect to dashboard
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                $message = "Registration failed. Please try again.";
                $toastClass = "error";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Log in | Richtr</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="stylesheet" href="/styles/login.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap"
        rel="stylesheet" />
    <link rel="icon" href="/assets/icon.png" />
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
            <a href="#" class="btn-order" style="pointer-events: none; opacity: 0.7">Log in</a>
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
                    <a
                        href="#"
                        class="btn-order-mobile"
                        style="
                padding: 0.5rem 1rem;
                margin-top: 1rem;
                pointer-events: none;
                opacity: 0.7;
              ">Log in</a>
                </li>
            </ul>
        </div>
    </header>
    <main>
        <div class="login-container">
            <div class="login-title">Sign Up</div>

            <?php if ($message): ?>
                <div class="message <?php echo $toastClass; ?>" style="margin-bottom: 1rem; padding: 0.5rem; border-radius: 5px; <?php echo $toastClass === 'error' ? 'background-color: #fee; color: #c33;' : 'background-color: #efe; color: #3c3;'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="">
                <div>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required />
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required />
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div>
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required />
                </div>
                <button type="submit" class="login-btn">Sign Up</button>
            </form>

            <div class="login-links">
                <a href="login.php">Already have an account? Log in</a>
            </div>
        </div>
    </main>
</body>

</html>