<?php
require_once 'config.php';
require_once 'database.php';

class Order {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function getOrders($userId, $dateFrom = '', $dateTo = '') {
        $query = "
            SELECT 
                o.O_id AS order_id,
                o.date AS order_date,
                o.statuse AS status,
                SUM(oc.amount * p.price) AS total_price,
                GROUP_CONCAT(DISTINCT CONCAT(oc.amount, ' x ', p.name) SEPARATOR ', ') AS products
            FROM Orders o
            JOIN Order_Contents oc ON o.O_id = oc.o_id  
            JOIN Products p ON oc.P_id = p.P_id  
            WHERE o.u_id = :u_id
        ";

        $params = [':u_id' => $userId];

        if (!empty($dateFrom)) {
            $query .= " AND o.date >= :dateFrom";
            $params[':dateFrom'] = $dateFrom . ' 00:00:00';
        }
        if (!empty($dateTo)) {
            $query .= " AND o.date <= :dateTo";
            $params[':dateTo'] = $dateTo . ' 23:59:59';
        }

        $query .= " GROUP BY o.O_id ORDER BY o.date DESC";

        try {
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => &$value) {
                $stmt->bindParam($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Database Error: " . $e->getMessage()];
        }
    }
}

class User {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function getUserById($userId) {
        try {
            $stmt = $this->db->prepare("SELECT name FROM User_Table WHERE u_id = :u_id");
            $stmt->execute([':u_id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Database Error: " . $e->getMessage()];
        }
    }
}
?>
