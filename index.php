<?php
session_start();
require_once "../config.php";

// Lấy thông tin các loại xe
$vehicles = $conn->query("SELECT * FROM vehicles WHERE is_available = TRUE LIMIT 3");

// Lấy thông tin đánh giá
// $reviews = $conn->query("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhà xe Tân Trung - Dịch vụ đưa đón & vận chuyển hàng hóa</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/includes.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0-beta3/css/all.min.css">
</head>

<body>
    <?php include "../includes/header.php"; ?>
    <?php include "../config.php"; ?>


    <!-- Banner chính -->
    <section class="main-banner">
        <div class="banner-content">
            <h1>Dịch vụ taxi & vận chuyển chuyên nghiệp</h1>
            <p>Đồng hành cùng bạn trên mọi nẻo đường</p>
        </div>
    </section>
    <!-- Form đặt xe nhanh -->
<!-- Form đặt xe nhanh -->
<section class="quick-booking">
    <div class="container">
        <div class="booking-card">
            <div class="booking-header">
                <h3><i class="fas fa-car"></i> ĐẶT XE NHANH</h3>
                <div class="service-tabs">
                    <button class="tab-btn active" data-service="taxi">Taxi</button>
                    <button class="tab-btn" data-service="delivery">Vận chuyển</button>
                </div>
            </div>
            
            <!-- Form cho dịch vụ Taxi -->
            <form action="booking.php" method="POST" class="booking-form" id="taxi-form">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Điểm đón</label>
                        <input type="text" name="pickup" placeholder="Nhập địa chỉ đón" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-flag-checkered"></i> Điểm đến</label>
                        <input type="text" name="destination" placeholder="Nhập địa chỉ đến" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-car-side"></i> Loại vé </label>
                        <select name="vehicle_type" required>
                            <option value="">-- Chọn loại vé --</option>
                            <option value="small">Xe Ghép (150k/người)</option>
                            <option value="medium"> Bao Xe (500k/xe)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Thời gian</label>
                        <input type="datetime-local" name="booking_time" required>
                    </div>
                </div>
                
                <button type="submit" class="booking-btn">
                    <i class="fas fa-search"></i> ĐẶT XE NGAY
                </button>
            </form>
            
            <!-- Form cho dịch vụ Vận chuyển -->
            <form action="booking.php" method="POST" class="booking-form" id="delivery-form" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Điểm lấy hàng</label>
                        <input type="text" name="pickup" placeholder="Nhập địa chỉ lấy hàng" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-flag-checkered"></i> Điểm giao hàng</label>
                        <input type="text" name="destination" placeholder="Nhập địa chỉ giao hàng" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-truck"></i> giá vé </label>
                        <select name="truck_type" required>
                            <option value="">-- Chọn loại vé --</option>
                            <option value="small"> Xe ghép (50k/đồ)</option>
                            <option value="medium"> Bao xe (500k/xe)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-box"></i> Loại hàng hóa</label>
                        <input type="text" name="cargo_type" placeholder="Mô tả hàng hóa" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-weight-hanging"></i> Trọng lượng (kg)</label>
                        <input type="number" name="weight" placeholder="Nhập trọng lượng" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Thời gian</label>
                        <input type="datetime-local" name="booking_time" required>
                    </div>
                </div>
                
                <button type="submit" class="booking-btn">
                    <i class="fas fa-search"></i> ĐẶT XE NGAY
                </button>
            </form>
        </div>
    </div>
</section>

    <!-- Dịch vụ chính -->
<section class="services-section">
    <div class="container">
        <h2 class="section-title">
            <span>DỊCH VỤ CỦA CHÚNG TÔI</span>
        </h2>
        <p class="section-subtitle">Những dịch vụ chất lượng chúng tôi cung cấp</p>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-taxi"></i>
                </div>
                <h3>Taxi đưa đón</h3>
                <p>Dịch vụ taxi 24/7 với đội ngũ tài xế chuyên nghiệp, xe đời mới, đảm bảo an toàn</p>
            </div>
            
            <div class="service-card highlight">
                <div class="service-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Vận chuyển hàng hóa</h3>
                <p>Vận chuyển hàng hóa nhanh chóng, an toàn với nhiều loại xe từ nhỏ đến lớn</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-plane-departure"></i>
                </div>
                <h3>Taxi sân bay</h3>
                <p>Đưa đón sân bay đúng giờ, giá cả hợp lý, hỗ trợ đặt xe trước 24/7</p>
            </div>
        </div>
    </div>
</section>

    <!-- Đội xe -->
    <section class="vehicle-section">
    <div class="container">
        <h2 class="section-title">
            <span>ĐOÀN XE CỦA CHÚNG TÔI</span>
        </h2>
        <p class="section-subtitle">Đa dạng loại xe phục vụ mọi nhu cầu của bạn</p>
        
        <div class="vehicle-grid">
            <!-- Xe 4 chỗ -->
            <div class="vehicle-card" data-type="4">
                <div class="vehicle-badge"> Ảnh chính </div>
                <div class="vehicle-image">
                    <img src="../img/vehicle-1.jpg" alt="VF5">
                </div>
                <div class="vehicle-info">
                    <h3> VF5 </h3>
                    <div class="vehicle-meta">
                        <span><i class="fas fa-users"></i> 4 chỗ</span>
                        <span><i class="fas fa-gas-pump"></i> Xe Điện </span>
                    </div>
                    <div class="vehicle-price">
                        <span>10,000đ/km</span>
                        <button class="book-btn">Đặt ngay</button>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</section>

    <!-- Đánh giá khách hàng -->
    <section class="testimonial-section">
    <div class="container">
        <h2 class="section-title">ĐÁNH GIÁ CỦA KHÁCH HÀNG</h2>
        <div class="testimonial-grid">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                <div class="testimonial-card">
                    <div class="rating">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $review['rating'] ? 'active' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="review-text">"<?= htmlspecialchars($review['comment']) ?>"</p>
                    <p class="review-author">- <?= htmlspecialchars($review['username']) ?> -</p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Chưa có đánh giá nào.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include "../includes/footer.php"; ?>

<script>
function setupServiceTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const taxiForm = document.querySelector('#taxi-form');
    const deliveryForm = document.querySelector('#delivery-form');
    
    // Only proceed if all elements exist
    if (tabButtons.length && taxiForm && deliveryForm) {
        tabButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all buttons
                tabButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show the corresponding form
                if (this.dataset.service === 'taxi') {
                    taxiForm.style.display = 'block';
                    deliveryForm.style.display = 'none';
                } else {
                    taxiForm.style.display = 'none';
                    deliveryForm.style.display = 'block';
                }
            });
        });
        
        // Initialize - show taxi form by default
        tabButtons[0].classList.add('active');
        taxiForm.style.display = 'block';
        deliveryForm.style.display = 'none';
    }
}

// Call the function when DOM is loaded
document.addEventListener('DOMContentLoaded', setupServiceTabs);
</script>

</body>
</html>