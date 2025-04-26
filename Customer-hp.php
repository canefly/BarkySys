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
    <title>Bark & Wiggle – Dashboard</title>
    <style>
        /* Color variables */
        :root {
            --bg-light: #FFFFFF;
            --bg-muted: #F7F2EB; /* soft warm background */
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
        /* Services Section */
        .services {
    display: flex;
    flex-wrap: wrap;
    justify-content: center; /* CENTER the cards horizontally */
    gap: 20px;
    margin-top: 30px;
    padding: 10px;
    align-items: flex-start; /* Keep vertical alignment clean */
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'components/services-type.php'; ?>
</body>
</html>
