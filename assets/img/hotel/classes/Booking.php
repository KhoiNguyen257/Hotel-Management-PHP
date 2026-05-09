<?php
require_once __DIR__ . '/../includes/db.php';

class Booking {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function calculateNights($checkIn, $checkOut) {
        $start = new DateTime($checkIn);
        $end = new DateTime($checkOut);
        $interval = $start->diff($end);
        return $interval->days > 0 ? $interval->days : 1;
    }

    public function create($customerId, $roomId, $userId, $checkIn, $checkOut, $totalPrice) {
        $sql = "INSERT INTO bookings (customer_id, room_id, user_id, check_in, check_out, status, total_price) 
                VALUES (?, ?, ?, ?, ?, 'Reserved', ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$customerId, $roomId, $userId, $checkIn, $checkOut, $totalPrice]);
    }

    public function getAllBookings() {
        $sql = "SELECT b.*, c.full_name, r.room_number 
                FROM bookings b 
                JOIN customers c ON b.customer_id = c.customer_id 
                JOIN rooms r ON b.room_id = r.room_id 
                ORDER BY b.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Xử lý nghiệp vụ Check-in (UC-02)
    public function checkIn($bookingId, $roomId) {
        $this->db->beginTransaction();
        try {
            // 1. Cập nhật Booking
            $stmt1 = $this->db->prepare("UPDATE bookings SET status = 'CheckedIn' WHERE booking_id = ?");
            $stmt1->execute([$bookingId]);
            
            // 2. Cập nhật Phòng
            $stmt2 = $this->db->prepare("UPDATE rooms SET status = 'Occupied' WHERE room_id = ?");
            $stmt2->execute([$roomId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    public function checkOut($bookingId, $roomId, $paymentMethod, $userId) {
        $this->db->beginTransaction();
        try {
            // 1. Lấy thông tin tiền phòng
            $stmt = $this->db->prepare("SELECT total_price FROM bookings WHERE booking_id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            $subtotalRoom = $booking['total_price'];
            
            // 2. Lấy TỔNG TIỀN DỊCH VỤ khách đã gọi (Sửa chỗ này)
            $stmtSvc = $this->db->prepare("SELECT SUM(subtotal) FROM booking_services WHERE booking_id = ?");
            $stmtSvc->execute([$bookingId]);
            $subtotalServices = $stmtSvc->fetchColumn(); 
            if (!$subtotalServices) $subtotalServices = 0; // Nếu không gọi dịch vụ thì bằng 0
            
            // 3. Tính toán Thuế và Tổng tiền
            $taxRate = 0.10;
            $taxAmount = ($subtotalRoom + $subtotalServices) * $taxRate;
            $grandTotal = $subtotalRoom + $subtotalServices + $taxAmount;

            // 4. Tạo Hóa đơn (Invoices table)
            $sqlInvoice = "INSERT INTO invoices (booking_id, subtotal_room, subtotal_services, tax_rate, tax_amount, grand_total, payment_method, payment_status, issued_at, issued_by) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'Paid', NOW(), ?)";
            $stmtInvoice = $this->db->prepare($sqlInvoice);
            $stmtInvoice->execute([$bookingId, $subtotalRoom, $subtotalServices, $taxRate, $taxAmount, $grandTotal, $paymentMethod, $userId]);

            // 5. Cập nhật trạng thái Booking -> CheckedOut
            $stmtUpdateB = $this->db->prepare("UPDATE bookings SET status = 'CheckedOut' WHERE booking_id = ?");
            $stmtUpdateB->execute([$bookingId]);

            // 6. Cập nhật trạng thái Phòng -> Available
            $stmtUpdateR = $this->db->prepare("UPDATE rooms SET status = 'Cleaning' WHERE room_id = ?");
            $stmtUpdateR->execute([$roomId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}