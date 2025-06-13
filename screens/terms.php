<!DOCTYPE html>
<html lang="cs">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Terms of Service | Richtr</title>
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
    <div
      class="login-container"
      style="max-width: 700px; align-items: flex-start">
      <div class="login-title" style="margin-bottom: 1rem">
        Terms of Service
      </div>
      <div style="color: #444; font-size: 1rem; line-height: 1.7">
        <p>
          Welcome to the Tomáš Richtr website. By using this service, you
          agree to the following terms:
        </p>
        <ol style="margin-left: 1.2em; margin-bottom: 1.2em">
          <li>
            <strong>Personal Data:</strong> Your personal data will be
            processed in accordance with the
            <a href="/screens/privacy.php">Privacy Policy</a>.
          </li>
          <li>
            <strong>Use of Service:</strong> You may not use the service for
            any illegal purposes or in any way that could harm other users or
            the operator.
          </li>
          <li>
            <strong>Content:</strong> All content on this website is protected
            by copyright. Copying or distributing content without permission
            is not allowed.
          </li>
          <li>
            <strong>Changes to Terms:</strong> The operator reserves the right
            to change these terms at any time. You will be informed of any
            changes on this page.
          </li>
          <li>
            <strong>Contact:</strong> If you have any questions, please
            contact us via the contact form.
          </li>
        </ol>
        <p>
          By using this service, you confirm that you have read and understand
          these terms.
        </p>
      </div>
      <div class="login-links" style="margin-top: 2rem">
        <a href="/screens/signup.php">Back to registration</a> |
        <a href="../index.php">Home</a>
      </div>
    </div>
  </main>
  <?php include 'includes/footer.php'; ?>
  <script src="/scripts/hamburger.js"></script>
</body>

</html>