<?php
session_start();

// Correct session check
if (!isset($_SESSION['username'])) {  
    echo '<script>alert("Please login to access this page."); window.location.href="login.php";</script>';
    exit();
}

include('db.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salon Services</title>
    <style>
body {
    background-color: rgb(236, 227, 218);
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    transition: margin-left 0.3s ease;
}



.container {
    padding: 20px;
    max-width: 100%; /* Smaller container */
    margin-left: 150px; /* Align to the left */
}


body.sidebar-open {
    margin-left: 250px; /* Ensure sidebar pushes content */
}

/* Services Section - Left to Right Alignment */
.services {
    display: flex; /* Use flexbox for left-to-right alignment */
    flex-wrap: wrap; /* Wrap cards when needed */
    gap: 20px;
    margin-top: 30px;
    padding: 10px;
    align-items: flex-start; /* Align items to top */
}

.service-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 300px; /* Set a fixed width for better alignment */
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-card img {
    width: 80px;
    height: 80px;
    margin-bottom: 15px;
    object-fit: contain;
}

.service-card h3 {
    margin: 10px 0;
    color: #333;
    font-size: 20px;
    font-weight: 600;
}

.service-card p {
    color: #777;
    font-size: 14px;
}

.book-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 12px 18px;
    background: #7a6f5d;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: 0.3s;
    border: none;
    cursor: pointer;
}

.book-btn:hover {
    background: #5f5445;
}

    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    

<?php include 'components\services-type.php'; ?>

</body>
</html>
