<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// include 'current_datetime.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nisit CPE9</title>
    <script src="current_time.js"></script>

</head>
<body>
    <h1>ระบบฝาก-ถอนเงิน CPE9</h1>
    
    <p>ขณะนี้เวลา: <span id="current-time"></span></p>

    <a href="customer_list.php">Customer List</a><br>
    <a href="account_list.php">Account List</a><br>
    <a href="transaction_list.php">Transaction List</a><br>
    <!-- <a href="working_days.php">Working Days</a><br> -->
    <a href="logout.php">Logout</a></br>
   

</body>
</html>
