<?php
session_start();
include_once '../db.php';
include_once 'admin-navigation.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $service_type        = mysqli_real_escape_string($conn, $_POST['servicesType']);
    $mode                = mysqli_real_escape_string($conn, $_POST['mode']);
    $service_name        = mysqli_real_escape_string($conn, $_POST['service_name']);
    $service_description = mysqli_real_escape_string($conn, $_POST['service_description']);
    $service_price_input = isset($_POST['service_price']) ? mysqli_real_escape_string($conn, $_POST['service_price']) : null;

    // Validate image upload
    if (!isset($_FILES['service_image']) || $_FILES['service_image']['error'] !== UPLOAD_ERR_OK) {
        echo '<script>alert("Please upload a valid image file.");</script>';
        exit;
    }

    $upload_dir = "../uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_name   = basename($_FILES["service_image"]["name"]);
    $unique_name  = time() . "_" . preg_replace('/\s+/', '_', $image_name);
    $relative_path = "uploads/" . $unique_name;
    $full_path    = $upload_dir . $unique_name;

    $ext     = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png'];
    if (!in_array($ext, $allowed)) {
        echo '<script>alert("Only JPG, JPEG, and PNG files are allowed.");</script>';
        exit;
    }

    if (!move_uploaded_file($_FILES["service_image"]["tmp_name"], $full_path)) {
        echo '<script>alert("Failed to upload image.");</script>';
        exit;
    }

    // Determine price value or NULL for packages
    if ($mode === 'package') {
        $priceValue = 'NULL';
    } else {
        $priceValue = "'" . $service_price_input . "'";
    }

    // Insert into services table (ensure `mode` column exists and `service_price` is nullable)
    $sql = "
      INSERT INTO services
        (service_type, mode, service_name, service_description, service_price, service_image)
      VALUES
        ('$service_type', '$mode', '$service_name', '$service_description', $priceValue, '$relative_path')
    ";

    if (mysqli_query($conn, $sql)) {
        echo '<script>alert("Service added successfully!"); window.location.href="admin-services-list.php";</script>';
        exit;
    } else {
        echo '<script>alert("Error adding service: ' . mysqli_error($conn) . '");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Service</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: "Helvetica", Arial, sans-serif; background-color: #F7F2EB; margin: 0; padding: 0; }
    h2, label { font-weight: bold; }
  </style>
</head>
<body>
  <div class="container-fluid py-4">
    <h2 class="text-center mb-4">Add New Service</h2>
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data" novalidate>

              <!-- Service Type -->
              <div class="mb-3">
                <label for="servicesType" class="form-label">Type of Service</label>
                <select class="form-select" id="servicesType" name="servicesType" required>
                  <option value="" selected disabled>Select an option</option>
                  <option value="DogGrooming">Dog Grooming</option>
                  <option value="CatGrooming">Cat Grooming</option>
                </select>
              </div>

              <!-- Service Mode -->
              <div class="mb-3">
                <label class="form-label">Service Mode</label><br>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="mode" id="modeIndividual" value="individual" checked>
                  <label class="form-check-label" for="modeIndividual">Individual</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="mode" id="modePackage" value="package">
                  <label class="form-check-label" for="modePackage">Package</label>
                </div>
              </div>

              <!-- Service Name -->
              <div class="mb-3">
                <label for="service_name" class="form-label">Service Name</label>
                <input type="text" class="form-control" id="service_name" name="service_name" placeholder="Enter service name" required>
              </div>

              <!-- Service Description -->
              <div class="mb-3">
                <label for="service_description" class="form-label">Service Description</label>
                <textarea class="form-control" id="service_description" name="service_description" rows="3" placeholder="Describe the service" required></textarea>
              </div>

              <!-- Service Price -->
              <div class="mb-3" id="priceDiv">
                <label for="service_price" class="form-label">Service Price (₱)</label>
                <input type="number" class="form-control" id="service_price" name="service_price" placeholder="0.00" step="0.01" required>
              </div>

              <!-- Service Image -->
              <div class="mb-4">
                <label for="service_image" class="form-label">Service Image</label>
                <input class="form-control" type="file" id="service_image" name="service_image" accept=",.jpg,.jpeg,.png" required>
              </div>

              <button type="submit" class="btn btn-warning w-100 py-2">Add Service</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle price input visibility based on mode
    const modeRadios = document.querySelectorAll('input[name="mode"]');
    const priceDiv   = document.getElementById('priceDiv');
    const priceInput = document.getElementById('service_price');

    modeRadios.forEach(radio => {
      radio.addEventListener('change', () => {
        if (radio.value === 'package' && radio.checked) {
          priceDiv.style.display = 'none';
          priceInput.removeAttribute('required');
        } else if (radio.value === 'individual' && radio.checked) {
          priceDiv.style.display = 'block';
          priceInput.setAttribute('required', '');
        }
      });
    });
  </script>
</body>
</html>
