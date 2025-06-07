<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background-color: #f4f4f4;
            color: #333;
        }
        h2 {
            color: #2ecc71;
        }
        h3 {
            color: #e74c3c;
        }
        .box {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="box">
        <?php
        $host = 'localhost';
        $dbname = 'portfoliodb';
        $username = 'root';
        $password = 'yourpassword'; // ← replace this

        $conn = new mysqli($host, $username, $password, $dbname);

        if ($conn->connect_error) {
            echo "<h2>❌ Connection Failed</h2>";
            echo "<h3>" . $conn->connect_error . "</h3>";
        } else {
            echo "<h2>✅ Connection Successful</h2>";
            echo "<h3>Connected to database '$dbname' as user '$username'</h3>";
        }
        ?>
    </div>
</body>
</html>
