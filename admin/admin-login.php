<?php
session_start();
include_once '../db.php';
include_once '../helpers/audit-log.php'; // Optional: audit logging

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_name'] = $admin['full_name'];

        // Optional: record login
        log_audit($conn, $admin['id'], 'login', 'Admin logged in', 'admin');

        echo '<script>alert("Login successful!"); window.location.href="admin-hp.php";</script>';
    } else {
        echo '<script>alert("Invalid credentials. Please try again.");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bark & Wiggle – Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FFFFFF;
            color: #000000;
        }
        .navbar {
            background-color: #6E3387 !important;
            margin-bottom: 20px;
        }
        .navbar-brand, .nav-link {
            color: #FFFFFF !important;
        }
        .nav-link:hover {
            color: #D6BE3E !important;
        }
        .hero {
            text-align: center;
            background-color: #6E3387;
            padding: 30px 0;
        }
        .hero .logo-wrapper {
            display: inline-block;
            background-color: #FFFFFF;
            padding: 15px;
            border-radius: 12px;
        }
        .hero img {
            max-width: 180px;
            height: auto;
            display: block;
        }
        .login-container {
            max-width: 400px;
            margin: 30px auto;
            padding: 30px;
            background: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            color: #6E3387;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #D6BE3E !important;
            border-color: #D6BE3E !important;
            color: #000000 !important;
        }
        .btn-primary:hover {
            background-color: #c5a634 !important;
            border-color: #c5a634 !important;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-link a {
            color: #6E3387;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .footer {
            text-align: center;
            padding: 15px 0;
            margin-top: 20px;
        }
        .footer p {
            color: #6E3387;
            margin: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Bark & Wiggle Admin</a>
        </div>
    </nav>
    <div class="hero">
        <div class="logo-wrapper">
            <img src="img/logo.png" alt="Admin Logo">
        </div>
    </div>
    <section class="container" style="max-width: 500px;">
        <div class="login-container">
            <h2 class="text-center">Admin Login</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
            </form>
            <div class="register-link">
                <p>Don't have an account? <a href="admin-register.php">Register</a></p>
            </div>
        </div>
    </section>
    <footer class="footer">
        <p>&copy; 2025 Bark & Wiggle. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
