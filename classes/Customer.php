<?php
require_once __DIR__ . '/../includes/db.php';

class Customer {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByIdNumber($idNumber) {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id_number = ?");
        $stmt->execute([$idNumber]);
        return $stmt->fetch();
    }
    // Hàm tạo khách hàng mới (Dùng cho FR-02)
    public function create($fullName, $idNumber, $phone, $email, $nationality) {
        $sql = "INSERT INTO customers (full_name, id_number, phone, email, nationality) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fullName, $idNumber, $phone, $email, $nationality]);
    }

    // Lấy ID của khách hàng vừa tạo để dùng cho Booking
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
}
?>