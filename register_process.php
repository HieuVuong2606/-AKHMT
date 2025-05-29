<?php
session_start();
require "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($password) || empty($email) || empty($phone)) {
        $_SESSION["register_error"] = "Vui lòng điền đầy đủ thông tin.";
        header("Location: register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION["register_error"] = "Mật khẩu và xác nhận mật khẩu không khớp.";
        header("Location: register.php");
        exit();
    }

    // Kiểm tra cả username và email
    $check_sql = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check_sql->bind_param("ss", $username, $email);
    $check_sql->execute();
    $check_sql->store_result();

    if ($check_sql->num_rows > 0) {
        $_SESSION["register_error"] = "Tên đăng nhập hoặc email đã tồn tại.";
        $check_sql->close();
        header("Location: register.php");
        exit();
    }

    $check_sql->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $insert_sql = $conn->prepare("INSERT INTO users (username, password, email, phone, role) VALUES (?, ?, ?, ?, 'user')");
    $insert_sql->bind_param("ssss", $username, $hashed_password, $email, $phone);

    if ($insert_sql->execute()) {
        $_SESSION["register_success"] = "Đăng ký thành công. Bạn có thể đăng nhập.";
        header("Location: login.php");
        exit();
    } else {
        // Thêm thông báo lỗi chi tiết từ MySQL
        $_SESSION["register_error"] = "Có lỗi xảy ra: " . $conn->error;
        header("Location: register.php");
        exit();
    }
    
    $insert_sql->close();
}
?>