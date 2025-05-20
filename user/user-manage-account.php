<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="user-login.php";</script>';
    exit();
}

include_once 'user-navigation.php';
include_once '../db.php';
?>