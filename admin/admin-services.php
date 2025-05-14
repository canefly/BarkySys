<?php
session_start();
include_once '../db.php';
include_once 'admin-navigation.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $service_type        = mysqli_real_escape_string($conn, $_POST['servicesType']);
    $service_name        = mysqli_real_escape_string($conn, $_POST['service_name']);
    $service_description = mysqli_real_escape_string($conn, $_POST['service_description']);
    $service_price       = mysqli_real_escape_string($conn, $_POST['service_price']);

    // Validate upload
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

    if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $full_path)) {
        $sql = "
          INSERT INTO services
            (service_type, service_name, service_description, service_price, service_image)
          VALUES
            ('$service_type', '$service_name', '$service_description', '$service_price', '$relative_path')
        ";
        if (mysqli_query($conn, $sql)) {
            echo '<script>alert("Service added successfully!"); window.location.href="admin-services-list.php";</script>';
            exit;
        } else {
            echo '<script>alert("Error adding service. Please try again.");</script>';
        }
    } else {
        echo '<script>alert("Failed to upload image.");</script>';
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
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <style>
    body {
      font-family: "Helvetica", Arial, sans-serif;
      background-color: #F7F2EB;
      margin: 0;
      padding: 0;
    }
    h2 {
      font-weight: bold;
    }
    label {
      font-weight: bold;
    }
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
              <div class="mb-3">
                <label for="servicesType" class="form-label">Type of Service</label>
                <select class="form-select" id="servicesType" name="servicesType" required>
                  <option value="" selected disabled>Select an option</option>
                  <option value="DogGrooming">Dog Grooming</option>
                  <option value="CatGrooming">Cat Grooming</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="service_name" class="form-label">Service Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="service_name"
                  name="service_name"
                  placeholder="Enter service name"
                  required
                >
              </div>

              <div class="mb-3">
                <label for="service_description" class="form-label">Service Description</label>
                <textarea
                  class="form-control"
                  id="service_description"
                  name="service_description"
                  rows="3"
                  placeholder="Describe the service"
                  required
                ></textarea>
              </div>

              <div class="mb-3">
                <label for="service_price" class="form-label">Service Price (₱)</label>
                <input
                  type="number"
                  class="form-control"
                  id="service_price"
                  name="service_price"
                  placeholder="0.00"
                  step="0.01"
                  required
                >
              </div>

              <div class="mb-4">
                <label for="service_image" class="form-label">Service Image</label>
                <input
                  class="form-control"
                  type="file"
                  id="service_image"
                  name="service_image"
                  accept=".jpg,.jpeg,.png"
                  required
                >
              </div>

              <button type="submit" class="btn btn-warning w-100 py-2">
                Add Service
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
