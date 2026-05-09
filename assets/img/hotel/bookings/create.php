<?php
require_once '../includes/auth.php';
require_once '../classes/Room.php';
require_once '../classes/Booking.php';
require_once '../classes/Customer.php';

$roomModel = new Room();
$bookingModel = new Booking();
$customerModel = new Customer();

$msg = "";
$today = date('Y-m-d');
$selectedRoomId = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

if (isset($_POST['btnBooking'])) {
    // 1. Thu thập thông tin khách hàng 
    $fullName = $_POST['full_name'];
    $idNumber = $_POST['id_number'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $nationality = $_POST['nationality'];
    
    // 2. Thu thập thông tin phòng
    $roomId = $_POST['room_id'];
    $checkIn = $_POST['check_in'];
    $checkOut = $_POST['check_out'];

    if ($checkOut <= $checkIn) {
        $msg = "<div class='alert alert-danger'>Ngày đi phải sau ngày đến!</div>";
    } else {
        // XỬ LÝ KHÁCH HÀNG: Kiểm tra xem CCCD này đã có trong máy chưa
        $existingCustomer = $customerModel->getByIdNumber($idNumber);
        if ($existingCustomer) {
            // Nếu CCCD đã tồn tại, BẮT BUỘC phải khớp đúng Họ tên và Số điện thoại
            if ($existingCustomer['full_name'] == $fullName && $existingCustomer['phone'] == $phone) {

                $customerId = $existingCustomer['customer_id'];
                
            } else {

                $msg = "<div class='alert alert-danger'>Lỗi: Số CCCD này đã được đăng ký! Nếu là khách cũ, vui lòng nhập đúng Họ Tên và SĐT đã lưu.</div>";
                $customerId = false;
                
            }
        } else {
            // Nếu chưa từng có CCCD này -> Khách mới toanh, tiến hành tạo mới
            $customerId = $customerModel->create($fullName, $idNumber, $phone, $email, $nationality);
        }

        if ($customerId) {
            // Tính toán giá tiền 
            $db = Database::getInstance();
            $stmtPrice = $db->prepare("SELECT price_per_night FROM rooms WHERE room_id = ?");
            $stmtPrice->execute([$roomId]);
            $price = $stmtPrice->fetchColumn();
            
            $nights = $bookingModel->calculateNights($checkIn, $checkOut);
            $totalPrice = $nights * $price;

            // 3. Tạo đơn đặt phòng
            if ($bookingModel->create($customerId, $roomId, $_SESSION['user_id'], $checkIn, $checkOut, $totalPrice)) {
                $roomModel->updateStatus($roomId, 'Reserved');
                $msg = "<div class='alert alert-success'>Thành công! Khách: $fullName. Tổng: ".number_format($totalPrice)."đ. Đang chuyển trang...</div>";
                header("refresh:2; url=../rooms/list.php");
            }
        }
    }
}
$availableRooms = $roomModel->getAvailableRooms();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Đặt Phòng - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white p-3">
                        <h4 class="mb-0 fw-bold">Phiếu Đặt Phòng & Thông Tin Khách</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php echo $msg; ?>
                        <form method="POST">
                            <h5 class="text-primary mb-3 border-bottom pb-2">1. Thông tin định danh khách hàng</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Họ tên khách hàng</label>
                                    <input type="text" name="full_name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Số CCCD / Passport</label>
                                    <input type="text" name="id_number" class="form-control" placeholder="Nhập số định danh" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" name="phone" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Quốc tịch</label>
                                    <input type="text" name="nationality" class="form-control" value="Việt Nam">
                                </div>
                            </div>

                            <h5 class="text-primary mb-3 border-bottom pb-2 mt-4">2. Chi tiết đặt phòng</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ngày đến</label>
                                    <input type="date" name="check_in" id="check_in" class="form-control" min="<?= $today ?>" value="<?= $today ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ngày đi</label>
                                    <input type="date" name="check_out" id="check_out" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Phòng chọn đặt</label>
                                <select name="room_id" class="form-select" required>
                                    <?php foreach($availableRooms as $r): ?>
                                        <option value="<?= $r['room_id'] ?>" <?= ($r['room_id'] == $selectedRoomId) ? 'selected' : '' ?>>
                                            Phòng <?= $r['room_number'] ?> - <?= $r['type_name'] ?> (<?= number_format($r['price_per_night']) ?>đ/đêm)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="btnBooking" class="btn btn-primary btn-lg shadow">Xác nhận Lưu & Đặt phòng</button>
                                <a href="../rooms/list.php" class="btn btn-link text-decoration-none text-muted">Hủy bỏ và quay lại</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const cin = document.getElementById('check_in');
        const cout = document.getElementById('check_out');
        cin.addEventListener('change', function() {
            let d = new Date(this.value); d.setDate(d.getDate() + 1);
            cout.min = d.toISOString().split('T')[0];
            if(cout.value <= this.value) cout.value = cout.min;
        });
    </script>
</body>
</html>