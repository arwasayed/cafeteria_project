<?php
require_once 'checksBusinessLogic.php';
require_once 'Auth.php';

$config = new DatabaseConfig();
$db = new Database($config);

$order = new Order($db);
$user = new User($db);
$product = new Product($db);

$errors = [];
$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';
$userSelect = $_GET['userSelect'] ?? '';


if (!empty($dateFrom) && !empty($dateTo)) {
    if ($dateFrom > $dateTo) {
        $errors[] = "The 'Date from' cannot be later than 'Date to'.";
    }
}


$userIds = array_column($user->getUsers(), 'u_id'); 
if (!empty($userSelect) && !in_array($userSelect, $userIds)) {
    $errors[] = "Invalid user selected.";
}


$filteredOrders = empty($errors) ? $order->getOrders($dateFrom, $dateTo, $userSelect) : [];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $orderId = intval($_POST['order_id']);
    $action = $_POST['action'];

    
    $validOrderIds = array_column($order->getOrders(), 'order_id');
    if (!in_array($orderId, $validOrderIds)) {
        $errors[] = "Invalid order ID.";
    } else {
        
        if ($action === 'approve') {
            $order->updateOrderStatus($orderId, 'Approved');
        } elseif ($action === 'reject') {
            $order->updateOrderStatus($orderId, 'Rejected');
        }
        header("Location: index.php");
        exit();
    }
}


$users = $user->getUsers();
$products = $product->getAvailableProducts();