<?php
// Clean and secure admin auth check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If not logged in, kill session and redirect
if (!isset($_SESSION['admin_id'])) {
    session_unset();              // clear session vars
    session_destroy();            // end the session
    header("Location: admin-login.php");
    exit();
}
?>
