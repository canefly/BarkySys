<?php
session_start();
include_once '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['full_name'];
        header("Location: user-hp.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bark & Wiggle – Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #fff5ff;
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }

    /* Paws as background particles */
    .paw-particles img {
      position: absolute;
      z-index: 0;
      opacity: 0.07;
      pointer-events: none;
    }
    .paw-1 { top: 5%; left: 10%; transform: rotate(-15deg) scale(0.4); }
    .paw-2 { top: 25%; right: -10%; transform: rotate(30deg) scale(0.9); }
    .paw-3 { bottom: 15%; left: -10%; transform: rotate(-45deg) scale(0.8); }
    .paw-4 { bottom: 15%; right: 15%; transform: rotate(-25deg) scale(0.1); }
    .paw-5 { top: 30%; left: 90%; transform: translate(-50%, -50%) rotate(60deg) scale(0.6); }

    .navbar {
      background-color: #7A1EA1;
    }

    .navbar-brand, .nav-link {
      color: #fff !important;
    }

    .nav-link:hover {
      color: #f9e79f !important;
    }

    .login-container {
      max-width: 400px;
      margin: 80px auto;
      background-color: #fefefe;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      position: relative;
      z-index: 1;
    }

    .logo-icon {
      width: 150px;
      margin: 0 auto 15px;
      display: block;
    }

    .login-container h2 {
      color: #6E3387;
      font-weight: 600;
      text-align: center;
    }

    .btn-primary {
      background-color: #D6BE3E !important;
      border-color: #D6BE3E !important;
      color: #000 !important;
    }

    .btn-primary:hover {
      background-color: #c5a634 !important;
      border-color: #c5a634 !important;
    }

    .register-link {
      text-align: center;
      margin-top: 20px;
    }

    .register-link a {
      color: #6E3387;
      font-weight: 500;
      text-decoration: none;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    .footer {
      text-align: center;
      padding: 15px 0;
      margin-top: 60px;
      color: #6E3387;
    }

    .alert {
      font-size: 0.9rem;
    }

    @media (max-width: 576px) {
      .login-container {
        margin: 40px 20px;
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

  <!-- Background floating paw prints -->
  <div class="paw-particles">
    <img src="../img/Paw_Print.svg" class="paw-1" alt="paw">
    <img src="../img/Paw_Print.svg" class="paw-2" alt="paw">
    <img src="../img/Paw_Print.svg" class="paw-3" alt="paw">
    <img src="../img/Paw_Print.svg" class="paw-4" alt="paw">
    <img src="../img/Paw_Print.svg" class="paw-5" alt="paw">
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand fw-bold" href="../index.php">Bark<span class="text-warning">&</span>Wiggle</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="user-register.php">Register</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Login Form -->
  <div class="container">
    <div class="login-container">
      <img src="../img/logo_cropped.png" alt="logo" class="logo-icon">
      <h2>Welcome Back!</h2>
      <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
      <?php endif; ?>
      <form method="POST" class="mt-4">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="text" class="form-control" id="email" name="email" required autofocus>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
      <div class="register-link mt-3">
        <p>Don't have an account? <a href="user-register.php">Register here</a></p>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2025 Bark & Wiggle. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
