<?php
session_start(); // Start the session at the very beginning of the file

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Login.css"> </head>
<body>
<header>';
// session_start();

// if (!isset($_SESSION['username'])) {
//     header("Location: .login.php");
//     exit();
// }

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $name = $_POST['name'];
//     $email = $_POST['email'];
//     $password = $_POST['password'];
//     $confirm_password = $_POST['confirm_password'];
//     $numRome = $_POST['numRome'];
//     $ext = $_POST['ext'];

//     if ($password != $confirm_password) {
//         echo "Passwords do not match.";
//     } else {
//         // Upload image
//         $target_dir = "uploads/";
//         $target_file = $target_dir . basename($_FILES["img"]["name"]);
//         move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);

//         echo "User added successfully! Welcome, " . $_SESSION['username'];
//     }
// }
?>
