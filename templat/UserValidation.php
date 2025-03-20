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

    public function uploadImage($file, $upload_dir) {
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid("user_", true) . "." . $file_extension;
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return $file_path;
        } else {
            return null;
        }
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

    public function addUser($name, $email, $password, $room, $ext, $file_path) {
        try {
            $this->conn->beginTransaction();

            
            $stmt = $this->conn->prepare("
                INSERT INTO User_Table (name, email, password, image_path, role)
                VALUES (:name, :email, :password, :image_path, :role)
            ");
            $role = 'user';
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':image_path' => $file_path,
                ':role' => $role
            ]);
            $user_id = $this->conn->lastInsertId();

            
            $stmt = $this->conn->prepare("
                INSERT INTO User_Rooms (u_id, room_number, EXT)
                VALUES (:u_id, :room_number, :ext)
            ");
            $stmt->execute([
                ':u_id' => $user_id,
                ':room_number' => $room,
                ':ext' => $ext
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return "Error adding user: " . $e->getMessage();
        }
    }

    public function updateUser($user_id, $name, $email, $room, $ext, $file_path = null) {
        try {
            $this->conn->beginTransaction();

            
            $stmt = $this->conn->prepare("
                UPDATE User_Table 
                SET name = :name, email = :email, image_path = :image_path 
                WHERE u_id = :user_id
            ");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':image_path' => $file_path,
                ':user_id' => $user_id
            ]);

            
            $stmt = $this->conn->prepare("
                UPDATE User_Rooms 
                SET room_number = :room_number, EXT = :ext 
                WHERE u_id = :user_id
            ");
            $stmt->execute([
                ':room_number' => $room,
                ':ext' => $ext,
                ':user_id' => $user_id
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return "Error updating user: " . $e->getMessage();
        }
    }

    public function deleteUser($user_id) {
        try {
            $this->conn->beginTransaction();

            
            $stmt = $this->conn->prepare("DELETE FROM User_Rooms WHERE u_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);

            
            $stmt = $this->conn->prepare("DELETE FROM User_Table WHERE u_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return "Error deleting user: " . $e->getMessage();
        }
    }

    public function isUserExists($user_id) {
        $stmt = $this->conn->prepare("SELECT u_id FROM User_Table WHERE u_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchColumn() !== false;
    }
}
?>
