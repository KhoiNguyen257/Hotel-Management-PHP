<?php
require_once __DIR__ . '/../includes/db.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user; // Trả về thông tin user nếu đúng
        }
        return false; // Trả về false nếu sai hoặc tài khoản bị khóa
    }

    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY role DESC");
        return $stmt->fetchAll();
    }

    public function create($username, $password, $role, $fullName, $email) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, password_hash, role, full_name, email, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, 1, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$username, $passwordHash, $role, $fullName, $email]);
    }

    public function updateUser($userId, $fullName, $email, $role) {
        $sql = "UPDATE users SET full_name = ?, email = ?, role = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fullName, $email, $role, $userId]);
    }

    public function toggleStatus($userId) {
        $sql = "UPDATE users SET is_active = NOT is_active WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
}
?>