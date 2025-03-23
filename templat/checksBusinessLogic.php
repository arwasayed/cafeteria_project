<?php
require_once 'config.php';
require_once 'database.php';


class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db->getConnection();
    }

    
    public function getOrders($dateFrom = '', $dateTo = '', $userSelect = '') {
        $query = "
            SELECT 
                o.O_id AS order_id,                      
                o.statuse, 
                o.note AS notes,                    
                o.date,                     
                u.name AS user_name,          
                o.room_number,  
                SUM(oc.amount * p.price) AS order_total,  
                GROUP_CONCAT(CONCAT(oc.amount, ' x ', p.name) SEPARATOR ', ') AS products  
            FROM Orders o
            JOIN User_Table u ON o.u_id = u.u_id  
            JOIN Order_Contents oc ON o.O_id = oc.o_id  
            JOIN Products p ON oc.P_id = p.P_id  
            WHERE 1=1
        ";

        $params = [];
        if (!empty($dateFrom)) {
            $query .= " AND o.date >= :dateFrom";
            $params[':dateFrom'] = $dateFrom . ' 00:00:00';
        }
        if (!empty($dateTo)) {
            $query .= " AND o.date <= :dateTo";
            $params[':dateTo'] = $dateTo . ' 23:59:59';
        }
        if (!empty($userSelect)) {
            $query .= " AND u.u_id = :u_id";
            $params[':u_id'] = $userSelect;
        }

        $query .= " GROUP BY o.O_id ORDER BY o.O_id DESC";

        try {
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => &$value) {
                $stmt->bindParam($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    
    public function updateOrderStatus($orderId, $status) {
        $query = "UPDATE Orders SET statuse = :status WHERE O_id = :orderId";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':orderId', $orderId);
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Error updating order status: " . $e->getMessage());
        }
    }
}

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db->getConnection();
    }

    public function getUsers() {
        try {
            $stmt = $this->conn->prepare("SELECT u_id, name FROM User_Table WHERE role != 'admin'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}

class Product {
    private $conn;

    public function __construct($db) {
        $this->conn = $db->getConnection();
    }

    public function getAvailableProducts() {
        try {
            $stmt = $this->conn->prepare("SELECT P_id, name, price FROM Products WHERE available = 'available'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>