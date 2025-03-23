<?php
include 'config.php';
include 'database.php';

// Create database connection
$config = new DatabaseConfig();
$db = new Database($config->getHost(), $config->getUser(), $config->getPass(), $config->getDbName());
$conn = $db->getConnection();

// Fetch users with their total order amounts
$query = "SELECT users.id, users.name, SUM(orders.total_amount) AS total 
          FROM users 
          LEFT JOIN orders ON users.id = orders.user_id 
          GROUP BY users.id";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll();

// Fetch orders per user
$orderQuery = "SELECT orders.id, orders.user_id, orders.order_date, orders.total_amount 
               FROM orders 
               ORDER BY orders.order_date DESC";
$orderStmt = $conn->prepare($orderQuery);
$orderStmt->execute();
$orders = $orderStmt->fetchAll();

// Fetch order details (items per order)
$detailsQuery = "SELECT order_items.order_id, products.name, order_items.quantity, order_items.price 
                 FROM order_items 
                 JOIN products ON order_items.product_id = products.id";
$detailsStmt = $conn->prepare($detailsQuery);
$detailsStmt->execute();
$orderDetails = $detailsStmt->fetchAll();

// Organizing data for display
$userOrders = [];
foreach ($orders as $order) {
    $userOrders[$order['user_id']][] = $order;
}

$orderItems = [];
foreach ($orderDetails as $detail) {
    $orderItems[$detail['order_id']][] = $detail;
}

// Return data as JSON for AJAX (if requested)
if (isset($_GET['fetch'])) {
    echo json_encode([
        'users' => $users,
        'orders' => $userOrders,
        'orderItems' => $orderItems
    ]);
    exit;
}
