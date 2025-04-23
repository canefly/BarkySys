<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Booking List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color:rgb(236, 227, 218);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        .container {
            width: 100vw; /* Default width (full screen) */
            margin-left: 0; /* Default position */
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
            background: #fff;
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
            color: #333;
            font-size: 18px;
        }

        .booking-info p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        /* Buttons for Accept and Cancel */
        .booking-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-accept {
            background:rgb(175, 162, 43);
            color: white;
        }

        .btn-accept:hover {
            background:rgb(202, 200, 54);
        }

        .btn-cancel {
            background: #e74c3c;
            color: white;
        }

        .btn-cancel:hover {
            background: #c0392b;
        }

        .btn-complete {
            background:rgb(47, 146, 60);
            color: white;
        }

        .btn-complete:hover {
            background:rgb(53, 185, 71);
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

            .booking-actions {
                margin-top: 10px;
                width: 100%;
                display: flex;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<?php include 'components\admin-bl.php'; ?>

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
