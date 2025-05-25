<?php
session_start();
include_once '../db.php';
include_once 'audit-log.php';

// If the admin is logged in, log the logout
if (isset($_SESSION['admin_id'])) {
    log_audit($_SESSION['admin_id'], 'admin', 'logout', 'Admin logged out', 'admin');
}

// Clear only admin-specific session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_name']);

// Optionally destroy all sessions
session_destroy();

// Redirect fast
header("Location: admin-login.php");
exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f8f4f0;
        }
        .message {
            font-size: 20px;
            color: #333;
        }
        .redirect {
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>

    <h2 class="message">You have been logged out.</h2>
    <p class="redirect">Redirecting to login page...</p>

</body>
</html>
