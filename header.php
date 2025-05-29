<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Nhà xe Tân Trung'; ?></title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/include.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="modern-header">
        <div class="header-container">
            <div class="brand-section">
                <div class="logo">
                    <a href="../index.php">
                        <img src="../img/logo.png" alt="Tân Trung Logo">
                    </a>
                    <div class="logo-text">
                        <h1>Nhà xe Tân Trung</h1>
                        <p>An toàn - Chu đáo - Đúng giờ</p>
                    </div>
                </div>
            </div>
            
            <!-- Phần bên phải - Thông tin liên hệ và tài khoản -->
            <div class="header-contacts">
                <div class="contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <span>Hotline</span>
                        <strong>0812203203</strong>
                    </div>
                </div>
                
                <?php if (isLoggedIn()): ?>
                    <!-- Hiển thị khi đã đăng nhập -->
                    <div class="contact-item account-info">
                        <i class="fas fa-user-circle"></i>
                        <div>
                            <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Khách'); ?></span>
                            <a href="../view/logout.php" class="logout-link">Đăng xuất</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Hiển thị khi chưa đăng nhập -->
                    <div class="contact-item">
                        <i class="fas fa-user"></i>
                        <div>
                            <a href="../view/login.php" class="btn secondary">Đăng nhập</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-user-plus"></i>
                        <div>
                            <a href="../view/register.php" class="btn secondary">Đăng ký</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
</body>
</html>