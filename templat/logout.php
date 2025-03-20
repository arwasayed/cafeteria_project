<?php
session_start();
require_once 'config.php';
require_once 'database.php';
$config = new DatabaseConfig();
$db = new Database($config);
$conn = $db->getConnection();

try {
    
    session_destroy();

    if ($conn) {
        $conn = null; 
    }

    header("Location: index.php"); 
    exit();
} catch (Exception $e) {
    die("Error during logout: " . $e->getMessage());
}
?>