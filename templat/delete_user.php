<?php
require_once 'businesslogic.php';

$businessLogic = new BusinessLogic();

if (!isset($_GET['id'])) {
    header("Location: Users.php");
    exit();
}
$user_id = intval($_GET['id']);

if (!$businessLogic->isUserExists($user_id)) {
    die("User does not exist.");
}

$result = $businessLogic->deleteUser($user_id);
if ($result === true) {
    header("Location: Users.php");
    exit();
} else {
    $error = $result;
}

if (isset($error)) {
    echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
}
?>
