<?php
session_start();
include_once '../db.php';

$feedback = '';
$success = false;

if (isset($_POST['submit'])) {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $name     = trim($_POST['name']);
    $contact  = trim($_POST['contact']);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM admin WHERE email = ? OR contact_number = ?");
    $stmt->bind_param("ss", $email, $contact);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $feedback = "This email or contact is already registered.";
    } else {
        $insert = $conn->prepare("INSERT INTO admin (email, password, full_name, contact_number) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $email, $hashedPassword, $name, $contact);
        if ($insert->execute()) {
            $feedback = "Registration successful! Redirecting to login...";
            $success = true;
        } else {
            $feedback = "Error registering. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bark & Wiggle – Admin Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FFFFFF;
            color: #000000;
        }
        .navbar { background-color: #6E3387 !important; margin-bottom: 20px; }
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
        .hero img {
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
        <img src="../img/logo no text.png" alt="Admin Logo">
    </div>
</div>

<section class="container">
    <div class="register-container">
        <h2 class="text-center">Admin Register</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="contact">Contact Number</label>
                <input type="tel" class="form-control" name="contact" id="contact" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</section>

<footer class="footer">
    <p>&copy; 2025 Bark & Wiggle. All rights reserved.</p>
</footer>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo $success ? 'Success!' : 'Notice'; ?></h5>
      </div>
      <div class="modal-body">
        <p><?php echo $feedback; ?></p>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap + Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($feedback): ?>
<script>
    var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
    feedbackModal.show();

    <?php if ($success): ?>
        setTimeout(function () {
            window.location.href = "admin-login.php";
        }, 2000);
    <?php endif; ?>
</script>
<?php endif; ?>
</body>
</html>
