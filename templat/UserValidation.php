<?php
require_once 'config.php';
require_once 'database.php';

class UserValidation {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function validateNotEmpty($value, $fieldName) {
        if (empty($value)) {
            return "$fieldName is required!";
        }
        return null;
    }

    public function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format!";
        }
        return null;
    }

    public function validatePasswordMatch($password, $confirm_password) {
        if ($password !== $confirm_password) {
            return "Passwords do not match!";
        }
        return null;
    }

    public function validatePasswordStrength($password) {
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
            return "Password must be at least 8 characters long, including numbers, uppercase, and lowercase letters.";
        }
        return null;
    }

    public function validateImage($file, $allowed_extensions, $max_size) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "File upload error!";
        }

        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_extensions)) {
            return "Invalid file type! Only " . implode(", ", $allowed_extensions) . " are allowed.";
        }

        if ($file['size'] > $max_size) {
            return "File size must be less than " . ($max_size / 1024 / 1024) . "MB!";
        }

        return null;
    }

    public function validateUserData($name, $email, $room, $ext, $file = null, $checkPassword = false, $password = null, $confirm_password = null) {
        $errors = [];

        $errors[] = $this->validateNotEmpty($name, "Name");
        $errors[] = $this->validateNotEmpty($email, "Email");
        $errors[] = $this->validateEmail($email);
        $errors[] = $this->validateNotEmpty($room, "Room number");
        $errors[] = $this->validateNotEmpty($ext, "Ext");

        if ($checkPassword) {
            $errors[] = $this->validateNotEmpty($password, "Password");
            $errors[] = $this->validatePasswordStrength($password);
            $errors[] = $this->validatePasswordMatch($password, $confirm_password);
        }

        if ($file && $file['error'] == UPLOAD_ERR_OK) {
            $errors[] = $this->validateImage($file, ['jpg', 'jpeg', 'png', 'gif'], 2 * 1024 * 1024);
        }

        return array_filter($errors);
    }

    public function isUserExists($user_id) {
        $stmt = $this->conn->prepare("SELECT u_id FROM User_Table WHERE u_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchColumn() !== false;
    }
}
?>
