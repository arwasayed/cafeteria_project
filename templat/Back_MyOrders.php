<?php
session_start();
require_once 'config.php';
require_once 'database.php';
require_once 'MyOrdersBusinessLogic.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$config = new DatabaseConfig();
$db = new Database($config);

$orderObj = new Order($db);
$userObj = new User($db);

$userId = $_SESSION['user_id'];
$user = $userObj->getUserById($userId);

if (!$user) {
    die("Error: User not found.");
}


$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';
$errors = [];


if (!empty($dateFrom) && !empty($dateTo) && $dateFrom > $dateTo) {
    $errors[] = "The 'Date from' cannot be later than 'Date to'.";
}


$orders = empty($errors) ? $orderObj->getOrders($userId, $dateFrom, $dateTo) : [];

?>