<?php
// filepath: c:\PROJEKTY\portfolio_web\screens\cards.php
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

// Game logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['start_game'])) {
        // Start new game
        $betAmount = (int)$_POST['bet_amount'];
        
        if ($betAmount <= 0 || $betAmount > $userCoins) {
            $message = "Invalid bet amount!";
            $messageType = "error";
        } else {
            $_SESSION['card_game'] = [
                'bet' => $betAmount,
                'stage' => 1,
                'cards' => [],
                'multiplier' => 2,
                'active' => true
            ];
            
            // Deduct bet from user coins
            $newCoins = $userCoins - $betAmount;
            $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE email = ?");
            $stmt->bind_param("is", $newCoins, $userEmail);
            $stmt->execute();
            $stmt->close();
            $userCoins = $newCoins;
        }
    } elseif (isset($_POST['make_guess'])) {
        // Process guess
        $game = $_SESSION['card_game'];
        $guess = $_POST['guess'];
        
        // Generate new card
        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $values = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]; // 11=J, 12=Q, 13=K, 14=A
        $suit = $suits[array_rand($suits)];
        $value = $values[array_rand($values)];
        
        $card = ['suit' => $suit, 'value' => $value];
        $game['cards'][] = $card;
        
        $correct = false;
        
        // Check guess based on stage
        switch ($game['stage']) {
            case 1: // Red or Black
                $isRed = ($suit == 'hearts' || $suit == 'diamonds');
                $correct = ($guess == 'red' && $isRed) || ($guess == 'black' && !$isRed);
                break;
                
            case 2: // Higher or Lower
                $prevCard = $game['cards'][0];
                $correct = ($guess == 'higher' && $value > $prevCard['value']) || 
                          ($guess == 'lower' && $value < $prevCard['value']);
                break;
                
            case 3: // Inside or Outside
                $card1 = $game['cards'][0]['value'];
                $card2 = $game['cards'][1]['value'];
                $min = min($card1, $card2);
                $max = max($card1, $card2);
                $inside = ($value > $min && $value < $max);
                $correct = ($guess == 'inside' && $inside) || ($guess == 'outside' && !$inside);
                break;
                
            case 4: // Suit
                $correct = ($guess == $suit);
                break;
        }
        
        if ($correct) {
            $game['stage']++;
            switch ($game['stage']) {
                case 2: $game['multiplier'] = 3; break;
                case 3: $game['multiplier'] = 4; break;
                case 4: $game['multiplier'] = 20; break;
                case 5: // Game won!
                    $winAmount = $game['bet'] * 20;
                    $newCoins = $userCoins + $winAmount;
                    $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE email = ?");
                    $stmt->bind_param("is", $newCoins, $userEmail);
                    $stmt->execute();
                    $stmt->close();
                    $userCoins = $newCoins;
                    $message = "🎉 You won all 4 rounds! You earned " . $winAmount . " coins!";
                    $messageType = "success";
                    $game['active'] = false;
                    break;
            }
        } else {
            $message = "😞 Wrong guess! You lost your bet.";
            $messageType = "error";
            $game['active'] = false;
        }
        
        $_SESSION['card_game'] = $game;
    } elseif (isset($_POST['collect_winnings'])) {
        // Collect current winnings
        $game = $_SESSION['card_game'];
        $winAmount = $game['bet'] * $game['multiplier'];
        $newCoins = $userCoins + $winAmount;
        
        $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE email = ?");
        $stmt->bind_param("is", $newCoins, $userEmail);
        $stmt->execute();
        $stmt->close();
        $userCoins = $newCoins;
        
        $message = "💰 You collected " . $winAmount . " coins!";
        $messageType = "success";
        unset($_SESSION['card_game']);
    }
}

$game = $_SESSION['card_game'] ?? null;

