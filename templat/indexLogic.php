<?php
require_once 'config.php';
require_once 'database.php';

class AuthLogic {
    private $db;

    public function __construct() {
        $this->db = new Database(new DatabaseConfig());
    }

    public function authenticateUser($email, $password) {
        try {
            $stmt = $this->db->getConnection()->prepare("SELECT * FROM User_Table WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>