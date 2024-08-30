<?php
session_start();
include_once 'functions.php'; // ใช้ include_once เพื่อหลีกเลี่ยงการประกาศซ้ำ

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_customer'])) {
    $customer_id = $_POST['customer_id'];
    $deleted_by = $_SESSION['user_id']; // ดึง user_id จาก session

    // เชื่อมต่อฐานข้อมูล
    $conn = connectDB();

    // เริ่มต้นธุรกรรม
    $conn->begin_transaction();

    try {
        // ลบรายการการทำธุรกรรมที่เกี่ยวข้องกับบัญชี
        $sql = "DELETE t FROM transactions t
                JOIN accounts a ON t.account_id = a.account_id
                WHERE a.customer_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement for transactions: " . $conn->error);
        }
        $stmt->bind_param("i", $customer_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting associated transactions: " . $stmt->error);
        }
        $stmt->close();

        // ลบบัญชีที่เกี่ยวข้อง
        $sql = "DELETE FROM accounts WHERE customer_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement for accounts: " . $conn->error);
        }
        $stmt->bind_param("i", $customer_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting associated accounts: " . $stmt->error);
        }
        $stmt->close();

        // บันทึกการลบข้อมูลลูกค้า
        $sql = "INSERT INTO deleted_customers (customer_id, deleted_by, deleted_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement for deleted_customers: " . $conn->error);
        }
        $stmt->bind_param("ii", $customer_id, $deleted_by);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting into deleted_customers: " . $stmt->error);
        }
        $stmt->close();

        // ลบข้อมูลลูกค้า
        $sql = "DELETE FROM customers WHERE customer_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement for customers: " . $conn->error);
        }
        $stmt->bind_param("i", $customer_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting customer: " . $stmt->error);
        }
        $stmt->close();

        // คอมมิทธุรกรรม
        $conn->commit();
        echo "Customer and all associated records deleted successfully!";
        
    } catch (Exception $e) {
        // ยกเลิกธุรกรรมหากเกิดข้อผิดพลาด
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
    
    $conn->close();
}

$customer_id = isset($_GET['customer_id']) ? htmlspecialchars($_GET['customer_id']) : ''; // ตรวจสอบว่ามีค่า customer_id ถูกส่งมาหรือไม่
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Customer</title>
</head>
<body>
    <h1>Delete Customer</h1>
    <form action="delete_customer.php" method="POST">
        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
        <input type="submit" name="delete_customer" value="Delete Customer">
    </form>
    <a href="customer_list.php">Back to Customer List</a>
</body>
</html>
