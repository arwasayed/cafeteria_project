<?php
session_start();
require_once 'config.php';
require_once 'database.php';


$dbConfig = new DatabaseConfig();
$db = new Database($dbConfig->getHost(), $dbConfig->getUser(), $dbConfig->getPass(), $dbConfig->getDbName());

$userId = $_SESSION['user_id'];

$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : null;
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : null;

$sql = "SELECT id, order_date, status, total_amount FROM orders WHERE user_id = ?";
$params = [$userId];

if ($fromDate && $toDate) {
    $sql .= " AND order_date BETWEEN ? AND ?";
    array_push($params, $fromDate, $toDate);
}

// Pagination
$limit = 10; // Orders per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$sql .= " ORDER BY order_date DESC LIMIT ? OFFSET ?";
array_push($params, $limit, $offset);

$stmt = $db->getConnection()->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];
    $sql = "SELECT product_name, quantity, price FROM order_items WHERE order_id = ?";
    $stmt = $db->getConnection()->prepare($sql);
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_order'])) {
    $orderId = $_POST['order_id'];
    
    // Check if order is still "Processing"
    $sql = "UPDATE orders SET status = 'Canceled' WHERE id = ? AND status = 'Processing'";
    $stmt = $db->getConnection()->prepare($sql);
    
    if ($stmt->execute([$orderId])) {
        echo "<script>alert('Order canceled successfully!'); window.location.reload();</script>";
    } else {
        echo "<script>alert('Failed to cancel order!');</script>";
    }
}

