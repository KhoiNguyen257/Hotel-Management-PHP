<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Chủ - Khách Sạn ALEX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .room-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .room-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .room-img { height: 250px; object-fit: cover; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-building me-2"></i>ALEX Hotel</a>
            <div class="d-flex">
                <a href="login.php" class="btn btn-primary btn-sm fw-bold px-4">Đăng Nhập Quản Lý</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <h1 class="fw-bold text-primary mb-3">Chào mừng đến với ALEX Hotel</h1>
        <p class="text-muted fs-5">Trải nghiệm không gian nghỉ dưỡng đẳng cấp và tiện nghi hàng đầu.</p>
    </div>

    <div class="container my-5">
        <h3 class="fw-bold border-bottom pb-2 mb-4">Các Loại Phòng Của Chúng Tôi</h3>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 room-card">
                    <img src="assets/img/standard.jpg" class="card-img-top room-img" alt="Phòng Standard">
                    <div class="card-body d-flex flex-column">
                        <h4 class="card-title fw-bold text-dark">Standard Room</h4>
                        <p class="card-text text-muted small">Phòng tiêu chuẩn với thiết kế ấm cúng, phù hợp cho cá nhân hoặc cặp đôi đi công tác ngắn ngày.</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-light text-dark border me-1"><i class="bi bi-wifi"></i> Free WiFi</span>
                            <span class="badge bg-light text-dark border me-1"><i class="bi bi-tv"></i> Smart TV</span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-snow"></i> Điều hòa</span>
                        </div>
                        
                        <div class="mt-auto">
                            <h5 class="text-danger fw-bold">500,000đ <span class="text-muted fs-6 fw-normal">/ đêm</span></h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 room-card">
                    <img src="assets/img/deluxe.jpg" class="card-img-top room-img" alt="Phòng Deluxe">
                    <div class="card-body d-flex flex-column">
                        <h4 class="card-title fw-bold text-dark">Deluxe Room</h4>
                        <p class="card-text text-muted small">Không gian rộng rãi, cửa sổ lớn ngắm cảnh thành phố. Lựa chọn tuyệt vời cho kỳ nghỉ trọn vẹn.</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-light text-dark border me-1"><i class="bi bi-wifi"></i> Free WiFi</span>
                            <span class="badge bg-light text-dark border me-1"><i class="bi bi-tv"></i> Smart TV</span>
                            <span class="badge bg-light text-dark border me-1"><i class="bi bi-cup-hot"></i> Trà/Cà phê</span>
                        </div>
                        
                        <div class="mt-auto">
                            <h5 class="text-danger fw-bold">800,000đ <span class="text-muted fs-6 fw-normal">/ đêm</span></h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 room-card">
                    <img src="assets/img/suite.jpg" class="card-img-top room-img" alt="Phòng Suite">
                    <div class="card-body d-flex flex-column">
                        <h4 class="card-title fw-bold text-dark">Suite VIP</h4>
                        <p class="card-text text-muted small">Phòng hạng sang với khu vực phòng khách riêng biệt, nội thất cao cấp mang lại trải nghiệm hoàng gia.</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-light text-dark border me-1"><i class="bi bi-wifi"></i> Free WiFi</span>
                            <span class="badge bg-light text-dark border me-1"><i class="bi bi-star"></i> Nội thất VIP</span>
                            <span class="badge bg-light text-dark border me-1"><i class="bi bi-cup-straw"></i> Minibar</span>
                        </div>
                        
                        <div class="mt-auto">
                            <h5 class="text-danger fw-bold">1,500,000đ <span class="text-muted fs-6 fw-normal">/ đêm</span></h5>
                        </div>
                    </div>
                </div>
            </div>

        </div> </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p class="mb-0">&copy; 2026 ALEX Hotel Management System. Developed by Alex.</p>
    </footer>

</body>
</html>