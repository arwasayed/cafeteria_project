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


if (!isset($_GET['id'])) {
    header("Location: Users.php");
    exit();
}
$user_id = intval($_GET['id']);


try {
    $conn->beginTransaction();


    $stmt = $conn->prepare("DELETE FROM User_Table WHERE u_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);

    if ($conn->commit()) {

        header("Location: Users.php");
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