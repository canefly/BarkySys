<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color:rgb(236, 227, 218);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            transition: margin-left 0.3s ease-in-out;
        }

        .container {
            flex: 1;
            padding: 20px;
            margin-left:70px;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Apply margin-left when sidebar is open */
        body.sidebar-open .container {
            width: calc(100vw - 290px);
            margin-left: 290px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .service-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .service-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
            flex-wrap: wrap;
        }

        .service-card img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 10px;
        }

        .service-info {
            flex: 1;
            text-align: left;
        }

        .service-info h3 {
            margin: 0;
            color: #333;
        }

        .service-info p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .delete-btn:hover {
            background: #c0392b;
        }
        .btn {
            background: #7a6f5d;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn:hover {
            background: #5f5445;
        }
    </style>
</head>
<body>

<?php include 'components\admin-services-list.php'; ?>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const body = document.body;

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            body.classList.toggle("sidebar-open"); // Moves content when sidebar is open
        }
    </script>

</body>
</html>
