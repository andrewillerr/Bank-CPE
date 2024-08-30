<?php
session_start();
include 'db.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_customer'])) {
    // รับค่าจากฟอร์ม
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];

    // เชื่อมต่อฐานข้อมูล
    $conn = connectDB();

    // คำสั่ง SQL สำหรับเพิ่มข้อมูลลูกค้า
    $sql = "INSERT INTO customers (name, phone, address, dob) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $phone, $address, $dob);

    if ($stmt->execute()) {
        $success = "Customer added successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Added</title>
</head>
<body>
    <h1>Customer Added</h1>
    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <a href="add_customer.html">Add Another Customer</a><br>
    <a href="customer_list.php">View Customer List</a>
</body>
</html>
