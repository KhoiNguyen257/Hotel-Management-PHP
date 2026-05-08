<?php
// rooms/list.php
require_once '../includes/auth.php'; // Chặn người chưa đăng nhập
require_once '../includes/db.php';   // Load DB trước
require_once '../classes/Room.php';  // Load Model

// 1. Khởi tạo biến Database và Model ngay từ đầu
$db = Database::getInstance();
$roomModel = new Room();

// 2. XỬ LÝ SỰ KIỆN NÚT BẤM DỌN DẸP (Cleaning <-> Available)
if (isset($_GET['action']) && $_GET['action'] == 'update_cleaning' && isset($_GET['room_id'])) {
    $roomId = (int)$_GET['room_id'];
    $currentStatus = $_GET['status'];
    
    // Nếu đang dọn -> Xong (Available). Nếu đang Trống -> Báo dọn (Cleaning)
    $newStatus = ($currentStatus == 'Cleaning') ? 'Available' : 'Cleaning';
    
    // Cập nhật trạng thái vào Database
    $stmt = $db->prepare("UPDATE rooms SET status = ? WHERE room_id = ?");
    if($stmt->execute([$newStatus, $roomId])) {
        // Cập nhật xong thì load lại trang cho mới
        header("Location: list.php"); 
        exit();
    }
}

// 3. XỬ LÝ BỘ LỌC TÌM KIẾM
$selectedFloor = isset($_GET['floor']) ? $_GET['floor'] : '';
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Lấy danh sách phòng dựa theo bộ lọc (nếu không chọn gì thì mặc định lấy hết)
$rooms = $roomModel->getFilteredRooms($selectedFloor, $selectedStatus);

function getRoomCardClass($status) {
    switch ($status) {
        case 'Available': return 'bg-success text-white';
        case 'Reserved':  return 'bg-warning text-dark';
        case 'Occupied':  return 'bg-danger text-white';
        case 'Cleaning':  return 'bg-info text-dark';
        default: return 'bg-secondary text-white';
    }
}

// Hàm hỗ trợ đổi màu thẻ (Badge) Bootstrap dựa theo trạng thái phòng
function getStatusCardClass($status) {
    switch ($status) {
        case 'Available': return 'bg-success text-white'; // Xanh lá
        case 'Reserved':  return 'bg-warning text-dark'; // Vàng
        case 'Occupied':  return 'bg-danger text-white'; // Đỏ
        case 'Cleaning':  return 'bg-info text-white';   // Xanh dương
        default: return 'bg-secondary text-white';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phòng - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="../dashboard.php"><i class="bi bi-building me-2"></i>HMS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="../dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="list.php">Quản lý Phòng</a></li>
                    <li class="nav-item"><a class="nav-link" href="../bookings/list.php">Danh sách Đặt phòng</a></li>
                    
                    <?php if($_SESSION['role'] == 'manager'): ?>
                        <li class="nav-item"><a class="nav-link" href="../users/manage.php">Nhân sự</a></li>
                    <?php endif; ?>
                </ul>
                <div class="text-white">
                    <span class="me-3">Xin chào, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong></span>
                    <a href="../logout.php" class="btn btn-sm btn-danger">Đăng xuất</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Sơ đồ Phòng</h2>
            <div class="card shadow-sm border-0 mb-4 bg-white p-3">
                <form method="GET" action="list.php" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold me-2">Lọc theo Tầng:</label>
                        <select name="floor" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="">Tất cả các tầng</option>
                            <option value="1" <?= $selectedFloor == '1' ? 'selected' : '' ?>>Tầng 1</option>
                            <option value="2" <?= $selectedFloor == '2' ? 'selected' : '' ?>>Tầng 2</option>
                            <option value="3" <?= $selectedFloor == '3' ? 'selected' : '' ?>>Tầng 3</option>
                            <option value="4" <?= $selectedFloor == '4' ? 'selected' : '' ?>>Tầng 4</option>
                            <option value="5" <?= $selectedFloor == '5' ? 'selected' : '' ?>>Tầng 5</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="fw-bold me-2">Trạng thái:</label>
                        <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="">Tất cả trạng thái</option>
                            <option value="Available" <?= $selectedStatus == 'Available' ? 'selected' : '' ?>>Phòng Trống (Xanh)</option>
                            <option value="Reserved" <?= $selectedStatus == 'Reserved' ? 'selected' : '' ?>>Đã Đặt (Vàng)</option>
                            <option value="Occupied" <?= $selectedStatus == 'Occupied' ? 'selected' : '' ?>>Đang Ở (Đỏ)</option>
                            <option value="Cleaning" <?= $selectedStatus == 'Cleaning' ? 'selected' : '' ?>>Đang Dọn (Xanh dương)</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm fw-bold">Lọc kết quả</button>
                        <a href="list.php" class="btn btn-light btn-sm">Xóa lọc</a>
                    </div>
                </form>
            </div>
            <a href="#" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Thêm Phòng Mới</a>
        </div>

        <div class="row g-4">
    <?php foreach ($rooms as $r): ?>
        <div class="col-md-3"> <div class="card shadow-sm h-100 border-0 <?= getStatusCardClass($r['status']) ?>">
                <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-door-open me-2"></i>P. <?= $r['room_number'] ?></h5>
                    <small class="fw-bold"><?= $r['status'] ?></small>
                </div>
                
                <div class="card-body">
                    <p class="mb-1">Tầng: <?= $r['floor'] ?></p>
                    <p class="mb-1">Loại: <strong><?= $r['type_name'] ?></strong></p>
                    <h5 class="mt-2 fw-bold"><?= number_format($r['price_per_night']) ?>đ/đêm</h5>
                </div>

                <div class="card-footer border-0 bg-transparent pb-3">
                    <div class="d-grid gap-2">
                        <?php if ($r['status'] == 'Cleaning'): ?>
                            <a href="list.php?action=update_cleaning&room_id=<?= $r['room_id'] ?>&status=Cleaning" 
                            class="btn btn-sm btn-warning fw-bold shadow-sm" 
                            onclick="return confirm('Xác nhận phòng này đã dọn xong?');">
                            <i class="bi bi-check-circle-fill"></i> Xác nhận dọn xong
                            </a>

                        <?php elseif ($r['status'] == 'Available'): ?>
                            <a href="../bookings/create.php?room_id=<?= $r['room_id'] ?>" 
                            class="btn btn-sm btn-light fw-bold shadow-sm text-success">
                            <i class="bi bi-plus-circle-fill"></i> ĐẶT PHÒNG NGAY
                            </a>
                            
                            <a href="list.php?action=update_cleaning&room_id=<?= $r['room_id'] ?>&status=Available" 
                            class="btn btn-sm btn-outline-light mt-1">
                            <i class="bi bi-broom"></i> Báo cần dọn dẹp
                            </a>

                        <?php else: ?>
                            <button class="btn btn-sm btn-dark opacity-50 fw-bold" disabled>
                                <i class="bi bi-slash-circle"></i> Không thể đặt
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div> </div> <?php endforeach; ?>
</div>
    </div>

</body>
</html>