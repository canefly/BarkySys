<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bark & Wiggle Admin – Navigation</title>
    <style>
        :root {
            --primary: #6E3387;      /* Deep Purple */
            --alt: #8F54A0;          /* Lighter Purple */
            --accent: #D6BE3E;       /* Gold Yellow */
            --bg-light: #FFFFFF;
            --text-dark: #000000;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
        }
        .menu-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            background: var(--accent);
            color: var(--text-dark);
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1100;
        }
        .sidebar {
            width: 250px;
            background: var(--primary);
            color: var(--bg-light);
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
            color: var(--accent);
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li {
            padding: 12px 16px;
            margin-bottom: 10px;
            background: var(--alt);
            color: var(--bg-light);
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
            text-align: center;
        }
        .sidebar ul li:hover {
            background: var(--accent);
            color: var(--text-dark);
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
        <h2>Admin Menu</h2>
        <ul>
            <li onclick="location.href='admin-hp.php'">Dashboard</li>
            <li onclick="location.href='admin-appointment.php'">Appointments</li>
            <li onclick="location.href='admin-bl.php'">Booking List</li>
            <li onclick="location.href='admin-pricing-list.php'">Pricing List</li>
            <li onclick="location.href='admin-services.php'">Services</li>
            <li onclick="location.href='admin-services-list.php'">Services List</li>
            <li onclick="location.href='admin-history.php'">History Booking</li>
            <li onclick="location.href='admin-audit-log.php'">Audit Log</li>
            <li onclick="location.href='admin-logout.php'">Logout</li>
        </ul>
    </div>
    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>
    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            document.body.classList.toggle('sidebar-open');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    </script>
</body>
</html>
