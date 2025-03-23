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
                SELECT u.u_id, u.name, u.email, u.image_path, u.role, 
                       GROUP_CONCAT(ur.room_number SEPARATOR ', ') AS rooms, ur.EXT
                FROM User_Table u
                LEFT JOIN User_Rooms ur ON u.u_id = ur.u_id
                WHERE u.role != 'admin'  
                GROUP BY u.u_id, u.name, u.email, u.image_path, u.role, ur.EXT
                ORDER BY u.u_id DESC
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

        $file_path = $this->userValidation->uploadImage($file, $upload_dir);
        if (!$file_path) {
            return "Failed to upload profile picture.";
        }

        return $this->userValidation->addUser($name, $email, $password, $room, $ext, $file_path);
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

    public function updateUser($user_id, $name, $email, $room, $ext, $file = null) {
        if (!$this->isUserExists($user_id)) {
            return "User does not exist.";
        }

        $errors = $this->userValidation->validateUserData($name, $email, $room, $ext, $file);
        if (!empty($errors)) {
            return implode("<br>", $errors);
        }

        $file_path = null;
        if ($file && $file['error'] == UPLOAD_ERR_OK) {
            $upload_dir = './images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_path = $this->userValidation->uploadImage($file, $upload_dir);
            if (!$file_path) {
                return "Failed to upload profile picture.";
            }
        }

        return $this->userValidation->updateUser($user_id, $name, $email, $room, $ext, $file_path);
    }

    public function deleteUser($user_id) {
        return $this->userValidation->deleteUser($user_id);
    }
}
?>
