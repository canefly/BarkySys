

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salon Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color:rgb(236, 227, 218);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease-in-out;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            margin-top:4em;
            margin-left:50px;
            transition: margin-left 0.3s ease-in-out;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .stats-container {
            display: flex;
            gap: 20px;
            width: 100%;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            text-align: center;
            font-size: 1.2em;
        }
        .chart-container, .pie-chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .chart-container { width: 50%; max-width: 800px; }
        .pie-chart-container { width: 30%; max-width: 400px; }
    </style>
</head>
<body>

<?php include 'components/admin-hp.php'; ?>



</body>
</html>