function getCardName($card) {
    $names = [11 => 'J', 12 => 'Q', 13 => 'K', 14 => 'A'];
    $value = $names[$card['value']] ?? $card['value'];
    $suitSymbols = ['hearts' => '♥️', 'diamonds' => '♦️', 'clubs' => '♣️', 'spades' => '♠️'];
    return $value . $suitSymbols[$card['suit']];
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Card Game | Richtr</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link rel="icon" href="/assets/icon.png"/>
</head>
<body class="login-page">
    <?php include '../includes/header.php'; ?>
    <main>
        <div class="login-container cards-container">
            <div class="login-title">🃏 Card Game</div>
            
            <!-- User Coins Display -->
            <div class="coins-display">
                <h2>Your Coins: <span id="userCoins"><?php echo $userCoins; ?></span> 🪙</h2>
            </div>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>" style="margin-bottom: 1rem; padding: 0.8rem; border-radius: 10px; text-align: center; font-weight: 600; <?php echo $messageType === 'error' ? 'background-color: #fee; color: #c33;' : 'background-color: #efe; color: #3c3;'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$game || !$game['active']): ?>
                <!-- Betting Phase -->
                <div class="betting-section">
                    <h3>Place Your Bet</h3>
                    <form method="POST" class="betting-form">
                        <div class="slider-container">
                            <input type="range" name="bet_amount" id="betSlider" min="1" max="<?php echo $userCoins; ?>" value="1" class="bet-slider">
                            <div class="bet-display">
                                <span id="betDisplay">1</span> coins
                            </div>
                        </div>
                        <button type="submit" name="start_game" class="start-btn" <?php echo $userCoins <= 0 ? 'disabled' : ''; ?>>
                            🎮 Start Game
                        </button>
                    </form>
                </div>
                
                <!-- Game Rules -->
                <div class="game-rules">
                    <h3>Game Rules:</h3>
                    <ul>
                        <li>🎯 <strong>Round 1:</strong> Red or Black (2x multiplier)</li>
                        <li>🎲 <strong>Round 2:</strong> Higher or Lower (3x multiplier)</li>
                        <li>📊 <strong>Round 3:</strong> Inside or Outside range (4x multiplier)</li>
                        <li>♠️ <strong>Round 4:</strong> Guess the suit (20x multiplier)</li>
                        <li>💰 You can collect your winnings after any successful round!</li>
                    </ul>
                </div>
            <?php else: ?>
                <!-- Game Phase -->
                <div class="game-section">
                    <div class="game-info">
                        <p><strong>Stage:</strong> <?php echo $game['stage']; ?>/4</p>
                        <p><strong>Current Bet:</strong> <?php echo $game['bet']; ?> coins</p>
                        <p><strong>Current Multiplier:</strong> <?php echo $game['multiplier']; ?>x</p>
                        <p><strong>Potential Win:</strong> <?php echo $game['bet'] * $game['multiplier']; ?> coins</p>
                    </div>
                    
                    <!-- Display Previous Cards -->
                    <?php if (!empty($game['cards'])): ?>
                        <div class="cards-display">
                            <h4>Cards Drawn:</h4>
                            <?php foreach ($game['cards'] as $card): ?>
                                <div class="card <?php echo ($card['suit'] == 'hearts' || $card['suit'] == 'diamonds') ? 'red' : 'black'; ?>">
                                    <?php echo getCardName($card); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Game Stage -->
                    <form method="POST" class="guess-form">
                        <?php if ($game['stage'] == 1): ?>
                            <h3>Round 1: Will the card be Red or Black?</h3>
                            <div class="guess-buttons">
                                <button type="submit" name="guess" value="red" class="guess-btn red">❤️ Red</button>
                                <button type="submit" name="guess" value="black" class="guess-btn black">♠️ Black</button>
                            </div>
                        <?php elseif ($game['stage'] == 2): ?>
                            <h3>Round 2: Will the next card be Higher or Lower?</h3>
                            <p>Previous card: <strong><?php echo getCardName($game['cards'][0]); ?></strong></p>
                            <div class="guess-buttons">
                                <button type="submit" name="guess" value="higher" class="guess-btn">📈 Higher</button>
                                <button type="submit" name="guess" value="lower" class="guess-btn">📉 Lower</button>
                            </div>
                        <?php elseif ($game['stage'] == 3): ?>
                            <h3>Round 3: Will the card be Inside or Outside the range?</h3>
                            <p>Range: <strong><?php echo getCardName($game['cards'][0]); ?></strong> to <strong><?php echo getCardName($game['cards'][1]); ?></strong></p>
                            <div class="guess-buttons">
                                <button type="submit" name="guess" value="inside" class="guess-btn">📍 Inside</button>
                                <button type="submit" name="guess" value="outside" class="guess-btn">🔄 Outside</button>
                            </div>
                        <?php elseif ($game['stage'] == 4): ?>
                            <h3>Round 4: What suit will the card be?</h3>
                            <div class="guess-buttons">
                                <button type="submit" name="guess" value="hearts" class="guess-btn red">♥️ Hearts</button>
                                <button type="submit" name="guess" value="diamonds" class="guess-btn red">♦️ Diamonds</button>
                                <button type="submit" name="guess" value="clubs" class="guess-btn black">♣️ Clubs</button>
                                <button type="submit" name="guess" value="spades" class="guess-btn black">♠️ Spades</button>
                            </div>
                        <?php endif; ?>
                        
                        <input type="hidden" name="make_guess" value="1">
                    </form>
                    
                    <?php if ($game['stage'] > 1): ?>
                        <!-- Collect Winnings Option -->
                        <form method="POST" class="collect-form">
                            <button type="submit" name="collect_winnings" class="collect-btn">
                                💰 Collect <?php echo $game['bet'] * $game['multiplier']; ?> coins
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="login-links">
                <a href="../index.php">Home</a> | <a href="leaderboard.php">Leaderboard</a>
            </div>
        </div>
    </main>
    
    <script>
        const slider = document.getElementById('betSlider');
        const display = document.getElementById('betDisplay');
        
        if (slider && display) {
            slider.addEventListener('input', function() {
                display.textContent = this.value;
            });
        }
    </script>
    <script src="/scripts/hamburger.js"></script>
</body>
</html>