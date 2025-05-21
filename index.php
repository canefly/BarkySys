
<?php include_once 'helpers/head.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bark & Wiggle - Homepage</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .navbar {
      background-color: #7A1EA1;
    }

    .navbar-brand {
      font-weight: 700;
      color: white !important;
    }

    .navbar-nav .nav-link {
      color: white !important;
    }

    .hero-section {
      background: linear-gradient(135deg, #7A1EA1, #9C27B0);
      color: white;
      padding: 80px 0;
      text-align: center;
    }

    .hero-section .btn {
      background-color: #FFD700;
      color: #000;
      font-weight: 600;
      border: none;
    }

    .about-section {
      padding: 60px 20px;
    }

    .text-purple {
      color: #7A1EA1;
    }

    footer {
      background-color: #f8f9fa;
      padding: 20px;
      text-align: center;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="#">Bark<span class="text-warning">&</span>Wiggle</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon bg-light"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="homepage.html">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="user/user-login.php">Book Now</a></li>
          <li class="nav-item"><a class="nav-link" href="aboutus.html">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="hero-section">
    <div class="container">
      <h1 class="display-4 fw-bold">Professional Pet Grooming & Supplies</h1>
      <p class="lead mt-3">Let’s Bark & Wiggle!<br>Book online. Quick. Easy. Reliable.</p>
      <a href="user/user-login.php" class="btn btn-lg mt-3">Book Now</a>
    </div>
  </section>

  <section class="about-section text-center">
    <div class="container">
      <h2 class="fw-bold mb-4">About Bark & Wiggle</h2>
      <p class="text-muted mx-auto" style="max-width: 720px;">
        Located in Caloocan City, Bark & Wiggle offers premium pet grooming and quality pet supplies. 
        Our team is dedicated to making your furry friends feel their best! With easy online booking and 
        friendly staff, we aim to give your pets the love and care they deserve.
      </p>
    </div>
  </section>

  <section class="hours-section text-center py-5 bg-light">
    <div class="container">
      <h2 class="fw-bold text-purple mb-4">Opening Hours</h2>
      <div class="row justify-content-center">
        <div class="col-md-5 mb-3">
          <h5 class="text-warning">Monday – Friday</h5>
          <p class="text-dark">8:00 AM – 6:00 PM</p>
        </div>
        <div class="col-md-5 mb-3">
          <h5 class="text-warning">Saturday & Sunday</h5>
          <p class="text-dark">7:30 AM – 7:00 PM</p>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <p>📍 Five J's Building, Bagumbong Rd., Caloocan</p>
    <p>📘 Bark & Wiggle Pet Grooming & Store | 📞 +63 927 990 2 111</p>
    <p>Follow us on Facebook and Viber!</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
