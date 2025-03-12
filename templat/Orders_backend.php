<?php
require_once 'config.php';
require_once 'database.php';

// Initialize database connection
$config = new DatabaseConfig();
$db = new Database($config->getHost(), $config->getUser(), $config->getPass(), $config->getDbName());
$conn = $db->getConnection();

// Fetch users for the dropdown
$users = $db->selectAll('Users');

// Fetch products for ordering
$products = $db->selectAll('Products');

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $room = $_POST['room'];
    $notes = $_POST['notes'];
    $totalAmount = $_POST['total_amount'];
    $orderDate = date('Y-m-d H:i:s');

    // Insert new order into Orders table
    $db->insert('Orders', ['user_id', 'room_number', 'notes', 'order_total', 'statuse', 'date'], [$userId, $room, $notes, $totalAmount, 'Pending', $orderDate]);
    $orderId = $conn->lastInsertId();

    // Insert order items into Order_Items table
    foreach ($_POST['products'] as $productId => $quantity) {
        if ($quantity > 0) {
            // Get product price
            $productData = $db->select('Products', ['price'], 'P_id = ?', [$productId]);
            $price = $productData[0]['price'];
            $db->insert('Order_Items', ['order_id', 'product_id', 'quantity', 'price'], [$orderId, $productId, $quantity, $price]);
        }
    }
}

// Fetch latest orders
$latestOrders = $conn->query("SELECT o.order_id, o.statuse, o.notes, u.name AS user_name, o.room_number, o.order_total, o.date,
            GROUP_CONCAT(p.name SEPARATOR ', ') AS products
            FROM Orders o
            JOIN Users u ON o.user_id = u.u_id
            JOIN Order_Items oi ON o.order_id = oi.order_id
            JOIN Products p ON oi.product_id = p.P_id
            GROUP BY o.order_id ORDER BY o.date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
