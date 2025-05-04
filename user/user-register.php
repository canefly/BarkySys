<?php
session_start();
include_once '../db.php'; // Database connection file

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bark & Wiggle – Register</title>
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

    /* Floating Paw Particles */
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

    .register-container {
      max-width: 450px;
      margin: 80px auto;
      background-color: #ffffff;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      position: relative;
      z-index: 1;
    }

    .logo-icon {
      width: 150px;
      margin: 0 auto 20px;
      display: block;
    }

    .register-container h2 {
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

    .form-text {
      text-align: center;
      margin-top: 20px;
    }

    .form-text a {
      color: #6E3387;
      text-decoration: none;
      font-weight: 500;
    }

    .form-text a:hover {
      text-decoration: underline;
    }

    .footer {
      text-align: center;
      padding: 15px 0;
      margin-top: 60px;
      color: #6E3387;
    }

    @media (max-width: 576px) {
      .register-container {
        margin: 40px 20px;
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

  <!-- Floating Paw Background -->
  <div class="paw-particles">
    <img src="../img/Paw_Print.svg" class="paw-1" alt="" role="presentation">
    <img src="../img/Paw_Print.svg" class="paw-2" alt="" role="presentation">
    <img src="../img/Paw_Print.svg" class="paw-3" alt="" role="presentation">
    <img src="../img/Paw_Print.svg" class="paw-4" alt="" role="presentation">
    <img src="../img/Paw_Print.svg" class="paw-5" alt="" role="presentation">
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
          <li class="nav-item"><a class="nav-link" href="user-login.php">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Register Form -->
  <div class="container">
    <div class="register-container">
      <img src="../img/logo_cropped.png" alt="Bark & Wiggle Logo" class="logo-icon">
      <h2 class="mb-4">Register</h2>
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
          <label for="petName" class="form-label">Pet Name</label>
          <input type="text" id="petName" name="petName" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="petBreed" class="form-label">Pet Breed</label>
          <input type="text" id="petBreed" name="petBreed" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="petAge" class="form-label">Pet Age</label>
          <input type="number" id="petAge" name="petAge" class="form-control" required min="0">
        </div>
        <div class="mb-3">
          <label for="petWeight" class="form-label">Pet Weight (kg)</label>
          <input type="number" id="petWeight" name="petWeight" class="form-control" required min="0" step="0.1">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="agree" required>
          <label class="form-check-label" for="agree">
            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a> and 
            <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>.
          </label>
        </div>
        <button type="submit" class="btn btn-primary w-100" name="register">Register</button>
      </form>
      <div class="form-text">
        <p class="mt-3">Already have an account? <a href="user-login.php">Login here</a></p>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2025 Bark & Wiggle. All rights reserved.</p>
  </footer>

  <!-- Terms & Conditions Modal -->
  <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Terms & Conditions</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Welcome to Bark & Wiggle! By registering and using our services, you agree to the following terms:</p>
          <ol>
            <li><strong>Service Scope:</strong> Bark & Wiggle provides online booking and profile management for pet-related services. By using our system, you confirm the accuracy of all details provided, including your pet's information and your personal contact/payment details.</li>
            <li><strong>Eligibility:</strong> You must be 18 years or older to register. All information provided must be truthful and up-to-date.</li>
            <li><strong>Pet Information:</strong> You agree to provide accurate details about your pet, including their name, weight, and age, to ensure proper care and service compatibility.</li>
            <li><strong>Payments:</strong> By entering your payment information, you authorize Bark & Wiggle or our payment partners to charge you for the selected services. We do not store raw card data on our servers.</li>
            <li><strong>Account Responsibility:</strong> You are responsible for keeping your login credentials safe. Bark & Wiggle is not liable for unauthorized access due to user negligence.</li>
            <li><strong>Right to Modify:</strong> Bark & Wiggle may update these terms at any time. You will be notified of any major changes through your registered email.</li>
            <li><strong>Service Refusal:</strong> We reserve the right to refuse or terminate service to anyone who violates these terms or behaves inappropriately toward our staff or animals.</li>
          </ol>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Privacy Policy Modal -->
  <div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Privacy Policy</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Your privacy is important to us. Bark & Wiggle is committed to protecting your personal data. This policy explains what we collect and how we use it:</p>
          <ol>
            <li><strong>Information We Collect:</strong>
              <ul>
                <li>Pet name, weight, age</li>
                <li>Owner's full name and contact number</li>
                <li>Email address and login credentials</li>
                <li>Payment information (processed via secure third-party gateway)</li>
              </ul>
            </li>
            <li><strong>How We Use Your Data:</strong>
              <ul>
                <li>To manage your bookings and pet profiles</li>
                <li>To contact you about appointments, confirmations, or service updates</li>
                <li>To process payments securely</li>
                <li>To improve our services and user experience</li>
              </ul>
            </li>
            <li><strong>Data Protection:</strong> All personal information is stored securely. We never share or sell your data to third parties. Payment processing is handled via encrypted channels through trusted providers.</li>
            <li><strong>Cookies:</strong> Our site may use cookies to enhance functionality. You can control cookie preferences in your browser settings.</li>
            <li><strong>Access & Deletion:</strong> You may request to view or delete your stored data at any time by contacting our support team.</li>
            <li><strong>Policy Updates:</strong> We may revise this policy from time to time. You will be notified of significant changes through email or on your dashboard.</li>
          </ol>
          <p>If you have any questions or concerns, please contact us at <a href="mailto:support@barkandwiggle.com">support@barkandwiggle.com</a>.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
