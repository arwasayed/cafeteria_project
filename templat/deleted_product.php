<?php
session_start();
require_once 'config.php';
require_once 'database.php';
require_once 'business_logic.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$dbConfig = new DatabaseConfig();
$db = new Database($dbConfig);
$productObj  = new Product($db);


if (!isset($_GET['P_id'])) {
    header("Location: Products.php");
    exit();
}

$p_id = intval($_GET['P_id']);


if ($productObj ->delete($p_id)) {
    header("Location: Products.php?success=" . urlencode("Product deleted successfully!"));
    exit();
} else {
    header("Location: Products.php?error=" . urlencode("Failed to delete product!"));
    exit();
}
?>
