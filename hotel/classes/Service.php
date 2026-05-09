<?php
require_once __DIR__ . '/../includes/db.php';

class Service {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllServices() {
        $stmt = $this->db->query("SELECT * FROM services WHERE is_available = 1");
        return $stmt->fetchAll();
    }
}
?>