<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = '127.0.0.1';
        $db   = 'hotel_management';
        $user = 'root';
        $pass = ''; // Mặc định của XAMPP là trống
        $dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        
        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Chống SQL Injection [cite: 29, 31]
            ]);
        } catch (PDOException $e) {
            die("Kết nối thất bại: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}