<?php
session_start();
include('db.php');

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']); // Store password as plain text

    // Check if the username or contact address already exists
    $ret = mysqli_query($conn, "SELECT username FROM admin WHERE username='$username' OR contact_number='$contact'");
    $result = mysqli_fetch_array($ret);

    if ($result) {
        echo '<script>alert("This username or contact address is already associated with another account.")</script>';
    } else {
        // Insert the new user into the users table
        $query = mysqli_query($conn, "INSERT INTO admin(username, password, full_name, contact_number) 
        VALUES('$username', '$password', '$name', '$contact')");

        if ($query) {
            echo '<script>alert("You have successfully registered.")</script>';
        } else {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
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
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
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
            <button type="submit" class="submit-btn" name="submit">Register</button>
        </form>
        <p>By clicking the Sign Up button, you agree to our <a href="">Terms and Conditions</a> and <a href="">Privacy Policy</a></p>
        <p>Already have an account? <a href="admin-login.php">Login Here</a></p>
    </div>
</body>
</html>
