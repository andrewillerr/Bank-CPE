<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaction</title>
    <script>
        function updateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            document.getElementById('current-time').innerText = now.toLocaleDateString('en-US', options);
        }

        // Update time every second
        setInterval(updateTime, 1000);

        // Initialize time on page load
        window.onload = updateTime;
    </script>
</head>
<body>
    <?php
    session_start();
    include 'db.php'; // Include the file with the connectDB() function

    // Function to check user role
    function isUserManager($user_id) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $conn->close();

        return $user && $user['role'] === 'manager';
    }

    $canWithdraw = isUserManager($_SESSION['user_id']); // Check user permission
    ?>

    <h1>Add New Transaction</h1>
    <p>Current Date and Time: <span id="current-time"></span></p>

    <form action="add_transaction.php" method="POST">
        <label>Account ID:</label>
        <input type="number" name="account_id" required><br>
        <label>Amount:</label>
        <input type="number" step="0.01" name="amount" required><br>
        <label>Transaction Type:</label>
        <select name="transaction_type" required>
            <option value="deposit">Deposit</option>
            <?php if ($canWithdraw): ?>
                <option value="withdraw">Withdraw</option>
            <?php endif; ?>
        </select><br>
        <label>Performed By (User ID):</label>
        <input type="number" name="performed_by" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>" readonly><br>
        <input type="submit" name="add_transaction" value="Add Transaction">
    </form>

    <p><a href="transaction_list.php">Back</a></p>
</body>
</html>
