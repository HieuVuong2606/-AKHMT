<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Hàm lấy danh sách đặt xe của người dùng với thông tin đầy đủ
// Thay thế đoạn code hiện tại bằng:
function getUserBookings($userId) {
    global $conn;
    
    // Kiểm tra kết nối
    if (!$conn) {
        die("Không thể kết nối đến cơ sở dữ liệu");
    }

    try {
        $sql = "SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_time DESC";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
        }
        
        $stmt->bind_param("i", $userId);
        
        if (!$stmt->execute()) {
            throw new Exception("Lỗi thực thi câu lệnh: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if (!$result) {
            throw new Exception("Lỗi lấy kết quả: " . $stmt->error);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        // Ghi log lỗi
        error_log("Lỗi trong getUserBookings: " . $e->getMessage());
        return []; // Trả về mảng rỗng nếu có lỗi
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra và lấy dữ liệu từ form
    $car_type = $_POST['vehicle_type'] ?? '';
    $pickup = $_POST['pickup'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';
    $passengers = $_POST['passengers'] ?? 1;
    $notes = $_POST['notes'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $user_id = $_SESSION['user_id'];
    
    // Validate dữ liệu
    $errors = [];
    if (empty($car_type)) $errors[] = "Vui lòng chọn loại xe";
    if (empty($pickup)) $errors[] = "Vui lòng nhập điểm đón";
    if (empty($destination)) $errors[] = "Vui lòng nhập điểm đến";
    if (empty($booking_time)) $errors[] = "Vui lòng chọn thời gian đón";
    if ($passengers < 1) $errors[] = "Số hành khách không hợp lệ";
    
    if (!empty($errors)) {
        $_SESSION['booking_errors'] = $errors;
        $_SESSION['old_booking_data'] = $_POST;
        header("Location: index.php");
        exit();
    }
    
    // Thêm đơn đặt xe vào CSDL
    try {
        $sql = "INSERT INTO bookings (user_id, car_type, pickup, destination, booking_time, passengers, notes, payment_method, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssiss", $user_id, $car_type, $pickup, $destination, $booking_time, $passengers, $notes, $payment_method);
        
        if ($stmt->execute()) {
            $_SESSION['booking_success'] = "Đặt xe thành công! Mã đơn: " . $conn->insert_id;
            header("Location: booking.php");
            exit();
        } else {
            $_SESSION['booking_errors'] = ["Có lỗi xảy ra khi đặt xe. Vui lòng thử lại."];
            header("Location: index.php");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['booking_errors'] = ["Lỗi cơ sở dữ liệu: " . $e->getMessage()];
        header("Location: index.php");
        exit();
    }
}

$user_id = $_SESSION['user_id'];
$bookings = getUserBookings($user_id);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ - Taxi Tân Trung</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/includes.css">
    <style>
        .booking-details {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .driver-info {
            background: #e9f7ef;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .status-pending { color: #ff9800; }
        .status-confirmed { color: #4caf50; }
        .status-cancelled { color: #f44336; }
        .status-completed { color: #2196f3; }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>
    
    <div class="profile-container">
        <h1>HỒ SƠ CÁ NHÂN</h1>
        
        <!-- Hiển thị thông báo -->
        <?php if (isset($_SESSION['booking_success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['booking_success']; ?>
                <?php unset($_SESSION['booking_success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['booking_error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['booking_error']; ?>
                <?php unset($_SESSION['booking_error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-info">
            <h2>Thông tin tài khoản</h2>
            <p><strong>Tên:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email'] ?? 'Chưa cập nhật') ?></p>
            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($_SESSION['phone'] ?? 'Chưa cập nhật') ?></p>
            <a href="edit_booking.php" class="edit-btn">Chỉnh sửa thông tin</a>
        </div>
        
        <div class="booking-history">
            <h2>Lịch sử đặt xe</h2>
            
            <?php if (count($bookings) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Loại xe</th>
                            <th>Điểm đón</th>
                            <th>Điểm đến</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= $booking['id'] ?></td>
                            <td>
                                <?php 
                                switch($booking['car_type']) {
                                    case '4': echo 'Xe 4 chỗ'; break;
                                    case '7': echo 'Xe 7 chỗ'; break;
                                    case '16': echo 'Xe 16 chỗ'; break;
                                    default: echo $booking['car_type'];
                                }
                                ?>
                                <?php if ($booking['passengers'] > 0): ?>
                                    <br><small>(<?= $booking['passengers'] ?> khách)</small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($booking['pickup']) ?></td>
                            <td><?= htmlspecialchars($booking['destination']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($booking['booking_time'])) ?></td>
                            <td class="status-<?= $booking['status'] ?>">
                                <?= ucfirst($booking['status']) ?>
                            </td>
                            <td>
                                <?php if ($booking['status'] == 'pending'): ?>
                                    <a href="cancel_booking.php?id=<?= $booking['id'] ?>" class="cancel-btn">Hủy</a>
                                <?php endif; ?>
                                <button class="view-details-btn" data-booking="<?= htmlspecialchars(json_encode($booking)) ?>">Chi tiết</button>
                            </td>
                        </tr>
                        <tr class="details-row" id="details-<?= $booking['id'] ?>" style="display:none;">
                            <td colspan="7">
                                <div class="booking-details">
                                    <p><strong>Ghi chú:</strong> <?= !empty($booking['notes']) ? htmlspecialchars($booking['notes']) : 'Không có' ?></p>
                                    <p><strong>Phương thức thanh toán:</strong> 
                                        <?= $booking['payment_method'] === 'cash' ? 'Tiền mặt' : 'Chuyển khoản' ?>
                                    </p>
                                    <p><strong>Thời gian tạo đơn:</strong> <?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></p>
                                    
                                    <?php if (!empty($booking['driver_id'])): ?>
                                        <div class="driver-info">
                                            <h4>Thông tin tài xế</h4>
                                            <p><strong>Tên tài xế:</strong> <?= htmlspecialchars($booking['driver_name']) ?></p>
                                            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($booking['driver_phone']) ?></p>
                                            <p><strong>Biển số xe:</strong> <?= htmlspecialchars($booking['license_plate']) ?></p>
                                            <p><strong>Model xe:</strong> <?= htmlspecialchars($booking['model']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Bạn chưa có đơn đặt xe nào</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include "../includes/footer.php"; ?>
    
    <script>
        document.querySelectorAll('.view-details-btn').forEach(button => {
            button.addEventListener('click', function() {
                const bookingId = JSON.parse(this.getAttribute('data-booking')).id;
                const detailsRow = document.getElementById(`details-${bookingId}`);
                detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
            });
        });
    </script>
</body>
</html>