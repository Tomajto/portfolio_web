<?php
// filepath: c:\PROJEKTY\portfolio_web\screens\slots.php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include '../database/db_connection.php';
$userEmail = $_SESSION['email'];

$message = "";
$messageType = "";

// Get user's current coins
$stmt = $conn->prepare("SELECT coins FROM users WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$userCoins = (int)$user['coins'];
$stmt->close();

// Handle slot game spin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bet_amount'])) {
    $betAmount = (int)$_POST['bet_amount'];
    
    // Store bet amount in session
    $_SESSION['last_bet_amount'] = $betAmount;
    
    if ($betAmount <= 0) {
        $message = "Bet amount must be greater than 0!";
        $messageType = "error";
    } elseif ($betAmount > $userCoins) {
        $message = "Not enough coins to place this bet!";
        $messageType = "error";
    } else {
        // Generate random slot results
        $fruits = ['🍎', '🍊', '🍋', '🍌', '🍇'];
        $slot1 = $fruits[array_rand($fruits)];
        $slot2 = $fruits[array_rand($fruits)];
        $slot3 = $fruits[array_rand($fruits)];
        
        $winAmount = 0;
        
        // Check for wins
        if ($slot1 === $slot2 && $slot2 === $slot3) {
            // Three of a kind - 10x bet
            $winAmount = $betAmount * 10;
            $message = "🎉 JACKPOT! Three {$slot1}! You won {$winAmount} coins!";
            $messageType = "success";
        } elseif ($slot1 === $slot2 || $slot2 === $slot3 || $slot1 === $slot3) {
            // Two of a kind - 2x bet
            $winAmount = $betAmount * 2;
            $message = "🎊 Nice! Two matching! You won {$winAmount} coins!";
            $messageType = "success";
        } else {
            // No match - lose bet
            $winAmount = 0;
            $message = "😔 No match. You lost {$betAmount} coins. Try again!";
            $messageType = "error";
        }
        
        // Update user coins
        $newCoins = $userCoins - $betAmount + $winAmount;
        $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE email = ?");
        $stmt->bind_param("is", $newCoins, $userEmail);
        $stmt->execute();
        $stmt->close();
        
        // Update userCoins for display
        $userCoins = $newCoins;
        
        // Store results for display
        $_SESSION['slot_results'] = [$slot1, $slot2, $slot3];
        $_SESSION['game_message'] = $message;
        $_SESSION['game_message_type'] = $messageType;
        
        // Redirect to prevent form resubmission
        header("Location: slots.php");
        exit();
    }
}

// Get stored results if any
$slotResults = $_SESSION['slot_results'] ?? ['❓', '❓', '❓'];
if (isset($_SESSION['game_message'])) {
    $message = $_SESSION['game_message'];
    $messageType = $_SESSION['game_message_type'];
    unset($_SESSION['game_message']);
    unset($_SESSION['game_message_type']);
}

// Get last bet amount from session, default to 1
$lastBetAmount = $_SESSION['last_bet_amount'] ?? 1;

// Ensure bet amount doesn't exceed current coins
if ($lastBetAmount > $userCoins) {
    $lastBetAmount = min($userCoins, 1);
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Slots Game | Richtr</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link rel="icon" href="/assets/icon.png"/>
</head>
<body class="login-page">
    <?php include '../includes/header.php'; ?>
    <main>
        <div class="login-container slots-container">
            <div class="login-title">🎰 Slots Game</div>
            
            <!-- User Coins Display -->
            <div class="coins-display">
                <h2>Your Coins: <span id="userCoins"><?php echo $userCoins; ?></span> 🪙</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>" style="margin-bottom: 1rem; padding: 0.8rem; border-radius: 10px; text-align: center; font-weight: 600; <?php echo $messageType === 'error' ? 'background-color: #fee; color: #c33;' : 'background-color: #efe; color: #3c3;'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Slot Machine -->
            <div class="slot-machine">
                <div class="slot-reels">
                    <div class="slot-reel">
                        <div class="slot-symbol"><?php echo $slotResults[0]; ?></div>
                    </div>
                    <div class="slot-reel">
                        <div class="slot-symbol"><?php echo $slotResults[1]; ?></div>
                    </div>
                    <div class="slot-reel">
                        <div class="slot-symbol"><?php echo $slotResults[2]; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Betting Controls -->
            <form method="POST" class="betting-form">
                <div class="bet-controls">
                    <button type="button" class="bet-btn" onclick="adjustBet(-10)">-10</button>
                    <button type="button" class="bet-btn" onclick="adjustBet(-1)">-1</button>
                    <input type="number" name="bet_amount" id="betAmount" value="<?php echo $lastBetAmount; ?>" min="1" max="<?php echo $userCoins; ?>" class="bet-input">
                    <button type="button" class="bet-btn" onclick="adjustBet(1)">+1</button>
                    <button type="button" class="bet-btn" onclick="adjustBet(10)">+10</button>
                </div>
                <button type="submit" class="spin-btn" <?php echo $userCoins <= 0 ? 'disabled' : ''; ?>>
                    🎰 SPIN 🎰
                </button>
            </form>
            
            <!-- Game Rules -->
            <div class="game-rules">
                <h3>Game Rules:</h3>
                <ul>
                    <li>🎯 Three matching symbols = 10x your bet</li>
                    <li>🎲 Two matching symbols = 2x your bet</li>
                    <li>💔 No match = Lose your bet</li>
                </ul>
            </div>
            
            <div class="login-links">
                <a href="../index.php">Home</a> | <a href="leaderboard.php">Leaderboard</a>
            </div>
        </div>
    </main>
    
    <script>
        function adjustBet(amount) {
            const betInput = document.getElementById('betAmount');
            const userCoins = <?php echo $userCoins; ?>;
            let currentBet = parseInt(betInput.value) || <?php echo $lastBetAmount; ?>;
            let newBet = currentBet + amount;
            
            // Ensure bet is within valid range
            if (newBet < 1) newBet = 1;
            if (newBet > userCoins) newBet = userCoins;
            
            betInput.value = newBet;
            
            // Store in session via AJAX (optional for immediate persistence)
            updateBetInSession(newBet);
        }
        
        // Optional: Update bet amount in session immediately via AJAX
        function updateBetInSession(betAmount) {
            fetch('update_bet.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'bet_amount=' + betAmount
            });
        }
        
        // Update session when user manually changes input
        document.getElementById('betAmount').addEventListener('input', function() {
            const betAmount = parseInt(this.value) || 1;
            updateBetInSession(betAmount);
        });
        
        // Prevent form submission if user has no coins
        document.querySelector('.betting-form').addEventListener('submit', function(e) {
            const userCoins = <?php echo $userCoins; ?>;
            const betAmount = parseInt(document.getElementById('betAmount').value);
            
            if (userCoins <= 0) {
                e.preventDefault();
                alert('You need coins to play! Visit the dashboard to collect coins.');
            } else if (betAmount > userCoins) {
                e.preventDefault();
                alert('You cannot bet more coins than you have!');
            }
        });
    </script>
    <script src="/scripts/hamburger.js"></script>
</body>
</html>