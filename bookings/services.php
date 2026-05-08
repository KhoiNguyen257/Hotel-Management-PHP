<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../classes/Service.php';
require_once '../classes/BookingService.php';

$db = Database::getInstance();
$serviceModel = new Service();
$bsModel = new BookingService();

$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Xử lý khi nhấn nút Thêm dịch vụ
if (isset($_POST['btnAddService'])) {
    $serviceId = (int)$_POST['service_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        // Lấy đơn giá từ database để tính thành tiền
        $stmt = $db->prepare("SELECT price FROM services WHERE service_id = ?");
        $stmt->execute([$serviceId]);
        $price = $stmt->fetchColumn();
        
        $subtotal = $price * $quantity;
        
        // Lưu vào database (bảng booking_services)
        if ($bsModel->addService($bookingId, $serviceId, $quantity, $subtotal)) {
            // Tải lại trang để tránh lỗi submit lại form (F5)
            header("Location: services.php?id=" . $bookingId);
            exit();
        }
    }
}

// Lấy dữ liệu hiển thị ra màn hình
$allServices = $serviceModel->getAllServices();
$usedServices = $bsModel->getServicesByBooking($bookingId);

// Tính tổng tiền các dịch vụ đã gọi
$totalServicesCost = 0;
foreach ($usedServices as $us) {
    $totalServicesCost += $us['subtotal'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Dịch Vụ - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="../dashboard.php">HMS - Quản lý Dịch vụ</a>
            <a href="list.php" class="btn btn-outline-light btn-sm">Quay lại danh sách</a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white fw-bold">
                        Thêm Dịch Vụ Mới
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Chọn dịch vụ:</label>
                                <select name="service_id" class="form-select" required>
                                    <option value="">-- Lựa chọn --</option>
                                    <?php foreach($allServices as $s): ?>
                                        <option value="<?= $s['service_id'] ?>">
                                            <?= htmlspecialchars($s['service_name']) ?> - <?= number_format($s['price']) ?>đ
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số lượng:</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                            </div>
                            <button type="submit" name="btnAddService" class="btn btn-primary w-100">Cập nhật vào đơn</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white fw-bold">
                        Chi tiết dịch vụ Đơn #<?= $bookingId ?>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên dịch vụ</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($usedServices) > 0): ?>
                                    <?php foreach($usedServices as $u): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($u['service_name']) ?></td>
                                        <td class="text-center"><?= $u['quantity'] ?></td>
                                        <td class="text-end"><?= number_format($u['subtotal']) ?>đ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted">Khách chưa sử dụng dịch vụ nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                    <td colspan="2" class="text-end text-danger">TỔNG CỘNG:</td>
                                    <td class="text-end text-danger"><?= number_format($totalServicesCost) ?>đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>