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
    <center><p1>1.นายพรหมมินทร์ บัวพันธ์ (Aun) Manager Nisit ID: 6640203094</p1></center>
    <h1>Login</h1>
    <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
    <p style="color:red;"><?php echo $error; ?></p>

    <h1>Register</h1>
    <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="register_username" required><br>
        <label>Password:</label>
        <input type="password" name="register_password" required><br>
        <label>Role:</label>
        <select name="register_role" required>
            <option value="accountant">Accountant</option>
            <option value="manager">Manager</option>
        </select><br>
        <label>Secret Code:</label> <!-- ฟิลด์สำหรับรหัสเข้ารหัสพิเศษ -->
        <input type="text" name="secret_code" required><br>
        <input type="submit" name="register" value="Register">
    </form>
    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
</body>
</html>

