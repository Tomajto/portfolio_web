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
    <?php include 'includes/header.php'; ?>

    <section class="hero" id="overview">
      <div class="hero-content">
        <h1 class="hero-title">at <span id="dynamic-word"></span></h1>
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
    <?php include './includes/footer.php'; ?>
    <script src="scripts/hamburger.js"></script>
    <script src="scripts/typing.js"></script>
  </body>
</html>
