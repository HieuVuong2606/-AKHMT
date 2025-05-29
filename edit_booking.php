<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy thông tin người dùng từ session hoặc CSDL
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'] ?? '';
$phone = $_SESSION['phone'] ?? '';

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_phone = trim($_POST['phone']);
    
    // Validate dữ liệu
    $errors = [];
    
    if (empty($new_username)) {
        $errors[] = "Tên người dùng không được để trống";
    }
    
    if (!empty($new_email) && !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    if (!empty($new_phone) && !preg_match('/^[0-9]{10,15}$/', $new_phone)) {
        $errors[] = "Số điện thoại không hợp lệ";
    }
    
    // Nếu không có lỗi, cập nhật thông tin
    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $new_username, $new_email, $new_phone, $user_id);
            
            if ($stmt->execute()) {
                // Cập nhật session
                $_SESSION['username'] = $new_username;
                $_SESSION['email'] = $new_email;
                $_SESSION['phone'] = $new_phone;
                
                $_SESSION['profile_update_success'] = "Cập nhật thông tin thành công!";
                header("Location: booking.php");
                exit();
            } else {
                $errors[] = "Có lỗi xảy ra khi cập nhật thông tin";
            }
        } catch (mysqli_sql_exception $e) {
            // Kiểm tra lỗi trùng username hoặc email
            if ($e->getCode() == 1062) {
                $errors[] = "Tên người dùng hoặc email đã tồn tại";
            } else {
                $errors[] = "Lỗi cơ sở dữ liệu: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin - Taxi Tân Trung</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/includes.css">
    <style>
        .edit-profile-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .edit-profile-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        
        .edit-profile-form .form-group {
            margin-bottom: 15px;
        }
        
        .edit-profile-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .edit-profile-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .error-message {
            color: #ff4444;
            margin-bottom: 15px;
        }
        
        .error-message ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        
        .form-actions {
            margin-top: 20px;
            text-align: center;
        }
        
        .save-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .save-btn:hover {
            background-color: #45a049;
        }
        
        .cancel-btn {
            display: inline-block;
            margin-left: 10px;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .cancel-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>
    
    <div class="edit-profile-container">
        <h1>CHỈNH SỬA THÔNG TIN CÁ NHÂN</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form class="edit-profile-form" method="POST">
            <div class="form-group">
                <label for="username">Tên người dùng</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="save-btn">Lưu thay đổi</button>
                <a href="booking.php" class="cancel-btn">Hủy bỏ</a>
            </div>
        </form>
    </div>
    
    <?php include "../includes/footer.php"; ?>
</body>
</html>