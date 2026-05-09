<?php
require_once '../includes/auth.php';
require_once '../classes/Booking.php';
require_once '../includes/db.php';
$bookingModel = new Booking();
$msg = "";
$db = Database::getInstance();


// Lắng nghe sự kiện click nút Check-in
if (isset($_GET['action']) && $_GET['action'] == 'checkin' && isset($_GET['id']) && isset($_GET['room_id'])) {
    $bookingId = (int)$_GET['id'];
    $roomId = (int)$_GET['room_id'];
    
    if ($bookingModel->checkIn($bookingId, $roomId)) {
        $msg = "<div class='alert alert-success'>Check-in thành công! Khách đã nhận phòng.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Lỗi hệ thống khi check-in.</div>";
    }
}

$sql = "SELECT b.*, c.full_name, r.room_number 
        FROM bookings b
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN rooms r ON b.room_id = r.room_id
        ORDER BY FIELD(b.status, 'Reserved', 'CheckedIn', 'CheckedOut', 'Cancelled') ASC, b.created_at DESC";

$bookings = $db->query($sql)->fetchAll();

// $bookings = $bookingModel->getAllBookings();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Đặt phòng - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="../dashboard.php">HMS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="../dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../rooms/list.php">Quản lý Phòng</a></li>
                    <li class="nav-item"><a class="nav-link active" href="list.php">Danh sách Đặt phòng</a></li>
                    
                    <?php if($_SESSION['role'] == 'manager'): ?>
                        <li class="nav-item"><a class="nav-link" href="../users/manage.php">Nhân sự</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Danh sách Đơn Đặt Phòng</h2>
            <a href="create.php" class="btn btn-primary">+ Tạo Đơn Mới</a>
        </div>
        
        <?= $msg ?>

        <div class="card shadow border-0">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Ngày đến</th>
                            <th>Ngày đi</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($bookings as $b): ?>
                        <tr>
                            <td>#<?= $b['booking_id'] ?></td>
                            <td><strong><?= htmlspecialchars($b['full_name']) ?></strong></td>
                            <td>P.<?= $b['room_number'] ?></td>
                            <td><?= date('d/m/Y', strtotime($b['check_in'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($b['check_out'])) ?></td>
                            <td class="text-danger fw-bold"><?= number_format($b['total_price']) ?>đ</td>
                            <td>
                                <?php if($b['status'] == 'Reserved'): ?>
                                    <span class="badge bg-warning text-dark">Chờ nhận phòng</span>
                                <?php elseif($b['status'] == 'CheckedIn'): ?>
                                    <span class="badge bg-success">Đang ở</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= $b['status'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($b['status'] == 'Reserved'): ?>
                                    <a href="list.php?action=checkin&id=<?= $b['booking_id'] ?>&room_id=<?= $b['room_id'] ?>" 
                                       class="btn btn-sm btn-success" onclick="return confirm('Xác nhận khách nhận phòng?');">
                                        Check-in
                                    </a>
                                <?php elseif($b['status'] == 'CheckedIn'): ?>
                                    <a href="services.php?id=<?= $b['booking_id'] ?>" class="btn btn-sm btn-info text-white me-1">Dịch vụ</a>
                                    <a href="checkout.php?id=<?= $b['booking_id'] ?>&room_id=<?= $b['room_id'] ?>" class="btn btn-sm btn-danger">Check-out</a>
                                <?php elseif($b['status'] == 'CheckedOut'): ?>
                                    <a href="../invoices/view.php?booking_id=<?= $b['booking_id'] ?>" class="btn btn-sm btn-secondary">
                                        <i class="bi bi-receipt"></i> Hóa đơn
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>