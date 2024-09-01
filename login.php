<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'functions.php'; // ใช้ include_once เพื่อป้องกันการประกาศซ้ำ

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        // การล็อกอิน
        $username = $_POST['username'];
        $password = $_POST['password'];

        // ตรวจสอบการล็อกอิน
        if (login($username, $password)) {
            header('Location: index.php');
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } elseif (isset($_POST['register'])) {
        // การลงทะเบียนผู้ใช้
        $register_username = $_POST['register_username'];
        $register_password = $_POST['register_password'];
        $register_role = $_POST['register_role'];
        $secret_code = $_POST['secret_code'];

        // ตรวจสอบรหัสเข้ารหัสพิเศษ
        if ($secret_code !== '1234') {
            $error = "Invalid secret code. Please enter the correct code to register.";
        } else {
            // ลงทะเบียนผู้ใช้
            if (registerUser($register_username, $register_password, $register_role)) {
                $success = "User registered successfully!";
            } else {
                $error = "Error: Username already exists or registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <a1>เข้าสู่ระบบรับ-เบิกเงิน CPE9</a1></br></br></br>
    
    <center><p1>รายชื่อพนักงาน</p1></center>
    <ul>
        <li><center><p1>1.นายพรหมมินทร์ บัวพันธ์ (Aun) Manager Nisit ID: 6640203094</p1></center></li>
        <li><center><p2>2. () Asst.Manager Nisit ID: 664020</p2></center></li>
        <li><center><p3>3. () Accountant   Nisit ID: 664020</p3></center></li>
        <li><center><p4>4. () Accountant   Nisit ID: 664020</p4></center></li>
    </ul>
    <h1>Login</h1>
    <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
    <p style="color:red;"><?php echo $error; ?></p>
    <center><iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=1&ctz=Asia%2FBangkok&bgcolor=%23F6BF26&src=Y182ZmJmNTI2YzUxNzVjM2FlYzQ2MGNhN2UzMDUzZGM1MzU5YjE3NjNjYjBlMDA4YWNjNWUzODYyNDdkOTYxOGU3QGdyb3VwLmNhbGVuZGFyLmdvb2dsZS5jb20&color=%23b5783f" style="border:solid 1px #777" width="800" height="600" frameborder="0" scrolling="no"></iframe><br>

    <h1>Register</h1>
    <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="register_username" required><br>
        <label>Password:</label>
        <input type="password" name="register_password" required><br>
        <label>ตำแหน่งงาน:</label>
        <select name="register_role" required>
            <option value="accountant">Accountant</option>
            <option value="manager">Manager</option>
        </select><br>
        <label>Top Secret Code:</label> <!-- ฟิลด์สำหรับรหัสเข้ารหัสพิเศษ -->
        <input type="text" name="secret_code" required><br>
        <input type="submit" name="register" value="Register">
    </form>
    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
</body>
</html>

