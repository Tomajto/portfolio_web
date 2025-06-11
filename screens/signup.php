<?php
include '../database/db_connection.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists
    $checkEmailStmt = $conn->prepare("SELECT email FROM userdata WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        $message = "Email ID already exists";
        $toastClass = "#007bff"; // Primary color
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO userdata (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $message = "Account created successfully";
            $toastClass = "#28a745"; // Success color
        } else {
            $message = "Error: " . $stmt->error;
            $toastClass = "#dc3545"; // Danger color
        }

        $stmt->close();
    }

    $checkEmailStmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="cs">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign up | Richtr</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="stylesheet" href="/styles/login.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap"
      rel="stylesheet"
    />
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
        <a href="#" class="btn-order" style="pointer-events: none; opacity: 0.7"
          >Sign up</a
        >
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
              "
              >Sign up</a
            >
          </li>
        </ul>
      </div>
    </header>
    <main>
      <?php if ($message): ?>
            <div class="toast align-items-center text-white border-0" 
          role="alert" aria-live="assertive" aria-atomic="true"
                style="background-color: <?php echo $toastClass; ?>;">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close
                    btn-close-white me-2 m-auto" 
                          data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
      <div class="login-container" method="post">
        <div class="login-title">Sign up</div>
        <form class="login-form" autocomplete="off" action="" method="POST">
          <div>
            <label for="email">Username</label>
            <input
              type="name"
              id="username"
              name="username"
              autocomplete="username"
              method="post"
            />
          </div>
          <div>
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              autocomplete="username"
              method="post"
            />
          </div>
          <div>
            <label for="password">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              autocomplete="current-password"
              method="post"
            />
          </div>
          <button type="submit" class="login-btn">Sign up</button>
        </form>
        <div class="login-links">
          <p>
            By signing up, you agree to our
            <a href="/screens/terms.php">Terms of Service</a> and
            <a href="/screens/privacy.php">Privacy Policy</a>.
          </p>
          <p>
            Already have an account?
            <a href="/screens/login.php">Log back in</a>
          </p>
        </div>
      </div>
    </main>
    <script src="/scripts/hamburger.js"></script>
  </body>
</html>
