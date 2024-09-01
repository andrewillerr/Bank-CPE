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
    <center><iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=1&ctz=Asia%2FBangkok&bgcolor=%23F6BF26&src=Y182ZmJmNTI2YzUxNzVjM2FlYzQ2MGNhN2UzMDUzZGM1MzU5YjE3NjNjYjBlMDA4YWNjNWUzODYyNDdkOTYxOGU3QGdyb3VwLmNhbGVuZGFyLmdvb2dsZS5jb20&color=%23b5783f" style="border:solid 1px #777" width="800" height="600" frameborder="0" scrolling="no"></iframe><br>
    <a href="account_list.php">Account List</a><br>
    <a href="transaction_list.php">Transaction List</a><br>
    <!-- <a href="working_days.php">Working Days</a><br> -->
    <a href="logout.php">Logout</a></br>
   

</body>
</html>
