<!DOCTYPE html>
<html lang="cs">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Richtr</title>
    <link rel="stylesheet" href="/styles/style.css" />
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
        <a href="#" class="logo">Richtr</a>
        <nav class="nav-links">
          <a href="#overview">Overview</a>
          <a href="#features">Features</a>
          <a href="#photos">Photos</a>
          <a href="../index.php#aboutme">About me</a>
        </nav>
        <a href="screens/login.php" class="btn-order">Log in</a>
        <div class="hamburger" id="hamburger">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
      <div class="mobile-menu" id="mobileMenu">
        <ul>
          <li><a href="#overview">Overview</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#photos">Photos</a></li>
          <li><a href="../index.php#aboutme">About me</a></li>
          <li>
            <a
              href="screens/login.php"
              class="btn-order-mobile"
              style="padding: 0.5rem 1rem; margin-top: 1rem"
              >Log in</a
            >
          </li>
        </ul>
      </div>
    </header>

    <section class="hero" id="overview">
      <div class="hero-content">
        <h1 class="hero-title">at <span id="dynamic-word">work</span></h1>
      </div>
    </section>

    <section class="container features" id="features">
      <div class="feature-item">
        <h3>Audio technology</h3>
        <p>Adaptive EQ</p>
      </div>
      <div class="feature-item">
        <h3>Effective sensors</h3>
        <p>Accelerometer</p>
      </div>
    </section>
    <script src="scripts/hamburger.js"></script>
    <script src="scripts/typing.js"></script>
  </body>
</html>
