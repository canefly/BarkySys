<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="admin-login.php";</script>';
    exit();
}
?>
