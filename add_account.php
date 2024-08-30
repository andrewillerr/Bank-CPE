<?php
session_start();
include 'db.php'; // รวมไฟล์ที่มีฟังก์ชัน connectDB()

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_account'])) {
    $customer_id = $_POST['customer_id'];
    $account_number = $_POST['account_number'];
    $balance = $_POST['balance'];

    $conn = connectDB(); // เชื่อมต่อฐานข้อมูล

    // เตรียมคำสั่ง SQL เพื่อเพิ่มบัญชี
    $sql = "INSERT INTO accounts (customer_id, account_number, balance) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $customer_id, $account_number, $balance);

    if ($stmt->execute()) {
        echo "Account added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Added</title>
</head>
<body>
    <a href="account_list.php">Back to Account List</a>
</body>
</html>
