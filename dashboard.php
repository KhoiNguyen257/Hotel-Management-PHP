<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
$db = Database::getInstance();

// 1. Lấy số lượng phòng theo trạng thái
$cleaningCount  = $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'Cleaning'")->fetchColumn();
$availableCount = $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'Available'")->fetchColumn();
$occupiedCount  = $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'Occupied'")->fetchColumn();
$reservedCount  = $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'Reserved'")->fetchColumn();

// 2. Lấy dữ liệu doanh thu 7 ngày gần nhất để vẽ biểu đồ
$revenueData = $db->query("
    SELECT DATE(issued_at) as date, SUM(grand_total) as total 
    FROM invoices 
    WHERE payment_status = 'Paid' 
    GROUP BY DATE(issued_at) 
    ORDER BY date DESC 
    LIMIT 7
")->fetchAll();

// Chuẩn bị dữ liệu cho JavaScript (Chart.js)
$labels = [];
$totals = [];
foreach (array_reverse($revenueData) as $row) {
    $labels[] = date('d/m', strtotime($row['date']));
    $totals[] = $row['total'];
}

// 3. Tính tổng doanh thu mọi thời đại (cho Manager)
$totalRevenue = $db->query("SELECT SUM(grand_total) FROM invoices WHERE payment_status = 'Paid'")->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="dashboard.php">HMS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="rooms/list.php">Quản lý Phòng</a></li>
                    <li class="nav-item"><a class="nav-link" href="bookings/list.php">Danh sách Đặt phòng</a></li>
                    
                    <?php if($_SESSION['role'] == 'manager'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="users/manage.php">Nhân sự</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="text-white">
                    <span class="me-3">Xin chào, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong></span>
                    <a href="logout.php" class="btn btn-sm btn-danger">Đăng xuất</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <h2 class="fw-bold mb-4">Tổng quan hệ thống</h2>
        
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card bg-primary text-white shadow border-0 h-100 p-3">
                    <h6><i class="bi bi-door-open me-2"></i>Phòng trống</h6>
                    <h2 class="fw-bold"><?= $availableCount ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white shadow border-0 h-100 p-3">
                    <h6><i class="bi bi-check-circle me-2"></i>Đã Check-in</h6>
                    <h2 class="fw-bold"><?= $occupiedCount ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark shadow border-0 h-100 p-3">
                    <h6><i class="bi bi-calendar-check me-2"></i>Sắp đến</h6>
                    <h2 class="fw-bold"><?= $reservedCount ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-danger text-white shadow border-0 h-100 p-3">
                    <h6><i class="bi bi-tools me-2"></i>Đang dọn dẹp</h6>
                    <h2 class="fw-bold"><?= $cleaningCount ?></h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow border-0 p-4">
                    <h5 class="fw-bold mb-4">Doanh thu 7 ngày gần nhất</h5>
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

            <div class="col-md-4">
                <?php if($_SESSION['role'] == 'manager'): ?>
                <div class="card shadow border-0 p-4 border-start border-5 border-success mb-4">
                    <h6 class="text-muted">Tổng doanh thu thực tế</h6>
                    <h3 class="text-success fw-bold"><?= number_format($totalRevenue) ?> đ</h3>
                </div>
                <?php endif; ?>

                <div class="card shadow border-0 p-4">
                    <h6 class="fw-bold">Mẹo quản lý</h6>
                    <p class="small text-muted">Bạn nên kiểm tra danh sách dọn dẹp mỗi sáng để tối ưu hóa tỷ lệ lấp đầy phòng.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'bar', // Có thể đổi thành 'line' nếu muốn biểu đồ đường
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?= json_encode($totals) ?>,
                    backgroundColor: 'rgba(25, 135, 84, 0.6)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + 'đ';
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>