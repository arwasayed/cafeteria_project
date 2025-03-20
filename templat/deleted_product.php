<?php
session_start();
require_once 'config.php';
require_once 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


$config = new DatabaseConfig();

$db = new Database($config);
$conn = $db->getConnection();


if (!isset($_GET['P_id'])) {
    header("Location: Products.php");
    exit();
}
$p_id = intval($_GET['P_id']);


try {
    $conn->beginTransaction();


    $stmt = $conn->prepare("DELETE FROM Products WHERE P_id = :p_id");
    $stmt->execute([':p_id' => $p_id]);

    if ($conn->commit()) {

        header("Location: Products.php");
        exit();
    } else {
        $error = "Failed to delete user.";
    }
} catch (PDOException $e) {
    $conn->rollBack();
    $error = "Error deleting user: " . $e->getMessage();
}

if (isset($error)) {
    echo "<p class='error'>$error</p>";
}
?>