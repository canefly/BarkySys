<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="user-login.php";</script>';
    exit();
}

include('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Booking List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Color variables */
        :root {
            --bg-muted: #F7F2EB;
            --bg-light: #FFFFFF;
            --text: #333333;
            --primary: #6E3387;
            --accent: #D6BE3E;
            --secondary: #8F54A0;
        }
        body {
            background-color: var(--bg-muted);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            transition: margin-left 0.3s ease-in-out;
            color: var(--text);
        }

        .container {
            width: 100vw;
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        }

        /* Adjust when sidebar is shown */
        body.sidebar-open .container {
            width: calc(100vw - 250px);
            margin-left: 250px;
        }

        h2 {
            text-align: center;
            font-weight: 600;
            color: var(--primary);
        }

        .service-img {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }

        /* Booking List Styles */
        .booking-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 90%;
            max-width: 1200px;
            margin: auto;
            margin-top: 20px;
        }

        /* Booking Cards */
        .booking-card {
            background: var(--bg-light);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
            flex-wrap: wrap;
            width: 100%;
        }

        .booking-card:hover {
            transform: translateY(-3px);
        }

        .booking-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
            flex: 1;
        }

        .booking-info h3 {
            margin: 0;
            color: var(--text);
            font-size: 18px;
        }

        .booking-info p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        /* Status Badges */
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            text-align: center;
            min-width: 100px;
        }

        .status-pending {
            background: var(--accent);
            color: var(--text);
        }

        .status-completed {
            background: var(--primary);
            color: var(--bg-light);
        }

        .status-canceled {
            background: #e74c3c;
            color: var(--bg-light);
        }

        .status-approved {
            background: var(--secondary);
            color: var(--bg-light);
        }

        /* 🔹 RESPONSIVE STYLES */
        @media (max-width: 768px) {
            .container {
                width: 100vw;
                margin-left: 0;
            }

            .booking-list {
                width: 95%;
            }

            .booking-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 20px;
            }

            .status {
                margin-top: 10px;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <?php include 'components/customer-booking-list.php'; ?>

    

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const body = document.body;

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            body.classList.toggle("sidebar-open"); // This controls container movement
        }
    </script>

</body>
</html>
