<?php
require_once 'config.php';
require_once 'database.php';
require 'vendor/autoload.php';
use SendGrid\Mail\Mail;

class PasswordLogic {
    private $db;

    public function __construct() {
        $this->db = new Database(new DatabaseConfig());
    }

    // Check if the email exists in the database
    public function checkEmailExists($email) {
        try {
            $stmt = $this->db->getConnection()->prepare("SELECT * FROM User_Table WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    // Save reset token and expiry time in the database
    public function saveResetToken($email, $token, $expiry) {
        try {
            $stmt = $this->db->getConnection()->prepare("UPDATE User_Table SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiry', $expiry);
            $stmt->bindParam(':email', $email);
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    // Send a password reset email using SendGrid
    public function sendResetEmail($toEmail, $toName, $resetLink) {
        $email = new Mail();
        $email->setFrom("arwasayed036@gmail.com", "arwa");
        $email->addTo($toEmail, $toName);
        $email->setSubject("Password Reset Request");
        $email->addContent("text/html", "Click the following link to reset your password: <a href='$resetLink'>$resetLink</a>");

        $sendgrid = new \SendGrid('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');
        try {
            $response = $sendgrid->send($email);
            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                return true;
            } else {
                throw new Exception("Failed to send email. Status Code: " . $response->statusCode() . " - " . $response->body());
            }
        } catch (Exception $e) {
            throw new Exception("Failed to send email. Error: " . $e->getMessage());
        }
    }
}
?>