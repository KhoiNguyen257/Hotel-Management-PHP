<?php
// index.php
session_start();
require_once 'classes/User.php';

// Nếu người dùng đã đăng nhập rồi thì chuyển thẳng vào dashboard, không bắt đăng nhập lại
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $u = new User();
    $res = $u->login($_POST['username'], $_POST['password']);
    if ($res) {
        $_SESSION['user_id'] = $res['user_id'];
        $_SESSION['full_name'] = $res['full_name'];
        $_SESSION['role'] = $res['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMS Login - Quản lý Khách sạn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Tùy chỉnh background gradient sang trọng */
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            min-height: 100vh;
        }
        /* Tạo bo góc và đổ bóng cho form đăng nhập */
        .card-login {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-4">
                <div class="card card-login bg-white p-4">
                    <div class="card-body text-center">
                        
                        <div class="mb-4">
                            <i class="bi bi-building text-primary" style="font-size: 3rem;"></i>
                            <h2 class="fw-bold mt-2 text-dark">HMS Login</h2>
                            <p class="text-muted">Hệ thống quản lý khách sạn</p>
                        </div>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-floating mb-3 text-start">
                                <input type="text" name="username" class="form-control" id="floatingUsername" placeholder="Tên đăng nhập" required>
                                <label for="floatingUsername"><i class="bi bi-person me-2"></i>Tên đăng nhập</label>
                            </div>
                            <div class="form-floating mb-4 text-start">
                                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Mật khẩu" required>
                                <label for="floatingPassword"><i class="bi bi-lock me-2"></i>Mật khẩu</label>
                            </div>
                            <button name="login" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" type="submit">Đăng nhập</button>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>