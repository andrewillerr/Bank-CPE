<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// ฟังก์ชันเพิ่มลูกค้า
function addCustomer($name, $email, $phone) {
    $conn = connectDB();
    
    // เตรียมคำสั่ง SQL
    $sql = "INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("sss", $name, $email, $phone);
    
    if (!$stmt->execute()) {
        die('Execute failed: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    return true;
}

// ฟังก์ชันล็อกอิน
function login($username, $password) {
    $conn = connectDB();
    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $stmt->close();
            $conn->close();
            return true;
        }
    }

    $stmt->close();
    $conn->close();
    return false;
}

// ฟังก์ชันลงทะเบียนผู้ใช้
function registerUser($username, $password, $role) {
    $conn = connectDB();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return false; // ชื่อผู้ใช้มีอยู่แล้ว
    }

    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    $success = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $success;
}

// ฟังก์ชันดึงข้อมูลลูกค้าทั้งหมด
function getCustomers() {
    $conn = connectDB();
    $sql = "SELECT * FROM customers";
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

// ฟังก์ชันดึงข้อมูลที่ถูกลบ
function getDeletedCustomers() {
    $conn = connectDB();
    $sql = "SELECT d.id, c.name AS customer_name, u.username AS deleted_by, d.deleted_at
            FROM deleted_customers d
            JOIN customers c ON d.customer_id = c.customer_id
            JOIN users u ON d.deleted_by = u.user_id
            ORDER BY d.deleted_at DESC";
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

// ฟังก์ชันดึงข้อมูลสรุปประจำวัน
function getDailySummary($date) {
    $conn = connectDB();

    // คำสั่ง SQL สำหรับดึงข้อมูลยอดฝากและถอน
    $sql = "SELECT
                COALESCE(SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE 0 END), 0) AS total_deposits,
                COALESCE(SUM(CASE WHEN transaction_type = 'withdraw' THEN amount ELSE 0 END), 0) AS total_withdrawals
            FROM transactions
            WHERE DATE(transaction_date) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $summary = $result->fetch_assoc();

    // ตรวจสอบข้อผิดพลาด
    if ($summary === null) {
        die('Error fetching daily summary: ' . $conn->error);
    }

    // ตรวจสอบค่า
    if ($summary['total_withdrawals'] === null) {
        echo 'No withdrawals found for the given date.';
    }

    $stmt->close();
    $conn->close();

    return [
        'total_deposits' => $summary['total_deposits'] ?? 0,
        'total_withdrawals' => $summary['total_withdrawals'] ?? 0,
    ];
}


// ฟังก์ชันดึงข้อมูลวันทำการ
function getWorkingDays($month, $year) {
    $conn = connectDB();
    $sql = "SELECT * FROM working_days WHERE MONTH(date) = ? AND YEAR(date) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();
    return $result;
}

// ฟังก์ชันการอัปเดตยอดเงินคงเหลือในธนาคารหลังจากปิดบัญชี
// ฟังก์ชันการอัปเดตยอดเงินคงเหลือในธนาคารหลังจากปิดบัญชี
function updateBankBalanceAfterCloseAccount($account_id) {
    $conn = connectDB();
    
    // ดึงยอดเงินของบัญชีที่จะปิด
    $stmt = $conn->prepare("SELECT balance FROM accounts WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    
    if ($account) {
        $balance_to_deduct = $account['balance'];
        
        // อัปเดตยอดเงินของบัญชีให้เป็น 0.00
        $stmt = $conn->prepare("UPDATE accounts SET balance = 0.00 WHERE account_id = ?");
        $stmt->bind_param("i", $account_id);
        if (!$stmt->execute()) {
            die('Error updating account balance: ' . $stmt->error);
        }
        
        // สมมติว่ามีตาราง `bank_balance` ที่เก็บยอดเงินรวมของธนาคาร
        $stmt = $conn->prepare("UPDATE bank_balance SET balance = balance - ?");
        $stmt->bind_param("d", $balance_to_deduct);
        if (!$stmt->execute()) {
            die('Error updating bank balance: ' . $stmt->error);
        }
    }

    $stmt->close();
    $conn->close();
}



// ฟังก์ชันการปิดบัญชี
// ฟังก์ชันการปิดบัญชี
function closeAccount($account_id) {
    $conn = connectDB(); // เรียกใช้ฟังก์ชัน connectDB() เพื่อสร้างการเชื่อมต่อฐานข้อมูล

    // อัปเดตยอดเงินของบัญชีให้เป็น 0.00
    $stmt = $conn->prepare("UPDATE accounts SET balance = 0.00, closed_by = ?, closed_at = NOW() WHERE account_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $account_id);
    
    if (!$stmt->execute()) {
        die('Error updating account balance: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}


// ตรวจสอบการขอปิดบัญชี
if (isset($_GET['action']) && $_GET['action'] === 'close' && isset($_GET['account_id'])) {
    closeAccount($_GET['account_id']);
    header("Location: account_list.php"); // รีเฟรชหน้า
    exit();
}

function getClosedAccountsTotal() {
    $conn = connectDB();
    $sql = "SELECT SUM(closed_balance) AS total_closed_balance FROM closed_accounts";
    $result = $conn->query($sql);

    $total = 0;
    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['total_closed_balance'] ?? 0;
    }
    
    $conn->close();
    return $total;
}


// ฟังก์ชันคำนวณดอกเบี้ยและอัปเดตข้อมูลในฐานข้อมูล
function calculateAndUpdateInterest($account_id) {
    $conn = connectDB();
    
    // ดึงข้อมูลยอดเงินฝากเริ่มต้นและวันที่เริ่มต้น
    $stmt = $conn->prepare("SELECT deposit_start_date, total_deposit_amount, interest_amount
                            FROM accounts
                            WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    if ($account) {
        $start_date = new DateTime($account['deposit_start_date']);
        $current_date = new DateTime();
        $interval = $start_date->diff($current_date);

        // คำนวณดอกเบี้ยถ้าเวลาผ่านไปมากกว่าหนึ่งปี
        if ($interval->y > 0) {
            $interest_rate = 0.10; // 10%
            $principal = $account['total_deposit_amount'];
            $years = $interval->y;
            $interest = $principal * $interest_rate * $years;

            // อัปเดตยอดดอกเบี้ย
            $stmt = $conn->prepare("UPDATE accounts
                                    SET interest_amount = interest_amount + ?, deposit_start_date = NOW()
                                    WHERE account_id = ?");
            $stmt->bind_param("di", $interest, $account_id);
            if (!$stmt->execute()) {
                die('Error updating interest: ' . $stmt->error);
            }
        }
    }

    $stmt->close();
    $conn->close();
}

// ฟังก์ชันดึงข้อมูลบัญชีทั้งหมด
function getAllAccounts() {
    $conn = connectDB();
    $sql = "SELECT account_id, account_name, total_deposit_amount, interest_amount FROM accounts";
    $result = $conn->query($sql);

    if ($result === false) {
        die('Error fetching accounts: ' . $conn->error);
    }

    $accounts = $result->fetch_all(MYSQLI_ASSOC);

    $conn->close();

    return $accounts;
}
function getOverallBankBalance() {
    $conn = connectDB(); // ฟังก์ชันเพื่อสร้างการเชื่อมต่อฐานข้อมูล
    $sql = "SELECT SUM(balance) AS balance FROM accounts"; // คำสั่ง SQL เพื่อรวมยอดเงินทั้งหมดในตารางบัญชี
    $result = $conn->query($sql);
    
    $balance = 0;
    if ($result && $row = $result->fetch_assoc()) {
        $balance = $row['balance'] ?? 0;
    }
    
    $conn->close();
    return $balance;
}



?>
