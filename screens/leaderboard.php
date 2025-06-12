<?php
// filepath: c:\PROJEKTY\portfolio_web\screens\leaderboard.php
session_start();


include_once __DIR__ . '/../database/db_connection.php';

// Fetch top 10 users who have >0 coins
$sql = "
  SELECT username, profile_pic, coins 
    FROM users 
   WHERE coins > 0 
ORDER BY coins DESC 
   LIMIT 10
";
$result = $conn->query($sql);

$leaders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $leaders[] = $row;
    }
} else {
    // Query error
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Leaderboard | Richtr</title>
  <link rel="stylesheet" href="/styles/style.css" />
</head>
<body>
  <?php include_once __DIR__ . '/../includes/header.php'; ?>
  <main class="dashboard-page" style="background: #f5f5fa;">
    <div class="dashboard-container" style="max-width:500px;">
      <div class="login-title" style="margin-bottom:1rem;">
        Top 10 Leaderboard
      </div>

      <?php if (empty($leaders)): ?>
        <div class="message error" style="text-align:center;">
          Zatím nemá nikdo žádné coiny.
        </div>
      <?php else: ?>
        <table style="width:100%; border-collapse:collapse; background:#fff;">
          <thead>
            <tr style="background:#f5f5fa;">
              <th style="padding:0.5rem; text-align:left;">#</th>
              <th style="padding:0.5rem; text-align:left;">User</th>
              <th style="padding:0.5rem; text-align:left;">Coins</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($leaders as $i => $u): 
              $rank = $i + 1;
              $color = $rank === 1 ? '#6b21a8' : '#333';
            ?>
            <tr style="border-bottom:1px solid #eee;">
              <td style="padding:0.5rem; font-weight:600; color:<?php echo $color; ?>">
                <?php echo $rank; ?>
              </td>
              <td style="padding:0.5rem; display:flex; align-items:center; gap:0.7rem;">
                <img
                  src="<?php echo $u['profile_pic']
                           ? '/uploads/profile_pics/' . htmlspecialchars($u['profile_pic'])
                           : '/assets/default-avatar.png'; ?>"
                  alt="Avatar"
                  style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1.5px solid #6b21a8;"
                />
                <span style="font-weight:500;"><?php echo htmlspecialchars($u['username']); ?></span>
              </td>
              <td style="padding:0.5rem; font-weight:600;"><?php echo (int)$u['coins']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

      <div class="login-links" style="margin-top:2rem;">
        <a href="../index.php">Home</a>
      </div>
    </div>
  </main>
  <script src="/scripts/hamburger.js"></script>
</body>
</html>