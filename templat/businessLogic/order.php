<?php
require_once 'Database.php';
require_once 'OrderValidation.php';

class OrderManager {
    private $db;
    private $orderValidation;

    public function __construct($db) {
        $this->db = $db;
        $this->orderValidation = new OrderValidation($this->db->getPdo());
    }

    public function createOrder($u_id, $room_number, $products, $note = '') {
        $errors = $this->orderValidation->validateOrderData($u_id, $room_number, $products);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $noteValue = !empty($note) ? htmlspecialchars($note) : NULL;
            $roomNumberValue = !empty($room_number) ? $room_number : NULL;

            $this->db->setOperation('myinsert', [
                'tablename' => 'Orders',
                'columns' => ['u_id', 'statuse', 'note', 'room_number'],
                'values' => [$u_id, 'processing', $noteValue, $roomNumberValue]
            ]);

            if (!$this->db->executeQuery()) {
                throw new Exception("Failed to create order");
            }

            $order_id = $this->db->getPdo()->lastInsertId();

            foreach ($products as $product_id => $quantity) {
                if ($quantity > 0) {
                    $this->db->setOperation('myinsert', [
                        'tablename' => 'Order_Contents',
                        'columns' => ['o_id', 'P_id', 'amount'],
                        'values' => [$order_id, $product_id, $quantity]
                    ]);

                    if (!$this->db->executeQuery()) {
                        throw new Exception("Failed to add product $product_id to order");
                    }
                }
            }

            return ['success' => true, 'message' => "Order created successfully."];

        } catch (Exception $e) {
            return ['success' => false, 'errors' => ["Error: " . $e->getMessage()]];
        }
    }

    public function getLatestOrders($limit = 5) {
        try {
            $stmt = $this->db->getPdo()->prepare("SELECT * FROM orders_view ORDER BY order_date DESC LIMIT :limit");
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>