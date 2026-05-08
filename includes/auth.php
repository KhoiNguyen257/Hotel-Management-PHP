<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu chưa có session user_id thì đá về trang đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: /hotel/login.php");
    exit();
}
?>