<?php
// users/manage.php
require_once '../includes/auth.php';
require_once '../classes/User.php';

// Bảo mật FR-10: Chỉ Manager mới được truy cập
if ($_SESSION['role'] !== 'manager') {
    header("Location: ../dashboard.php");
    exit();
}


// --- XỬ LÝ CÁC HÀNH ĐỘNG (POST & GET) ---

$userModel = new User();
$db = Database::getInstance();
$managerCount = $db->query("SELECT COUNT(*) FROM users WHERE role = 'manager'")->fetchColumn();

// 1. Xử lý Thêm nhân viên mới
if (isset($_POST['btnSaveUser'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role     = $_POST['role'];
    $fullName = $_POST['full_name'];
    $email    = $_POST['email'];

    // Ràng buộc: Tối đa 3 Manager
    if ($role == 'manager' && $managerCount >= 3) {
        echo "<script>alert('Lỗi: Hệ thống chỉ cho phép tối đa 3 Quản lý!'); window.location.href='manage.php';</script>";
        exit();
    }

    if ($userModel->create($username, $password, $role, $fullName, $email)) {
        echo "<script>alert('Thêm nhân viên thành công!'); window.location.href='manage.php';</script>";
        exit();
    }
}

// 2. Xử lý Sửa thông tin nhân viên
if (isset($_POST['btnUpdateUser'])) {
    $userId   = (int)$_POST['edit_user_id'];
    $fullName = $_POST['edit_full_name'];
    $email    = $_POST['edit_email'];
    $role     = $_POST['edit_role'];

    // Lấy role cũ để kiểm tra xem có phải đang "thăng cấp" không
    $stmt = $db->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $oldRole = $stmt->fetchColumn();

    // Ràng buộc: Không cho thăng cấp nếu đã đủ 3 Manager
    if ($role == 'manager' && $oldRole != 'manager' && $managerCount >= 3) {
        echo "<script>alert('Lỗi: Không thể thăng cấp. Hệ thống đã đạt giới hạn 3 Quản lý!'); window.location.href='manage.php';</script>";
        exit();
    }

    if ($userModel->updateUser($userId, $fullName, $email, $role)) {
        echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href='manage.php';</script>";
        exit();
    }
}
// 3. Xử lý Khóa / Mở khóa tài khoản qua tham số URL (GET)
if (isset($_GET['action']) && $_GET['action'] == 'toggle' && isset($_GET['id'])) {
    $toggleId = (int)$_GET['id'];
    // Không cho phép Quản lý tự khóa chính mình
    if ($toggleId !== $_SESSION['user_id']) {
        $userModel->toggleStatus($toggleId);
    }
    header("Location: manage.php");
    exit();
}

// Lấy danh sách hiển thị
$allUsers = $userModel->getAllUsers();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Nhân sự - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="../dashboard.php"><i class="bi bi-building me-2"></i>HMS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="../dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../rooms/list.php">Quản lý Phòng</a></li>
                    <li class="nav-item"><a class="nav-link" href="../bookings/list.php">Danh sách Đặt phòng</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage.php">Nhân sự</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Quản lý Nhân sự</h2>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus-fill me-1"></i> Thêm Nhân Viên
            </button>
        </div>

        <div class="card shadow border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Họ và Tên</th>
                            <th>Tên đăng nhập</th>
                            <th>Vai trò</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($allUsers as $u): ?>
                        <tr>
                            <td class="ps-4"><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td>
                                <span class="badge <?= $u['role'] == 'manager' ? 'bg-danger' : 'bg-info text-dark' ?>">
                                    <?= strtoupper($u['role']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <?= $u['is_active'] 
                                    ? '<span class="text-success small fw-bold"><i class="bi bi-circle-fill me-1"></i>Hoạt động</span>' 
                                    : '<span class="text-muted small"><i class="bi bi-circle me-1"></i>Đã khóa</span>' ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary me-1" 
                                        onclick="openEditModal(<?= $u['user_id'] ?>, '<?= addslashes($u['full_name']) ?>', '<?= addslashes($u['email']) ?>', '<?= $u['role'] ?>')">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <?php if($u['user_id'] != $_SESSION['user_id']): ?>
                                    <a href="manage.php?action=toggle&id=<?= $u['user_id'] ?>" 
                                       class="btn btn-sm <?= $u['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                       onclick="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái tài khoản này?');">
                                        <i class="bi <?= $u['is_active'] ? 'bi-lock-fill' : 'bi-unlock-fill' ?>"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-light text-muted" disabled><i class="bi bi-shield-lock-fill"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form method="POST">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tạo Tài Khoản Nhân Viên</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label fw-bold">Tên đăng nhập</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col">
                                <label class="form-label fw-bold">Mật khẩu</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Vai trò</label>
                            <select name="role" class="form-select">
                                <option value="receptionist">Receptionist (Lễ tân)</option>
                                <option value="manager">Manager (Quản lý)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="btnSaveUser" class="btn btn-primary">Lưu nhân viên</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form method="POST">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold">Sửa Thông Tin Nhân Viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="edit_user_id" id="edit_id">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên</label>
                            <input type="text" name="edit_full_name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="edit_email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Vai trò</label>
                            <select name="edit_role" id="edit_role" class="form-select">
                                <option value="receptionist">Receptionist (Lễ tân)</option>
                                <option value="manager">Manager (Quản lý)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="btnUpdateUser" class="btn btn-warning fw-bold">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script đẩy dữ liệu từ bảng lên Modal Sửa
        function openEditModal(id, name, email, role) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            
            var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
        }
    </script>
</body>
</html>