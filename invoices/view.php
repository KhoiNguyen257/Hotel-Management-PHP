<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$db = Database::getInstance();
$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

// Truy vấn gộp 4 bảng để lấy toàn bộ thông tin hóa đơn
$sql = "SELECT i.*, b.check_in, b.check_out, c.full_name, c.phone, c.id_number, r.room_number 
        FROM invoices i
        JOIN bookings b ON i.booking_id = b.booking_id
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN rooms r ON b.room_id = r.room_id
        WHERE i.booking_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$bookingId]);
$invoice = $stmt->fetch();

if (!$invoice) {
    die("<div class='container mt-5 alert alert-danger'>Không tìm thấy hóa đơn cho mã đặt phòng này!</div>");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn #<?= $invoice['invoice_id'] ?> - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Ẩn các nút bấm khi in ra giấy */
        @media print {
            .no-print { display: none !important; }
            body { background-color: #fff; }
            .card { border: none; box-shadow: none !important; }
        }
        .invoice-title { letter-spacing: 2px; }
    </style>
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mb-3 text-end no-print">
                    <button onclick="window.print()" class="btn btn-success"><i class="bi bi-printer me-2"></i>In Hóa Đơn</button>
                    <a href="../bookings/list.php" class="btn btn-secondary">Về danh sách</a>
                </div>

                <div class="card shadow-lg p-5">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h2 class="fw-bold text-primary invoice-title">INVOICE</h2>
                            <p class="text-muted mb-0">Mã hóa đơn: <strong>#INV-<?= str_pad($invoice['invoice_id'], 5, '0', STR_PAD_LEFT) ?></strong></p>
                            <p class="text-muted">Ngày xuất: <?= date('d/m/Y H:i', strtotime($invoice['issued_at'])) ?></p>
                        </div>
                        <div class="col-sm-6 text-end">
                            <h4 class="fw-bold">Khách sạn Alex Hotel</h4>
                            <p class="mb-0">123 Đường Công Nghệ, TP.HCM</p>
                            <p class="mb-0">Email: contact@alexhotel.com</p>
                            <p>Hotline: 1900 9999</p>
                        </div>
                    </div>

                    <div class="row border-top border-bottom py-3 mb-4">
                        <div class="col-sm-6">
                            <h6 class="fw-bold text-secondary">THÔNG TIN KHÁCH HÀNG:</h6>
                            <p class="mb-0"><strong><?= htmlspecialchars($invoice['full_name']) ?></strong></p>
                            <p class="mb-0">CCCD: <?= htmlspecialchars($invoice['id_number']) ?></p>
                            <p class="mb-0">SĐT: <?= htmlspecialchars($invoice['phone']) ?></p>
                        </div>
                        <div class="col-sm-6 text-end">
                            <h6 class="fw-bold text-secondary">CHI TIẾT LƯU TRÚ:</h6>
                            <p class="mb-0">Phòng: <strong>P.<?= $invoice['room_number'] ?></strong></p>
                            <p class="mb-0">Check-in: <?= date('d/m/Y', strtotime($invoice['check_in'])) ?></p>
                            <p class="mb-0">Check-out: <?= date('d/m/Y', strtotime($invoice['check_out'])) ?></p>
                        </div>
                    </div>

                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Nội dung thanh toán</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tiền phòng (Room Charge)</td>
                                <td class="text-end"><?= number_format($invoice['subtotal_room']) ?>đ</td>
                            </tr>
                            <tr>
                                <td>Tiền dịch vụ (Services / Minibar)</td>
                                <td class="text-end"><?= number_format($invoice['subtotal_services']) ?>đ</td>
                            </tr>
                            <tr>
                                <td>Thuế VAT (<?= $invoice['tax_rate'] * 100 ?>%)</td>
                                <td class="text-end"><?= number_format($invoice['tax_amount']) ?>đ</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr>
                                <td class="fw-bold fs-5 text-end">TỔNG THANH TOÁN:</td>
                                <td class="fw-bold fs-4 text-danger text-end"><?= number_format($invoice['grand_total']) ?>đ</td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <p><strong>Phương thức TT:</strong> <?= $invoice['payment_method'] ?></p>
                            <p><strong>Trạng thái:</strong> <span class="badge bg-success">Đã thanh toán (Paid)</span></p>
                        </div>
                        <div class="col-sm-6 text-end">
                            <p class="fst-italic">Cảm ơn quý khách đã sử dụng dịch vụ!</p>
                            <br><br>
                            <p class="fw-bold">Chữ ký Lễ tân</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>