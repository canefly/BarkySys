<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Navigation</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #7a6f5d;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            left: -250px;
            top: 0;
            transition: left 0.3s ease;
            z-index: 1000;
        }
        .sidebar.show {
            left: 0;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px;
            margin: 10px 0;
            background: #5f5445;
            text-align: center;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        .sidebar ul li:hover {
            background: #4a3f35;
        }
        .menu-toggle {
            display: block;
            position: fixed;
            top: 20px;
            left: 20px;
            background: #7a6f5d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1100;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 900;
        }
        .overlay.show {
            display: block;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <div class="sidebar" id="sidebar">
        <h2>Salon Menu</h2>
        <ul>
            <li onclick="location.href='Customer-hp.php'">Dashboard</li>
            <li onclick="location.href='Customer-bl.php'">Booking List</li>
            <li onclick="location.href='loging-out.php'">Logout</li>
        </ul>
    </div>
    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>
    <script>
       function toggleMenu() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const body = document.body;

    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
    body.classList.toggle('sidebar-open'); // Add class to push content
}

        
    </script>
</body>
</html>
