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
          >Log in</a
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
              >Log in</a
            >
          </li>
        </ul>
      </div>
    </header>
    <main>
      <div class="login-container">
        <div class="login-title">Log in</div>
        <form class="login-form" autocomplete="off">
          <div>
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              required
              autocomplete="username"
            />
          </div>
          <div>
            <label for="password">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              required
              autocomplete="current-password"
            />
          </div>
          <button type="submit" class="login-btn">Log in</button>
        </form>
        <div class="login-links">
          <a href="#">Forgot password?</a> |
          <a href="/screens/signup.php">Create account</a>
        </div>
      </div>
    </main>
    <script src="/scripts/hamburger.js"></script>
  </body>
</html>
