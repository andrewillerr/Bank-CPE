<?php
include 'db.php'; // รวมการเชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];

    // เตรียม SQL Statement
    $sql = "INSERT INTO customers (name, phone, address, dob) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $phone, $address, $dob);

    // ตรวจสอบการทำงาน
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();
}
?>

<!-- ฟอร์ม HTML -->
<!DOCTYPE html>
<html>
<head>
    <title>Insert Customer</title>
</head>
<body>
    <h1>Insert Customer</h1>
    <form action="insert_customer.php" method="POST">
        <label>Name:</label>
        <input type="text" name="name" required><br>
        <label>Phone:</label>
        <input type="text" name="phone" required><br>
        <label>Address:</label>
        <textarea name="address" required></textarea><br>
        <label>Date of Birth:</label>
        <input type="date" name="dob" required><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
