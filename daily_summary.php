<?php
include 'functions.php'; // รวมฟังก์ชันที่จำเป็น

if (function_exists('getOverallBankBalance')) {
    echo "Function getOverallBankBalance is available.";
} else {
    echo "Function getOverallBankBalance is NOT available.";
}

// ดึงวันที่จาก query string หรือใช้วันที่ปัจจุบันเป็นค่าเริ่มต้น
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// ตรวจสอบวันที่ที่ได้รับเพื่อป้องกันข้อผิดพลาด
if (!DateTime::createFromFormat('Y-m-d', $date)) {
    $date = date('Y-m-d');
}

// ดึงข้อมูลสรุปประจำวัน
$summary = getDailySummary($date);

// ดึงยอดเงินคงเหลือทั้งหมด
$overallBalance = getOverallBankBalance();

// ดึงยอดเงินที่ปิดบัญชีไปแล้ว
$closedAccountsTotal = getClosedAccountsTotal();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daily Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type="date"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group input[type="submit"] {
            padding: 10px 15px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Daily Summary</h1>
        <form action="daily_summary.php" method="GET">
            <div class="form-group">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
            </div>
            <input type="submit" value="View Summary">
        </form>
        <h2>Summary for <?php echo htmlspecialchars($date); ?></h2>
        <p>Total Deposits: <?php echo htmlspecialchars(number_format($summary['total_deposits'], 2)); ?> THB</p>
        <p>Total Withdrawals: <?php echo htmlspecialchars(number_format($summary['total_withdrawals'], 2)); ?> THB</p>
        
        <h2>Overall Bank Balance</h2>
        <p>Overall Bank Balance: <?php echo htmlspecialchars(number_format($overallBalance, 2)); ?> THB</p>
        
        <!-- <h2>Closed Accounts</h2>
        <p>Total Closed Accounts Balance: <?php echo htmlspecialchars(number_format($closedAccountsTotal, 2)); ?> THB</p>
         -->
        <a href="index.php">Back to Home</a> <!-- เปลี่ยนลิงค์ตามที่คุณต้องการ -->
    </div>
</body>
</html>
