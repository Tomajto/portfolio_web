<?php
session_start();
include '../database/db_connection.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Prepare and execute - get both username and password
  $stmt = $conn->prepare("SELECT username, password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $db_password = $user['password'];
    $username = $user['username'];

    // Check if password is hashed or plain text
    if (password_verify($password, $db_password) || $password === $db_password) {
      // Login successful
      $_SESSION['email'] = $email;
      $_SESSION['username'] = $username;
      header("Location: dashboard.php");
      exit();
    } else {
      $message = "Incorrect password";
      $toastClass = "error";
    }
  } else {
    $message = "Email not found";
    $toastClass = "error";
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="cs">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Log in | Richtr</title>
  <link rel="stylesheet" href="/styles/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap"
    rel="stylesheet" />
  <link rel="icon" href="/assets/icon.png" />
</head>

<body>
  <?php include '../includes/header.php'; ?>
  <main>
    <div class="login-container">
      <div class="login-title">Log in</div>

      <?php if ($message): ?>
        <div class="message <?php echo $toastClass; ?>" style="margin-bottom: 1rem; padding: 0.5rem; border-radius: 5px; <?php echo $toastClass === 'error' ? 'background-color: #fee; color: #c33;' : 'background-color: #efe; color: #3c3;'; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <form class="login-form" method="POST" action="">
        <div>
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            required
            autocomplete="username" />
        </div>
        <div>
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password" />
        </div>
        <button type="submit" class="login-btn">Log in</button>
      </form>
      <div class="login-links">
        <a href="#">Forgot password?</a> |
        <a href="/screens/signup.php">Create account</a>
      </div>
    </div>
  </main>
  <?php include '../includes/footer.php'; ?>
  <script src="/scripts/hamburger.js"></script>
</body>

</html>