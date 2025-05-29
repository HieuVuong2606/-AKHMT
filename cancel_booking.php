<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Kiểm tra xem đơn đặt xe có thuộc về người dùng này không
    $check_sql = "SELECT status FROM bookings WHERE id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $booking_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        
        // Chỉ cho phép hủy nếu đơn đang ở trạng thái pending
        if ($booking['status'] == 'pending') {
            // Cập nhật trạng thái thành cancelled
            $update_sql = "UPDATE bookings SET status = 'cancelled', cancelled_at = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $booking_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['booking_success'] = "Đã hủy đơn đặt xe #$booking_id thành công!";
                
                // Ghi log hủy đơn
                $log_sql = "INSERT INTO booking_logs (booking_id, action, user_id, details) 
                            VALUES (?, 'cancelled', ?, 'Khách hàng hủy đơn')";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param("ii", $booking_id, $user_id);
                $log_stmt->execute();
            } else {
                $_SESSION['booking_error'] = "Có lỗi xảy ra khi hủy đơn đặt xe.";
            }
        } else {
            $_SESSION['booking_error'] = "Không thể hủy đơn đặt xe này vì nó đã được xác nhận hoặc hoàn thành.";
        }
    } else {
        $_SESSION['booking_error'] = "Không tìm thấy đơn đặt xe hoặc bạn không có quyền hủy đơn này.";
    }
}

header("Location: booking.php");
exit();
?>