<!-- <?php
include 'db.php'; // รวมไฟล์ที่มีฟังก์ชัน connectDB()

$conn = connectDB(); // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลลูกค้า
$sql = "SELECT c.*, u.username AS modified_by_name, c.modified_at FROM customers c LEFT JOIN users u ON c.modified_by = u.user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer List</title>
</head>
<body>
    <h1>Customer List</h1>
    <table border="1">
        <tr>
            <th>Customer ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Date of Birth</th>
            <th>Last Modified By</th>
            <th>Last Modified At</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['customer_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                echo "<td>" . htmlspecialchars($row['modified_by_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['modified_at']) . "</td>";
                echo "<td>
                    <a href='edit_customer.php?customer_id=" . htmlspecialchars($row['customer_id']) . "'>Edit</a> | 
                    <a href='delete_customer.php?customer_id=" . htmlspecialchars($row['customer_id']) . "' onclick=\"return confirm('Are you sure you want to delete this customer?');\">Delete</a>
                </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No customers found</td></tr>";
        }
        ?>
    </table>
    <a href="add_customer.php">Add New Customer</a>
</body>
</html> -->


<?php
include_once 'db.php'; // รวมไฟล์ที่มีฟังก์ชัน connectDB() แบบไม่ให้ซ้ำ

$conn = connectDB(); // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลลูกค้า
$sql = "SELECT c.*, u.username AS modified_by_name, c.modified_at 
        FROM customers c 
        LEFT JOIN users u ON c.modified_by = u.user_id";
$result = $conn->query($sql);

if (!$result) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer List</title>
    <script src="current_time.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .actions a {
            margin-right: 10px;
        }
        .add-new {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            font-weight: bold;
        }
        .add-new:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <center><p>ขณะนี้เวลา: <span id="current-time"></span></p></center>

    <div class="container">
        <h1>Customer List</h1>
        <table>
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Date of Birth</th>
                    <th>Last Modified By</th>
                    <th>Last Modified At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['dob']); ?></td>
                            <td><?php echo htmlspecialchars($row['modified_by_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['modified_at']); ?></td>
                            <td class="actions">
                                <a href="edit_customer.php?customer_id=<?php echo urlencode($row['customer_id']); ?>">Edit</a>
                                <a href="delete_customer.php?customer_id=<?php echo urlencode($row['customer_id']); ?>" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No customers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="add_customer.php" class="add-new">Add New Customer</a>
        <p1><a href="index.php">Back to Home</a></p1>

    </div>
</body>
</html>

<?php $conn->close(); ?>

