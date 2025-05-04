<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="user-login.php";</script>';
    exit();
}

include_once 'user-navigation.php';
include_once '../db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bark & Wiggle – Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-light: #FFFFFF;
            --bg-muted: #F7F2EB;
            --text: #333333;
            --primary: #6E3387;
            --accent: #D6BE3E;
            --btn-bg: #D6BE3E;
            --btn-hover: #C5A634;
        }

        body {
            background-color: var(--bg-muted);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text);
            transition: margin-left 0.3s ease;
        }

        .container {
            padding: 20px;
            margin-left: 150px;
            max-width: 100%;
        }

        body.sidebar-open {
            margin-left: 250px;
        }

        h2 {
            text-align: center;
            color: var(--primary);
        }

        .services {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            padding: 10px;
            align-items: flex-start;
        }

        .service-card {
            background: var(--bg-light);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 300px;
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
            color: var(--primary);
            font-size: 20px;
            font-weight: 600;
        }

        .service-card p {
            color: var(--text);
            font-size: 14px;
        }

        .book-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 18px;
            background: var(--btn-bg);
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }

        .book-btn:hover {
            background: var(--btn-hover);
        }

        @media(max-width: 768px) {
            .service-card {
                flex: 1 1 calc(50% - 20px);
                max-width: calc(50% - 20px);
            }
        }

        @media(max-width: 576px) {
            .container { margin-left: 0; }
            .services { flex-direction: column; }
            .service-card { max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Our Pet Services</h2>
    <div class="services">
        <div class="service-card">
            <img src="../img/doggy.png" alt="Dog Grooming">
            <h3>Dog Grooming</h3>
            <p>Keep your furry buddy clean and stylish with a professional trim, bath, and brush tailored for every dog.</p>
            <a href="grooming-dog.php" class="book-btn">View Now</a>
        </div>
        <div class="service-card">
            <img src="../img/cat.png" alt="Cat Grooming">
            <h3>Cat Grooming</h3>
            <p>Gentle, stress-free grooming sessions made just for felines — because they deserve to be fabulous too.</p>
            <a href="grooming-cat.php" class="book-btn">View Now</a>
        </div>
    </div>
</div>

</body>
</html>
