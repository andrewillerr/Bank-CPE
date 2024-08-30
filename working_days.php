<?php
session_start();
include 'functions.php'; // รวมฟังก์ชันที่จำเป็น

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ตรวจสอบการส่งเดือนและปี
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// ดึงข้อมูลวันทำการ
$working_days = getWorkingDays($month, $year);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Working Days</title>
</head>
<body>
    <h1>Working Days for <?php echo htmlspecialchars("$month/$year"); ?></h1>
    <table border="1">
        <tr>
            <th>Working Day ID</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php if ($working_days->num_rows > 0): ?>
            <?php while ($row = $working_days->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['working_day_id']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo $row['is_open'] ? 'Open' : 'Closed'; ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
        <tr>
            <td colspan="3">No working days found for this period.</td>
        </tr>
        <?php endif; ?>
    </table>
</body>
</html>
