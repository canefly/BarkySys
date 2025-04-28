<?php
session_start();
include('db.php'); // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_type = mysqli_real_escape_string($conn, $_POST['servicesType']);
    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
    $service_description = mysqli_real_escape_string($conn, $_POST['service_description']);
    $service_price = mysqli_real_escape_string($conn, $_POST['service_price']);

    if (!isset($_FILES['service_image']) || $_FILES['service_image']['error'] != UPLOAD_ERR_OK) {
        echo '<script>alert("Please upload a valid image file.");</script>';
        exit();
    }

    $target_dir = "uploads/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = basename($_FILES["service_image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_types = ['jpg', 'jpeg', 'png'];
    if (!in_array($imageFileType, $allowed_types)) {
        echo '<script>alert("Only JPG, JPEG, and PNG files are allowed.");</script>';
        exit();
    }

    if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $target_file)) {
        $query = "INSERT INTO services (service_type, service_name, service_description, service_price, service_image) 
                  VALUES ('$service_type', '$service_name', '$service_description', '$service_price', '$target_file')";

        if (mysqli_query($conn, $query)) {
            echo '<script>alert("Service added successfully!"); window.location.href="services-list.php";</script>';
        } else {
            echo '<script>alert("Error adding service. Please try again.");</script>';
        }
    } else {
        echo '<script>alert("Failed to upload image.");</script>';
    }
}
?>

<?php 
include 'admin-navigation.php'; 
include 'db.php';
?>

<div class="container">
    <h2>Add New Service</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="servicesType">Type of Services</label>
        <select name="servicesType" id="servicesType" required>
            <option value="" disabled selected>Select an option</option>
            <option value="DogGrooming">Dog Grooming</option> <!-- Haircut = Dog Grooming -->
            <option value="CatGrooming">Cat Grooming</option> <!-- HairColor = Cat Grooming -->

            <!-- Future service options, hidden for now -->
            <!-- 
            <option value="Rebond">Hair Rebonding</option>
            <option value="Manicure">Manicure and Pedicure</option>
            -->
        </select>

        <input type="text" name="service_name" placeholder="Service Name" required>
        <textarea name="service_description" placeholder="Service Description" rows="3" required></textarea>
        <input type="number" name="service_price" placeholder="Service Price (₱)" required>

        <label for="service_image">Upload Service Image</label>
        <input type="file" name="service_image" accept="image/*" required>

        <button type="submit" class="btn">Add Service</button>
    </form>
</div>
