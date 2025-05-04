<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Haircut</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color:rgb(236, 227, 218);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: margin-left 0.3s ease-in-out;
        }

        .container {
            display: flex;
            max-width: 900px;
            width: 100%;
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .selected-service {
            width: 40%;
            padding: 20px;
            text-align: center;
        }

        .selected-service img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .booking-form {
            width: 60%;
            padding: 20px;
        }

        .booking-form h2 {
            margin-bottom: 10px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .submit-btn {
            width: 100%;
            padding: 10px;
            background: #7a6f5d;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background: #5f5445;
        }

        /* Sidebar effect */
        body.sidebar-open {
            margin-left: 250px;
        }
    </style>
</head>
<body>

<?php include 'components\customer-bookform.php'; ?>


<script>
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const body = document.body;

        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
        body.classList.toggle("sidebar-open"); // Moves container when sidebar opens
    }
</script>

</body>
</html>
