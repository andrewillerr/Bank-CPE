<?php
session_start();
include 'db.php'; // รวมไฟล์ที่มีฟังก์ชัน connectDB()

// ตรวจสอบว่ามีการส่ง transaction_id จาก URL หรือไม่
$transaction_id = isset($_GET['transaction_id']) ? intval($_GET['transaction_id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_transaction'])) {
    $transaction_id = intval($_POST['transaction_id']);
    $new_amount = $_POST['amount'];
    $new_transaction_type = $_POST['transaction_type'];

    // ตรวจสอบว่า transaction_id ถูกตั้งค่าเป็นค่าที่ไม่เป็น 0
    if ($transaction_id > 0) {
        $conn = connectDB(); // เชื่อมต่อฐานข้อมูล

        // เริ่มการทำธุรกรรม
        $conn->begin_transaction();

        try {
            // ดึงข้อมูลการทำธุรกรรมที่ต้องการแก้ไข
            $sql = "SELECT * FROM transactions WHERE transaction_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $transaction_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $transaction = $result->fetch_assoc();
            $stmt->close();

            if ($transaction) {
                // อัปเดตยอดเงินในบัญชีก่อนและหลังการแก้ไข
                $old_amount = $transaction['amount'];
                $account_id = $transaction['account_id'];
                $old_transaction_type = $transaction['transaction_type'];

                // อัปเดตยอดเงินในบัญชีก่อนการแก้ไข
                if ($old_transaction_type == 'deposit') {
                    $sql = "UPDATE accounts SET balance = balance - ? WHERE account_id = ?";
                } elseif ($old_transaction_type == 'withdraw') {
                    $sql = "UPDATE accounts SET balance = balance + ? WHERE account_id = ?";
                }
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("di", $old_amount, $account_id);
                $stmt->execute();
                $stmt->close();

                // อัปเดตข้อมูลการทำธุรกรรม
                $sql = "UPDATE transactions SET amount = ?, transaction_type = ? WHERE transaction_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("dsi", $new_amount, $new_transaction_type, $transaction_id);
                $stmt->execute();
                $stmt->close();

                // อัปเดตยอดเงินในบัญชีหลังการแก้ไข
                if ($new_transaction_type == 'deposit') {
                    $sql = "UPDATE accounts SET balance = balance + ? WHERE account_id = ?";
                } elseif ($new_transaction_type == 'withdraw') {
                    $sql = "UPDATE accounts SET balance = balance - ? WHERE account_id = ?";
                }
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("di", $new_amount, $account_id);
                $stmt->execute();
                $stmt->close();

                $conn->commit();
                echo "Transaction updated successfully!";
            } else {
                echo "Transaction not found! (Transaction ID: $transaction_id)";
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }

        $conn->close();
    } else {
        echo "Invalid transaction ID!";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Transaction</title>
</head>
<body>
    <h1>Edit Transaction</h1>
    <form action="edit_transaction.php" method="POST">
        <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id); ?>">
        <label>Amount:</label>
        <input type="number" step="0.01" name="amount" required><br>
        <label>Transaction Type:</label>
        <select name="transaction_type" required>
            <option value="deposit">Deposit</option>
            <option value="withdraw">Withdraw</option>
        </select><br>
        <input type="submit" name="edit_transaction" value="Update Transaction">
    </form>
    <a href="transaction_list.php">Back to Transaction List</a>
</body>
</html>
