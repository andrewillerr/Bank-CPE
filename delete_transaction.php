<?php
// เริ่มเซสชัน
session_start();
include 'db.php'; // รวมไฟล์ที่มีฟังก์ชัน connectDB()

// ตรวจสอบว่ามีการส่งแบบฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_transaction'])) {
    $transaction_id = $_POST['transaction_id'];

    $conn = connectDB(); // เชื่อมต่อฐานข้อมูล

    // เริ่มการทำธุรกรรม
    $conn->begin_transaction();

    try {
        // ดึงข้อมูลการทำธุรกรรมที่ต้องการลบ
        $sql = "SELECT * FROM transactions WHERE transaction_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement for transactions: " . $conn->error);
        }
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaction = $result->fetch_assoc();
        $stmt->close();

        if ($transaction) {
            // อัปเดตยอดเงินในบัญชี
            $amount = $transaction['amount'];
            $account_id = $transaction['account_id'];
            $transaction_type = $transaction['transaction_type'];

            if ($transaction_type == 'deposit') {
                $sql = "UPDATE accounts SET balance = balance - ? WHERE account_id = ?";
            } elseif ($transaction_type == 'withdraw') {
                $sql = "UPDATE accounts SET balance = balance + ? WHERE account_id = ?";
            }
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparing statement for accounts update: " . $conn->error);
            }
            $stmt->bind_param("di", $amount, $account_id);
            $stmt->execute();
            $stmt->close();

            // ลบข้อมูลการทำธุรกรรม
            $sql = "DELETE FROM transactions WHERE transaction_id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparing statement for transaction deletion: " . $conn->error);
            }
            $stmt->bind_param("i", $transaction_id);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            echo "Transaction deleted successfully!";
        } else {
            echo "Transaction not found!";
        }
    } catch (Exception $e) {
        // ยกเลิกธุรกรรมหากเกิดข้อผิดพลาด
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    if ($conn) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Transaction</title>
</head>
<body>
    <h1>Delete Transaction</h1>
    <form action="delete_transaction.php" method="POST">
        <!-- ตรวจสอบค่าของ $_GET['transaction_id'] และทำให้มั่นใจว่ามีการกำหนดค่า -->
        <?php
        // ตรวจสอบว่ามีค่าใน $_GET['transaction_id']
        $transaction_id = isset($_GET['transaction_id']) ? intval($_GET['transaction_id']) : 0;
        ?>
        <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id); ?>">
        <input type="submit" name="delete_transaction" value="Delete Transaction">
    </form>
    <a href="transaction_list.php">Back to Transaction List</a>
</body>
</html>
