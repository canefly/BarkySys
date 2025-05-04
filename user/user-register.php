<?php
session_start();
include_once 'db.php'; // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $role = 'customer'; // Default role

    // Check if email already exists
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        echo '<script>alert("Email already registered. Please use another."); window.location.href="user-register.php";</script>';
        exit;
    }

    // Insert new user
    $query = "INSERT INTO users (username, password, full_name, contact) VALUES ('$username', '$password', '$full_name', '$contact')";
    if (mysqli_query($conn, $query)) {
        echo '<script>alert("Registration successful! You can now log in."); window.location.href="user-login.php";</script>';
        exit;
    } else {
        echo '<script>alert("Error registering user. Please try again.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bark & Wiggle – Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
        .navbar-brand,
        .nav-link {
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
        .logo {
            max-width: 180px;
            height: auto;
            display: block;
        }
        .register-container {
            max-width: 450px;
            margin: 30px auto;
            padding: 30px;
            background: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            color: #6E3387;
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
        .footer {
            text-align: center;
            padding: 15px 0;
            margin-top: 20px;
        }
        .footer p {
            color: #6E3387;
            margin: 0;
        }
        .form-text p {
            margin-top: 15px;
        }
        .form-text a {
            color: #6E3387;
            text-decoration: none;
        }
        .form-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">Bark & Wiggle</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="user-login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="hero">
        <div class="logo-wrapper">
            <img src="img/logo.png" alt="Bark & Wiggle Logo" class="logo">
        </div>
    </div>
    <section id="register" class="container">
        <div class="register-container">
            <h2 class="text-center mb-4">Register</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact Number</label>
                    <input type="tel" id="contact" name="contact" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="register">Register</button>
            </form>
            <div class="form-text text-center">
                <p>By clicking Register, you agree to our <a href="#">Terms &amp; Conditions</a> and <a href="#">Privacy Policy</a>.</p>
                <p>Already have an account? <a href="user-login.php">Login here</a></p>
            </div>
        </div>
    </section>
    <footer class="footer">
        <p>&copy; 2025 Bark & Wiggle. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
