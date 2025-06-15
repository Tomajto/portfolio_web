<?php
// filepath: c:\PROJEKTY\portfolio_web\screens\black_jack.php
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

// Blackjack functions
function createDeck()
{
    $suits = ['♠️', '♥️', '♦️', '♣️'];
    $values = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
    $deck = [];

    foreach ($suits as $suit) {
        foreach ($values as $value) {
            $deck[] = ['suit' => $suit, 'value' => $value];
        }
    }

    shuffle($deck);
    return $deck;
}

function getCardValue($card, $currentTotal = 0)
{
    if (in_array($card['value'], ['J', 'Q', 'K'])) {
        return 10;
    } elseif ($card['value'] === 'A') {
        // Ace is 11 unless it would bust, then it's 1
        return ($currentTotal + 11 > 21) ? 1 : 11;
    } else {
        return (int)$card['value'];
    }
}

function calculateHandValue($hand)
{
    $total = 0;
    $aces = 0;

    foreach ($hand as $card) {
        if ($card['value'] === 'A') {
            $aces++;
            $total += 11;
        } elseif (in_array($card['value'], ['J', 'Q', 'K'])) {
            $total += 10;
        } else {
            $total += (int)$card['value'];
        }
    }

    // Adjust for aces
    while ($total > 21 && $aces > 0) {
        $total -= 10;
        $aces--;
    }

    return $total;
}

function displayCard($card, $hidden = false)
{
    if ($hidden) {
        return '<div class="card hidden">🂠</div>';
    }

    $color = ($card['suit'] === '♥️' || $card['suit'] === '♦️') ? 'red' : 'black';
    return '<div class="card ' . $color . '">' . $card['value'] . $card['suit'] . '</div>';
}

// Game logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['start_game'])) {
        // Start new game
        $betAmount = (int)$_POST['bet_amount'];

        if ($betAmount <= 0 || $betAmount > $userCoins) {
            $message = "Invalid bet amount!";
            $messageType = "error";
        } else {
            $deck = createDeck();

            // Deal initial cards
            $playerHand = [$deck[0], $deck[2]];
            $dealerHand = [$deck[1], $deck[3]];

            $_SESSION['blackjack'] = [
                'deck' => array_slice($deck, 4),
                'player_hand' => $playerHand,
                'dealer_hand' => $dealerHand,
                'bet' => $betAmount,
                'game_over' => false,
                'player_turn' => true
            ];

            // Deduct bet from user coins
            $newCoins = $userCoins - $betAmount;
            $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE email = ?");
            $stmt->bind_param("is", $newCoins, $userEmail);
            $stmt->execute();
            $stmt->close();
            $userCoins = $newCoins;

            // Check for blackjack
            $playerValue = calculateHandValue($playerHand);
            $dealerValue = calculateHandValue($dealerHand);

            if ($playerValue == 21) {
                if ($dealerValue == 21) {
                    // Push
                    $winAmount = $betAmount;
                    $message = "🤝 Push! Both have Blackjack. Bet returned.";
                    $messageType = "success";
                } else {
                    // Player blackjack
                    $winAmount = $betAmount + ($betAmount * 1.5);
                    $message = "🎉 BLACKJACK! You won " . ($betAmount * 1.5) . " coins!";
                    $messageType = "success";
                }

                $newCoins = $userCoins + $winAmount;
                $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE email = ?");
                $stmt->bind_param("is", $newCoins, $userEmail);
                $stmt->execute();
                $stmt->close();
                $userCoins = $newCoins;

                $_SESSION['blackjack']['game_over'] = true;
            }
        }
    } elseif (isset($_POST['hit']) && isset($_SESSION['blackjack'])) {
        // Player hits
        $game = $_SESSION['blackjack'];

        if (!$game['game_over'] && $game['player_turn']) {
            $card = array_shift($game['deck']);
            $game['player_hand'][] = $card;

            $playerValue = calculateHandValue($game['player_hand']);

            if ($playerValue >= 21) {
                $game['player_turn'] = false;

                if ($playerValue > 21) {
                    $message = "💥 Bust! You lost " . $game['bet'] . " coins.";
                    $messageType = "error";
                    $game['game_over'] = true;
                }
            }

            $_SESSION['blackjack'] = $game;
        }
    } elseif (isset($_POST['stand']) && isset($_SESSION['blackjack'])) {
        // Player stands - dealer's turn
        $game = $_SESSION['blackjack'];

        if (!$game['game_over'] && $game['player_turn']) {
            $game['player_turn'] = false;

            // Dealer hits until 17 or higher
            while (calculateHandValue($game['dealer_hand']) < 17) {
                $card = array_shift($game['deck']);
                $game['dealer_hand'][] = $card;
            }

            $playerValue = calculateHandValue($game['player_hand']);
            $dealerValue = calculateHandValue($game['dealer_hand']);

            $winAmount = 0;

            if ($dealerValue > 21) {
                // Dealer bust
                $winAmount = $game['bet'] * 2;
                $message = "🎉 Dealer bust! You won " . $game['bet'] . " coins!";
                $messageType = "success";
            } elseif ($playerValue > $dealerValue) {
                // Player wins
                $winAmount = $game['bet'] * 2;
                $message = "🎉 You win! You won " . $game['bet'] . " coins!";
                $messageType = "success";
            } elseif ($playerValue < $dealerValue) {
                // Dealer wins
                $message = "😞 Dealer wins. You lost " . $game['bet'] . " coins.";
                $messageType = "error";
            } else {
                // Push
                $winAmount = $game['bet'];
                $message = "🤝 Push! Bet returned.";
                $messageType = "success";
            }

            if ($winAmount > 0) {
                $newCoins = $userCoins + $winAmount;
                $stmt = $conn->prepare("UPDATE users SET coins = ? WHERE email = ?");
                $stmt->bind_param("is", $newCoins, $userEmail);
                $stmt->execute();
                $stmt->close();
                $userCoins = $newCoins;
            }

            $game['game_over'] = true;
            $_SESSION['blackjack'] = $game;
        }
    } elseif (isset($_POST['new_game'])) {
        // Clear game session
        unset($_SESSION['blackjack']);
    }
}

