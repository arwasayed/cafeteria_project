<?php
require_once 'config.php';
require_once 'database.php';
require_once 'UserValidation.php';
require_once 'Auth.php';

class BusinessLogic {
    private $db;
    private $userValidation;

    public function __construct() {
        $config = new DatabaseConfig();
        $this->db = new Database($config);
        $this->conn = $this->db->getConnection();
        $this->userValidation = new UserValidation($this->conn);
    }
    public function isUserExists($user_id) {
        return $this->userValidation->isUserExists($user_id);
    }

    public function getAllUsers() {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.u_id, u.name, u.email, u.image_path, u.role, ur.room_number AS room, ur.EXT
                FROM User_Table u
                LEFT JOIN User_Rooms ur ON u.u_id = ur.u_id
                WHERE u.role != 'admin'
                ORDER BY u.u_id DESC;
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    public function addUser($name, $email, $password, $room, $ext, $file) {
        
        $errors = $this->userValidation->validateUserData($name, $email, $room, $ext, $file, true, $password, $password);
        if (!empty($errors)) {
            return implode("<br>", $errors);
        }

        
        $upload_dir = './images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_path = $this->uploadImage($file, $upload_dir);
        if (!$file_path) {
            return "Failed to upload profile picture.";
        }

        
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

    public function getUserById($user_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.u_id, u.name, u.email, u.image_path, u.role, 
                       GROUP_CONCAT(ur.room_number SEPARATOR ', ') AS rooms, ur.EXT
                FROM User_Table u
                LEFT JOIN User_Rooms ur ON u.u_id = ur.u_id
                WHERE u.u_id = :user_id
                GROUP BY u.u_id, u.name, u.email, u.image_path, u.role, ur.EXT
            ");
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    public function updateUser($user_id, $name, $email, $room, $ext, $file = null, $current_image = null) {
        
        if (!$this->userValidation->isUserExists($user_id)) {
            return "User does not exist.";
        }

        
        $errors = $this->userValidation->validateUserData($name, $email, $room, $ext, $file);
        if (!empty($errors)) {
            return implode("<br>", $errors);
        }

        
        $file_path = $current_image;
        if ($file && $file['error'] == UPLOAD_ERR_OK) {
            $upload_dir = './images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_path = $this->uploadImage($file, $upload_dir);
            if (!$file_path) {
                return "Failed to upload profile picture.";
            }
        }

        
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

    private function uploadImage($file, $upload_dir) {
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid("user_", true) . "." . $file_extension;
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return $file_path;
        } else {
            return null;
        }
    }
}
?>
