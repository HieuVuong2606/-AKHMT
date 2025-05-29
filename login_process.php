<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require "../config.php";

    $username = $_POST["username"];
    $password = $_POST["password"];

    // Sử dụng prepared statement và băm mật khẩu
    $stmt = $conn->prepare("SELECT id, username, password, role, email, phone FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra mật khẩu đã băm
        if (password_verify($password, $user['password'])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["user_role"] = $user["role"];
            $_SESSION["email"] = $user["email"] ?? '';
            $_SESSION["phone"] = $user["phone"] ?? '';
            
            // Chuyển hướng theo role
            if ($user["role"] === "admin") {
                header("Location: booking.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    }
    
    // Nếu đăng nhập thất bại
    $_SESSION["login_error"] = "Tên đăng nhập hoặc mật khẩu không đúng.";
    header("Location: login.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>