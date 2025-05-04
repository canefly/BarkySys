<?php
session_start();
include_once '../db.php';
include_once 'admin-navigation.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_type = mysqli_real_escape_string($conn, $_POST['servicesType']);
    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
    $service_description = mysqli_real_escape_string($conn, $_POST['service_description']);
    $service_price = mysqli_real_escape_string($conn, $_POST['service_price']);

    // Validate image upload
    if (!isset($_FILES['service_image']) || $_FILES['service_image']['error'] != UPLOAD_ERR_OK) {
        echo '<script>alert("Please upload a valid image file.");</script>';
        exit();
    }

    $upload_dir = "../uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_name = basename($_FILES["service_image"]["name"]);
    $unique_name = time() . "_" . $image_name;
    $relative_path = "uploads/" . $unique_name; // This is what's stored in the DB
    $full_path = $upload_dir . $unique_name;

    $imageFileType = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png'];
    if (!in_array($imageFileType, $allowed_types)) {
        echo '<script>alert("Only JPG, JPEG, and PNG files are allowed.");</script>';
        exit();
    }

    // Move and insert into DB
    if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $full_path)) {
        $query = "INSERT INTO services (service_type, service_name, service_description, service_price, service_image) 
                  VALUES ('$service_type', '$service_name', '$service_description', '$service_price', '$relative_path')";

        if (mysqli_query($conn, $query)) {
            echo '<script>alert("Service added successfully!"); window.location.href="admin-services-list.php";</script>';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
         body {
        background-color: #F7F2EB;
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        transition: margin-left 0.3s ease-in-out;
    }

    .container {
        background: white;
        border-radius: 10px;
        width: 100vw;
        margin-left: 70px;
        margin-right: 1em;
        margin-top: 5em;
        padding: 20px;
        transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    body.sidebar-open .container {
        width: calc(100vw - 300px);
        margin-left: 300px;
    }

    h2 {
        margin-bottom: 20px;
        color: #6E3387;
        font-size: 24px;
        font-weight: bold;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    input, textarea, select {
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        background: #f9f9f9;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease-in-out;
    }

    input:focus, textarea:focus, select:focus {
        border-color: #6E3387;
        outline: none;
        box-shadow: 0px 0px 5px rgba(110, 51, 135, 0.5);
    }

    input[type="file"] {
        padding: 5px;
    }

    label {
        font-size: 16px;
        font-weight: bold;
        color: #6E3387;
        margin-bottom: 5px;
        display: block;
    }

    .custom-select {
        position: relative;
    }

    .custom-select select {
        appearance: none;
        cursor: pointer;
    }

    .custom-select::after {
        content: "\25BC";
        font-size: 14px;
        color: #6E3387;
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .btn {
        background: #D6BE3E;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        font-size: 16px;
        transition: 0.3s;
    }

    .btn:hover {
        background: #C5A634;
    }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Service</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="servicesType">Type of Services</label>
        <select name="servicesType" id="servicesType" required>
            <option value="" disabled selected>Select an option</option>
            <option value="DogGrooming">Dog Grooming</option>
            <option value="CatGrooming">Cat Grooming</option>
        </select>

        <input type="text" name="service_name" placeholder="Service Name" required>
        <textarea name="service_description" placeholder="Service Description" rows="3" required></textarea>
        <input type="number" name="service_price" placeholder="Service Price (₱)" required>

        <label for="service_image">Upload Service Image</label>
        <input type="file" name="service_image" accept="image/*" required>

        <button type="submit" class="btn">Add Service</button>
    </form>
</div>

<script>
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const body = document.body;

        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
        body.classList.toggle("sidebar-open");
    }
</script>

</body>
</html>