$game = $_SESSION['blackjack'] ?? null;
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Blackjack | Richtr</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link rel="icon" href="/assets/icon.png" />
</head>

<body class="login-page">
    <?php include '../includes/header.php'; ?>
    <main>
        <div class="login-container blackjack-container">
            <div class="login-title">🃏 Blackjack</div>

            <!-- User Coins Display -->
            <div class="coins-display">
                <h2>Your Coins: <span id="userCoins"><?php echo $userCoins; ?></span> 🪙</h2>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>" style="margin-bottom: 1rem; padding: 0.8rem; border-radius: 10px; text-align: center; font-weight: 600; <?php echo $messageType === 'error' ? 'background-color: #fee; color: #c33;' : 'background-color: #efe; color: #3c3;'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if (!$game): ?>
                <div class="betting-section">
                    <h3>Place Your Bet</h3>
                    
                    <form method="POST" class="betting-form">
                        <div class="bet-controls">
                            <button type="button" class="bet-btn red" onclick="adjustBet(-10)">-10</button>
                            <button type="button" class="bet-btn red" onclick="adjustBet(-1)">-1</button>
                            <input type="number" name="bet_amount" id="betAmount" value="1" min="1" max="<?php echo $userCoins; ?>" class="bet-input">
                            <button type="button" class="bet-btn green" onclick="adjustBet(1)">+1</button>
                            <button type="button" class="bet-btn green" onclick="adjustBet(10)">+10</button>
                        </div>
                        <button type="submit" name="start_game" class="start-btn" <?php echo $userCoins <= 0 ? 'disabled' : ''; ?>>
                            🎮 Deal Cards
                        </button>
                    </form>
                </div>

                <!-- Game Rules -->
                <div class="game-rules">
                    <h3>Blackjack Rules:</h3>
                    <ul>
                        <li>🎯 Get 21 or as close as possible without going over</li>
                        <li>🃏 Face cards (J, Q, K) are worth 10 points</li>
                        <li>🅰️ Aces are worth 1 or 11 (whichever is better)</li>
                        <li>🎊 Blackjack (21 with 2 cards) pays 3:2</li>
                        <li>🏆 Beat the dealer without busting to win!</li>
                    </ul>
                </div>
            <?php else: ?>
                <!-- Game Phase -->
                <div class="game-section">
                    <div class="game-info">
                        <p><strong>Bet:</strong> <?php echo $game['bet']; ?> coins</p>
                    </div>

                    <!-- Dealer's Hand -->
                    <div class="hand-section">
                        <h3>Dealer's Hand</h3>
                        <div class="cards-container">
                            <?php
                            $dealerValue = calculateHandValue($game['dealer_hand']);
                            foreach ($game['dealer_hand'] as $index => $card) {
                                if ($index === 1 && $game['player_turn'] && !$game['game_over']) {
                                    echo displayCard($card, true);
                                } else {
                                    echo displayCard($card);
                                }
                            }
                            ?>
                        </div>
                        <div class="hand-value">
                            <?php
                            if ($game['player_turn'] && !$game['game_over']) {
                                echo "Value: ?";
                            } else {
                                echo "Value: " . $dealerValue;
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Player's Hand -->
                    <div class="hand-section">
                        <h3>Your Hand</h3>
                        <div class="cards-container">
                            <?php
                            foreach ($game['player_hand'] as $card) {
                                echo displayCard($card);
                            }
                            ?>
                        </div>
                        <div class="hand-value">
                            Value: <?php echo calculateHandValue($game['player_hand']); ?>
                        </div>
                    </div>

                    <!-- Game Actions -->
                    <?php if (!$game['game_over']): ?>
                        <?php if ($game['player_turn']): ?>
                            <div class="game-actions">
                                <form method="POST" style="display: inline;">
                                    <button type="submit" name="hit" class="action-btn hit-btn">🎯 Hit</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <button type="submit" name="stand" class="action-btn stand-btn">✋ Stand</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="game-actions">
                                <p><strong>Dealer is playing...</strong></p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="game-actions">
                            <form method="POST">
                                <button type="submit" name="new_game" class="action-btn new-game-btn">🎮 New Game</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="login-links">
                <a href="../index.php">Home</a> | <a href="leaderboard.php">Leaderboard</a>
            </div>
        </div>
    </main>

    <script>
        function adjustBet(amount) {
            const betInput = document.getElementById('betAmount');
            const userCoins = <?php echo $userCoins; ?>;
            let currentBet = parseInt(betInput.value) || 1;
            let newBet = currentBet + amount;

            if (newBet < 1) newBet = 1;
            if (newBet > userCoins) newBet = userCoins;

            betInput.value = newBet;
        }
    </script>
    <script src="/scripts/hamburger.js"></script>
</body>

</html>