<?php
session_start();
include_once 'db.php'; // ใช้ include_once เพื่อหลีกเลี่ยงการประกาศซ้ำ

$conn = connectDB(); // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_customer'])) {
    $customer_id = $_POST['customer_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $modified_by = $_SESSION['user_id']; // ดึง user_id จาก session

    // อัปเดตข้อมูลลูกค้า
    $sql = "UPDATE customers SET name = ?, phone = ?, address = ?, dob = ?, modified_by = ?, modified_at = NOW() WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Error preparing the SQL statement: ' . $conn->error);
    }
    $stmt->bind_param("ssssii", $name, $phone, $address, $dob, $modified_by, $customer_id);

    if ($stmt->execute()) {
        echo "Customer updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

$customer_id = isset($_GET['customer_id']) ? htmlspecialchars($_GET['customer_id']) : ''; // ตรวจสอบว่ามีค่า customer_id ถูกส่งมาหรือไม่
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Customer</title>
</head>
<body>
    <h1>Edit Customer</h1>
    <form action="edit_customer.php" method="POST">
        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
        <label>Name:</label>
        <input type="text" name="name" required><br>
        <label>Phone:</label>
        <input type="text" name="phone" required><br>
        <label>Address:</label>
        <textarea name="address" required></textarea><br>
        <label>Date of Birth:</label>
        <input type="date" name="dob" required><br>
        <input type="submit" name="edit_customer" value="Update Customer">
    </form>
    <a href="customer_list.php">Back to Customer List</a>
</body>
</html>
