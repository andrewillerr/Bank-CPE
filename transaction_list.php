<?php
session_start();
include 'db.php'; // รวมไฟล์ที่มีฟังก์ชัน connectDB()

$conn = connectDB(); // เชื่อมต่อฐานข้อมูล

$sql = "SELECT t.transaction_id, t.account_id, t.amount, t.transaction_type, t.performed_by, u.username, t.transaction_date
        FROM transactions t
        JOIN users u ON t.performed_by = u.user_id";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaction List</title>
    <script src="current_time.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<center><p>ขณะนี้เวลา: <span id="current-time"></span></p></center>

    <h1>Transaction List</h1>
    <table>
        <tr>
            <th>Transaction ID</th>
            <th>Account ID</th>
            <th>Amount</th>
            <th>Transaction Type</th>
            <th>Performed By</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
            <td><?php echo htmlspecialchars($row['account_id']); ?></td>
            <td><?php echo htmlspecialchars(number_format($row['amount'], 2)); ?></td>
            <td><?php echo htmlspecialchars($row['transaction_type']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($row['transaction_date']))); ?></td>
            <td>
                <a href="edit_transaction.php?transaction_id=<?php echo urlencode($row['transaction_id']); ?>">Edit</a>
                <a href="delete_transaction.php?transaction_id=<?php echo urlencode($row['transaction_id']); ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="add_transaction.php">Add Transaction</a>
    <a href="daily_summary.php">View Daily Summary</a>
    <p1><a href="index.php">Back to Home</a></p1>
</body>
</html>
<?php $conn->close(); ?>
