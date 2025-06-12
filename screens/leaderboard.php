<?php
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
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main style="padding-top: 100px;">
        <div class="login-container" style="max-width: 500px;">
            <div class="login-title">Top 10 Leaderboard</div>
            <table style="width:100%; border-collapse:collapse; margin-top:1.5rem;">
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
                        <td style="padding:0.5rem;"><?php echo $rank; ?></td>
                        <td style="padding:0.5rem; display:flex; align-items:center; gap:0.7rem;">
                            <img src="<?php echo $row['profile_pic'] ? '/uploads/profile_pics/' . htmlspecialchars($row['profile_pic']) : '/assets/default-avatar.png'; ?>"
                                 alt="Profile"
                                 style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1.5px solid #6b21a8;">
                            <?php echo htmlspecialchars($row['username']); ?>
                        </td>
                        <td style="padding:0.5rem;"><?php echo (int)$row['coins']; ?></td>
                    </tr>
                    <?php
                    $rank++;
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>