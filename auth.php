<?php
// Đảm bảo kết nối database được thiết lập
require_once '../config.php';

// Kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Lấy thông tin người dùng hiện tại
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    } catch (Exception $e) {
        error_log("Lỗi khi lấy thông tin người dùng: " . $e->getMessage());
        return null;
    }
}

// Kiểm tra quyền admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>