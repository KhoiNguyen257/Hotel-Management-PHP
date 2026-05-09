<?php
require_once __DIR__ . '/../includes/db.php';

class BookingService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function addService($bookingId, $serviceId, $quantity, $subtotal) {
        $sql = "INSERT INTO booking_services (booking_id, service_id, quantity, subtotal, added_at) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$bookingId, $serviceId, $quantity, $subtotal]);
    }

    public function getServicesByBooking($bookingId) {
        $sql = "SELECT bs.*, s.service_name 
                FROM booking_services bs 
                JOIN services s ON bs.service_id = s.service_id 
                WHERE bs.booking_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll();
    }
}
?>