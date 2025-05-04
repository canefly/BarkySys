<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'admin-navigation.php';
include('db.php');

// Ensure user is logged in
if (!isset($_SESSION['admin'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="admin-login.php";</script>';
    exit();
}

// Fetch all canceled and completed bookings
$query = "
    SELECT b.*, u.username, s.service_image
    FROM bookings b
    JOIN users u ON b.email = u.username 
    JOIN services s ON b.service_name = s.service_name 
    WHERE TRIM(LOWER(b.status)) IN ('canceled', 'completed')
    ORDER BY b.date DESC
";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Booking List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: rgb(236, 227, 218);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        .container {
            width: 100vw;
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        }

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

        .booking-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 90%;
            max-width: 1200px;
            margin: auto;
            margin-top: 20px;
        }

        .booking-card {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
            transition: 0.3s;
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
            background: #ffcc00;
            color: #5a4500;
        }

        .status-completed {
            background: #4caf50;
            color: white;
        }

        .status-canceled {
            background: #e74c3c;
            color: white;
        }

        .status-approved {
            background: rgb(89, 189, 31);
            color: white;
        }

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

<div class="container">
    <h2>All Canceled & Completed Bookings</h2>
    <div class="booking-list">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): 
                $statusClass = strtolower(trim($row["status"])) === "completed" ? "status-completed" : "status-canceled";
            ?>
                <div class="booking-card">
                    <?php if (!empty($row['service_image'])): ?>
                        <img src="<?php echo htmlspecialchars($row['service_image']); ?>" alt="Service Image" class="service-img">
                    <?php endif; ?>
                    <div class="booking-info">
                        <h3>Customer Name: <?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><strong>Service Type:</strong> <?php echo htmlspecialchars($row['service_name']); ?></p>
                        <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($row['date'])); ?> - 
                           <strong>Time:</strong> <?php echo date("h:i A", strtotime($row['booking_time'])); ?></p>
                        <p><strong>Price:</strong> ₱<?php echo number_format($row['service_price'], 2); ?></p>
                    </div>
                    <span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No canceled or completed bookings found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        document.body.classList.toggle("sidebar-open");
        sidebar.classList.toggle("show");
        overlay.classList.toggle("show");
    }
</script>

</body>
</html>
