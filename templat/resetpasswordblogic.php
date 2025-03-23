<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'database.php';

class ResetPasswordLogic {
    private $db;

    public function __construct() {
        $this->db = new Database(new DatabaseConfig());
    }

    public function validateResetToken($token) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM User_Table WHERE reset_token = :token AND reset_token_expiry > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function resetPassword($token, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->getConnection()->prepare("UPDATE User_Table SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = :token");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }
}
?>