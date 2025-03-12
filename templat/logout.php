<?php
require_once 'config.php';
require_once 'header.php';
session_destroy();
header("Location: login.php");
exit();
?>