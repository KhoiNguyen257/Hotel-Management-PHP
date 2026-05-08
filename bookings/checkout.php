<?php
require_once '../includes/auth.php';
require_once '../classes/Booking.php';
require_once '../classes/BookingService.php'; // Load class dịch vụ

$db = Database::getInstance();
$bookingId = $_GET['id'] ?? 0;
$roomId = $_GET['room_id'] ?? 0;

// Lấy chi tiết đơn đặt phòng
$stmt = $db->prepare("SELECT b.*, c.full_name, r.room_number FROM bookings b JOIN customers c ON b.customer_id = c.customer_id JOIN rooms r ON b.room_id = r.room_id WHERE b.booking_id = ?");
$stmt->execute([$bookingId]);
$b = $stmt->fetch();

// Lấy danh sách dịch vụ đã dùng
$bsModel = new BookingService();
$usedServices = $bsModel->getServicesByBooking($bookingId);

// Tính toán các loại tiền hiển thị
$subtotalRoom = $b['total_price'];
$subtotalServices = 0;
foreach($usedServices as $s) {
    $subtotalServices += $s['subtotal'];
}

$tax = ($subtotalRoom + $subtotalServices) * 0.10; // Thuế VAT 10% tổng
$grandTotal = $subtotalRoom + $subtotalServices + $tax;

$bankId = "VietinBank"; 
$accountNo = "103625072006"; 
$accountName = "VO KHOI NGUYEN"; 
$content = "Thanh toan don phong " . $bookingId; 

$qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$grandTotal}&addInfo=" . urlencode($content) . "&accountName=" . urlencode($accountName);

if (isset($_POST['btnConfirmCheckout'])) {
    $paymentMethod = $_POST['payment_method'];
    $userId = $_SESSION['user_id'];
    
    $bookingModel = new Booking();
    if ($bookingModel->checkOut($bookingId, $roomId, $paymentMethod, $userId)) {
        echo "<script>alert('Thanh toán thành công! Khách đã trả phòng.'); window.location.href='list.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán Check-out</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div id="qrCodeContainer" class="container mt-3 text-center">
        <div class="card shadow-sm border-primary d-inline-block p-3">
            <h6 class="fw-bold text-primary">Quét mã VietQR thanh toán</h6>
            <img src="<?= $qrUrl ?>" class="img-fluid border" style="max-width: 200px;">
            <p class="small text-muted mb-0 mt-2">Nội dung: <?= $content ?></p>
        </div>
    </div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">Hóa Đơn Thanh Toán (Phòng <?= $b['room_number'] ?>)</h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-1"><strong>Khách hàng:</strong> <?= $b['full_name'] ?></p>
                        <p class="mb-3"><strong>Lưu trú:</strong> <?= date('d/m/Y', strtotime($b['check_in'])) ?> đến <?= date('d/m/Y', strtotime($b['check_out'])) ?></p>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <tr class="border-bottom border-dark">
                                    <td class="fw-bold">Tiền phòng:</td>
                                    <td class="text-end fw-bold"><?= number_format($subtotalRoom) ?>đ</td>
                                </tr>
                                
                                <?php if(count($usedServices) > 0): ?>
                                    <tr>
                                        <td colspan="2" class="fw-bold text-info pt-3">Dịch vụ sử dụng:</td>
                                    </tr>
                                    <?php foreach($usedServices as $s): ?>
                                    <tr>
                                        <td class="ps-4 fst-italic">- <?= htmlspecialchars($s['service_name']) ?> (SL: <?= $s['quantity'] ?>)</td>
                                        <td class="text-end"><?= number_format($s['subtotal']) ?>đ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <tr class="border-top mt-3">
                                    <td class="pt-3">Thuế VAT (10%):</td>
                                    <td class="text-end pt-3"><?= number_format($tax) ?>đ</td>
                                </tr>
                                <tr class="fw-bold fs-5 bg-light">
                                    <td class="py-3">TỔNG THANH TOÁN:</td>
                                    <td class="text-end text-danger py-3"><?= number_format($grandTotal) ?>đ</td>
                                </tr>
                            </table>
                        </div>

                        <form method="POST" class="mt-4 border-top pt-3">
                            <div class="mb-3 row align-items-center">
                                <label class="col-sm-4 col-form-label fw-bold">Phương thức thanh toán:</label>
                                    <div class="col-sm-8">
                                        <select name="payment_method" id="paymentMethodSelect" class="form-select" onchange="toggleQRCode()">
                                            <option value="Cash">Tiền mặt</option>
                                            <option value="Transfer">Chuyển khoản ngân hàng</option>
                                        </select>
                                    </div>
                            </div>
                            <button type="submit" name="btnConfirmCheckout" class="btn btn-danger btn-lg w-100 shadow-sm">
                                Xác nhận Thanh toán & Trả phòng
                            </button>
                        </form>
                        <a href="list.php" class="btn btn-link mt-2 d-block text-center text-decoration-none">Quay lại danh sách</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleQRCode() {
            var paymentMethod = document.getElementById("paymentMethodSelect").value;
            var qrContainer = document.getElementById("qrCodeContainer");

            if (paymentMethod === "Transfer") {
                qrContainer.classList.remove("d-none");
            } else {
                qrContainer.classList.add("d-none");
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            toggleQRCode();
        });
    </script>
</body>
</html>
</body>
</html>