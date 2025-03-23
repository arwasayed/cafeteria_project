<?php
require_once 'config.php';
require_once 'Database.php';

class OrderValidation {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    
    public function validateUser($u_id) {
        try {
            $stmt = $this->conn->prepare("SELECT u_id FROM User_Table WHERE u_id = :u_id");
            $stmt->execute([':u_id' => $u_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

   
    public function validateRoom($room_number) {
        if (empty($room_number)) {
            return true; 
        }

        try {
            $stmt = $this->conn->prepare("SELECT room_number FROM User_Rooms WHERE room_number = :room_number");
            $stmt->execute([':room_number' => $room_number]);
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    
    public function validateProducts($products) {
        if (empty($products)) {
            return false; 
        }

        foreach ($products as $product_id => $quantity) {
            if ($quantity > 0) {
                try {
                    $stmt = $this->conn->prepare("SELECT P_id FROM Products WHERE P_id = :product_id AND available = 'available'");
                    $stmt->execute([':product_id' => $product_id]);
                    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                        return false;
                    }
                } catch (PDOException $e) {
                    die("Database Error: " . $e->getMessage());
                }
            }
        }

        return true;
    }

   
    public function validateOrderData($u_id, $room_number, $products) {
        $errors = [];

        if (!$this->validateUser($u_id)) {
            $errors[] = "User does not exist.";
        }

        if (!$this->validateRoom($room_number)) {
            $errors[] = "Room does not exist.";
        }

        if (!$this->validateProducts($products)) {
            $errors[] = "Invalid products selected.";
        }

        return $errors;
    }
}
?>
