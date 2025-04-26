<?php
session_start();
include('db.php');

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $contact  = mysqli_real_escape_string($conn, $_POST['contact']);

    // Check if username or contact exists
    $ret = mysqli_query($conn,
        "SELECT username FROM admin WHERE username='$username' OR contact_number='$contact'");
    $result = mysqli_fetch_array($ret);

    if ($result) {
        echo '<script>alert("This username or contact is already registered.");</script>';
    } else {
        $insert = mysqli_query($conn,
            "INSERT INTO admin (username,password,full_name,contact_number)
             VALUES ('$username','$password','$name','$contact')");
        if ($insert) {
            echo '<script>alert("Registration successful! You can now log in."); '
               . 'window.location.href="admin-login.php";</script>';
            exit;
        } else {
            echo '<script>alert("Error registering. Please try again.");</script>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bark & Wiggle – Admin Register</title>
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
        .navbar-brand { color: #FFFFFF !important; }
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
        .hero .logo-wrapper img {
            max-width: 180px;
            height: auto;
        }
        .register-container {
            max-width: 400px;
            margin: 30px auto;
            padding: 30px;
            background: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            color: #6E3387;
            margin-bottom: 20px;
        }
        .form-label { font-weight: 500; }
        .btn-primary {
            background-color: #D6BE3E !important;
            border-color: #D6BE3E !important;
            color: #000000 !important;
        }
        .btn-primary:hover {
            background-color: #c5a634 !important;
            border-color: #c5a634 !important;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            color: #6E3387;
            text-decoration: none;
        }
        .login-link a:hover { text-decoration: underline; }
        .footer { text-align: center; padding: 15px 0; margin-top: 20px; }
        .footer p { color: #6E3387; margin: 0; }
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
            <img src="img/1.jpg" alt="Admin Logo">
        </div>
    </div>
    <section class="container">
        <div class="register-container">
            <h2 class="text-center">Admin Register</h2>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label" for="username">Email Address</label>
                    <input type="email" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="contact">Contact Number</label>
                    <input type="tel" class="form-control" id="contact" name="contact" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="submit">Register</button>
            </form>
            <div class="login-link">
                <p>Already have an account? <a href="admin-login.php">Login Here</a></p>
            </div>
        </div>
    </section>
    <footer class="footer">
        <p>&copy; 2025 Bark & Wiggle. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
