<?php
// ฟังก์ชันการเชื่อมต่อฐานข้อมูล
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bank_database";

    // สร้างการเชื่อมต่อ
    $conn = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
