<?php
session_start();
include 'db.php'; // ตรวจสอบให้แน่ใจว่าไฟล์นี้อยู่ในตำแหน่งที่ถูกต้อง

// ฟังก์ชันการดึงข้อมูลบัญชี
function getAccounts() {
    $conn = connectDB(); // เรียกใช้ฟังก์ชัน connectDB() เพื่อสร้างการเชื่อมต่อฐานข้อมูล
    $sql = "SELECT * FROM accounts";
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

// ฟังก์ชันการปิดบัญชี
function closeAccount($account_id) {
    $conn = connectDB(); // เรียกใช้ฟังก์ชัน connectDB() เพื่อสร้างการเชื่อมต่อฐานข้อมูล

    // อัปเดตยอดเงินของบัญชีให้เป็น 0.00
    $stmt = $conn->prepare("UPDATE accounts SET balance = 0.00, closed_by = ?, closed_at = NOW() WHERE account_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $account_id);
    
    if (!$stmt->execute()) {
        die('Error updating account balance: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}

// ฟังก์ชันการอัปเดตยอดเงินคงเหลือในธนาคารหลังจากปิดบัญชี
function updateBankBalanceAfterCloseAccount($account_id) {
    $conn = connectDB();
    
    // ดึงยอดเงินของบัญชีที่จะปิด
    $stmt = $conn->prepare("SELECT balance FROM accounts WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    
    if ($account) {
        $balance_to_deduct = $account['balance'];
        
        // อัปเดตยอดเงินของบัญชีให้เป็น 0.00
        $stmt = $conn->prepare("UPDATE accounts SET balance = 0.00 WHERE account_id = ?");
        $stmt->bind_param("i", $account_id);
        if (!$stmt->execute()) {
            die('Error updating account balance: ' . $stmt->error);
        }
        
        // สมมติว่ามีตาราง `bank_balance` ที่เก็บยอดเงินรวมของธนาคาร
        $stmt = $conn->prepare("UPDATE bank_balance SET balance = balance - ?");
        $stmt->bind_param("d", $balance_to_deduct);
        if (!$stmt->execute()) {
            die('Error updating bank balance: ' . $stmt->error);
        }
    }

    $stmt->close();
    $conn->close();
}

// ตรวจสอบการขอปิดบัญชี
if (isset($_GET['action']) && $_GET['action'] === 'close' && isset($_GET['account_id'])) {
    closeAccount($_GET['account_id']);
    updateBankBalanceAfterCloseAccount($_GET['account_id']); // อัปเดตยอดเงินคงเหลือในธนาคาร
    header("Location: account_list.php"); // รีเฟรชหน้า
    exit();
}

$accounts = getAccounts();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account List</title>
    <script src="current_time.js"></script>

</head>
<body>
<center><p>ขณะนี้เวลา: <span id="current-time"></span></p></center>

    <h1>Account List</h1>
    <a href="add_account.html">Add New Account</a><br><br>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer ID</th>
                <th>Account Number</th>
                <th>Balance</th>
                <th>Created At</th>
                <th>Closed By</th>
                <th>Closed At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($accounts->num_rows > 0): ?>
                <?php while($row = $accounts->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['account_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['account_number']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['balance'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo $row['closed_by'] ? htmlspecialchars($row['closed_by']) : 'N/A'; ?></td>
                        <td><?php echo $row['closed_at'] ? htmlspecialchars($row['closed_at']) : 'N/A'; ?></td>
                        <td>
                            <a href="account_list.php?action=close&account_id=<?php echo $row['account_id']; ?>" onclick="return confirm('Are you sure you want to close this account?');">Close</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No accounts found.</td>
                </tr>
            <?php endif; ?>
            <p1><a href="index.php">Back to Home</a></p1>

        </tbody>
    </table>
</body>
</html>



