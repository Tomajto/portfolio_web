<?php
session_start();


include_once __DIR__ . '/../database/db_connection.php';

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
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link rel="icon" href="/assets/icon.png" />
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <main class="login-page">
        <div class="login-container" style="max-width: 500px;">
            <div class="login-title">Top 10 Leaderboard</div>

            <?php if (empty($leaders)): ?>
                <div class="message error">
                    Zatím nikdo nemá žádné coiny.
                </div>
            <?php else: ?>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Coins</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaders as $i => $u):
                            $rank = $i + 1;
                            $highlight = $rank === 1 ? ' style="color:#6b21a8;"' : '';
                        ?>
                            <tr>
                                <td<?php echo $highlight ?>><?php echo $rank ?></td>
                                    <td>
                                        <img
                                            src="<?php echo $u['profile_pic']
                                                        ? '/uploads/profile_pics/' . htmlspecialchars($u['profile_pic'])
                                                        : '/assets/default-avatar.png'; ?>"
                                            alt=""
                                            class="profile-pic-nav"
                                            style="width:28px;height:28px;margin-right:0.5rem;" />
                                        <?php echo htmlspecialchars($u['username']) ?>
                                    </td>
                                    <td><?php echo (int)$u['coins'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="login-links">
                <a href="../index.php">Home</a>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <script src="/scripts/hamburger.js"></script>
</body>

</html>