<?php
// filepath: c:\PROJEKTY\portfolio_web\screens\leaderboard.php
session_start();
include '../database/db_connection.php';

// Get top 10 users by coins
$sql = "SELECT username, profile_pic, coins FROM users ORDER BY coins DESC LIMIT 10";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Leaderboard | Richtr</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link rel="icon" href="/assets/icon.png"/>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f5f5fa;">
        <div class="login-container" style="max-width: 500px; width: 100%; margin: 120px auto 0 auto; align-items: center;">
            <div class="login-title" style="margin-bottom: 1.5rem;">Top 10 Leaderboard</div>
            <table style="width:100%; border-collapse:collapse; margin-top:1.5rem; background: #fff;">
                <thead>
                    <tr style="background:#f5f5fa;">
                        <th style="text-align:left; padding:0.5rem;">#</th>
                        <th style="text-align:left; padding:0.5rem;">User</th>
                        <th style="text-align:left; padding:0.5rem;">Coins</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:0.5rem; font-weight:600; color:<?php echo $rank === 1 ? '#6b21a8' : '#333'; ?>">
                            <?php echo $rank; ?>
                        </td>
                        <td style="padding:0.5rem; display:flex; align-items:center; gap:0.7rem;">
                            <img src="<?php echo $row['profile_pic'] ? '/uploads/profile_pics/' . htmlspecialchars($row['profile_pic']) : '/assets/default-avatar.png'; ?>"
                                 alt="Profile"
                                 style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1.5px solid #6b21a8;">
                            <span style="font-weight:500;"><?php echo htmlspecialchars($row['username']); ?></span>
                        </td>
                        <td style="padding:0.5rem; font-weight:600;"><?php echo (int)$row['coins']; ?></td>
                    </tr>
                    <?php
                    $rank++;
                    endwhile;
                    ?>
                </tbody>
            </table>
            <div class="login-links" style="margin-top: 2rem;">
                <a href="../index.php">Home</a>
            </div>
        </div>
    </main>
    <script src="/scripts/hamburger.js"></script>
</body>
</html>