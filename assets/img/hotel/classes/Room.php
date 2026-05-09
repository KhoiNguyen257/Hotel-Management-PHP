<?php
// classes/Room.php
require_once __DIR__ . '/../includes/db.php';

class Room {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Lấy tất cả danh sách phòng để hiển thị sơ đồ 
    public function getAllRooms() {
        $stmt = $this->db->query("
            SELECT r.*, t.type_name, t.capacity 
            FROM rooms r 
            JOIN room_types t ON r.type_id = t.type_id
            ORDER BY r.floor ASC, r.room_number ASC
        ");
        return $stmt->fetchAll();
    }

    public function getFilteredRooms($floor = '', $status = '') {
        // Nối bảng rooms với room_types để lấy được tên loại phòng (Standard, VIP...)
        $sql = "SELECT r.*, t.type_name 
                FROM rooms r 
                JOIN room_types t ON r.type_id = t.type_id 
                WHERE 1=1"; // 1=1 là mẹo nhỏ để nối thêm AND phía sau dễ dàng hơn
        
        $params = [];

        // Nếu người dùng có chọn Tầng
        if ($floor != '') {
            $sql .= " AND r.floor = ?";
            $params[] = $floor;
        }

        // Nếu người dùng có chọn Trạng thái
        if ($status != '') {
            $sql .= " AND r.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY FIELD(r.status, 'Occupied', 'Cleaning', 'Reserved', 'Available') ASC, r.room_number ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function getAvailableRooms() {
        $stmt = $this->db->query("
            SELECT r.*, t.type_name 
            FROM rooms r 
            JOIN room_types t ON r.type_id = t.type_id 
            WHERE r.status = 'Available'
        ");
        return $stmt->fetchAll();
    }

    public function updateStatus($roomId, $status) {
        $sql = "UPDATE rooms SET status = :status WHERE room_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $roomId
        ]);
    }
}
?>