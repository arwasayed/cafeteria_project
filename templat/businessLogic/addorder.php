<?php
require_once 'Database.php';
require_once 'OrderValidation.php';

class OrderLogic {
    private $conn;
    private $loggedInUserId;
    private $orderValidation;

    public function __construct($userId) {
        $config = new DatabaseConfig();
        $db = new Databases($config);
        $this->conn = $db->getConnection();
        $this->loggedInUserId = intval($userId);
        $this->orderValidation = new OrderValidation($this->conn);
    }

    public function getProducts() {
        try {
            $stmt = $this->conn->prepare("SELECT P_id, name, price FROM Products WHERE available = 'available'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    public function getLatestOrders() {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    o.O_id AS order_id, o.statuse AS status, o.note AS notes, o.date AS date, 
                    u.name AS user_name, o.room_number, 
                    SUM(oc.amount * p.price) AS order_total, 
                    GROUP_CONCAT(CONCAT(oc.amount, ' x ', p.name) SEPARATOR ', ') AS products  
                FROM Orders o
                JOIN User_Table u ON o.u_id = u.u_id  
                JOIN Order_Contents oc ON o.O_id = oc.o_id  
                JOIN Products p ON oc.P_id = p.P_id  
                WHERE o.u_id = :u_id
                GROUP BY o.O_id, o.room_number  
                ORDER BY o.O_id DESC
                LIMIT 5
            ");
            $stmt->execute([':u_id' => $this->loggedInUserId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    public function createOrder($note, $room_number, $products) {
        $errors = $this->orderValidation->validateOrderData($this->loggedInUserId, $room_number, $products);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $statuse = 'processing';
            $stmt = $this->conn->prepare("
                INSERT INTO Orders (u_id, statuse, note, room_number)
                VALUES (:u_id, :statuse, :note, :room_number)
            ");
            $stmt->execute([
                ':u_id' => $this->loggedInUserId,
                ':statuse' => $statuse,
                ':note' => htmlspecialchars(trim($note)),
                ':room_number' => !empty($room_number) ? $room_number : NULL
            ]);
            $order_id = $this->conn->lastInsertId();

            foreach ($products as $product_id => $quantity) {
                if ($quantity > 0) {
                    $stmt = $this->conn->prepare("
                        INSERT INTO Order_Contents (o_id, P_id, amount)
                        VALUES (:o_id, :P_id, :amount)
                    ");
                    $stmt->execute([
                        ':o_id' => $order_id,
                        ':P_id' => $product_id,
                        ':amount' => $quantity
                    ]);
                }
            }

            return ['success' => true, 'order_id' => $order_id];
        } catch (PDOException $e) {
            die("Error saving order: " . $e->getMessage());
        }
    }

    public function getRooms() {
        try {
            $stmt = $this->conn->prepare("SELECT DISTINCT room_number FROM User_Rooms");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>