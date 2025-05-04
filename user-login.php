<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['full_name'];
        header("Location: Customer-hp.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bark & Wiggle – Login</title>
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
        .register-link {
            margin-top: 15px;
        }
        .register-link a {
            color: #6E3387;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Bark & Wiggle</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="hero">
        <div class="logo-wrapper">
            <img src="img/logo.png" alt="Bark & Wiggle Logo" class="logo">
        </div>
    </div>
    <section id="login" class="container">
        <div class="login-container">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <h2 class="text-center mb-4">Login</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
            </form>
            <div class="register-link text-center">
                <p>Don't have an account? <a href="user-register.php">Register</a></p>
            </div>
        </div>
    </section>
    <footer class="footer">
        <p>&copy; 2025 Bark & Wiggle. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
