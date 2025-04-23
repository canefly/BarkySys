<?php
session_start();
include('db.php'); // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['email']); 
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $role = 'customer'; // Default role

    // Check if username already exists
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        echo '<script>alert("Username already exists. Please choose another one."); window.location.href="register.php";</script>';
        exit();
    }

    // Insert user into database WITHOUT hashing the password
    $query = "INSERT INTO users (username, password, full_name, contact) 
              VALUES ('$username', '$password', '$full_name', '$contact')";

    if (mysqli_query($conn, $query)) {
        echo '<script>alert("Registration successful! You can now log in."); window.location.href="login.php";</script>';
    } else {
        echo '<script>alert("Error registering user. Please try again.");</script>';
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <style>
        body {
            background-color: #e5e0d8;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.3s;
        }
        .form-group input:focus {
            border-color: #7a6f5d;
            outline: none;
            box-shadow: 0 0 5px rgba(122, 111, 93, 0.5);
        }
        .submit-btn {
            width: 100%;
            background: #7a6f5d;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .submit-btn:hover {
            background: #5f5445;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <h2>Create an Account</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="tel" id="contact" name="contact" required>
            </div>
            <button type="submit" class="submit-btn">Register</button>
        </form>
        <p>By clicking the Sign Up button, you agree to our <a href="">Terms and Conditions</a> and <a href="">Privacy Policy</a></p>
        <p>Already have an account? <a href="login.php">Login Here</a></p>
    </div>
</body>
</html>
