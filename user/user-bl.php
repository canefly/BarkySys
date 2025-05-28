<?php include_once 'helpers/head.php' ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'user-navigation.php';
include_once '../db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['email'])) {
    echo '<script>alert("Please login to view your bookings."); window.location.href="user-login.php";</script>';
    exit();
}

$userEmail = $_SESSION['email'];

// Fetch user-specific bookings
$query = "
    SELECT b.*, u.email, s.service_image
    FROM bookings b
    JOIN users u ON b.email = u.email 
    JOIN services s ON b.service_name = s.service_name 
    WHERE b.email = ?
    ORDER BY b.date DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $userEmail);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
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
            background: var(--bg-light);
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
            color: var(--text);
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

        .status-pending { background: var(--accent); color: var(--text); }
        .status-completed { background: var(--primary); color: var(--bg-light); }
        .status-canceled { background: #e74c3c; color: var(--bg-light); }
        .status-approved { background: var(--secondary); color: var(--bg-light); }

        .status-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 10px;
        }

        .receipt-btn {
            display: inline-block;
            margin-top: 8px;
            padding: 6px 10px;
            background-color: rgb(108, 116, 110);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
        }

        .receipt-btn:hover {
            background-color: rgb(81, 86, 82);
        }

        @media (max-width: 768px) {
            .container { width: 100vw; margin-left: 0; }
            .booking-list { width: 95%; }
            .booking-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 20px;
            }
            .status { margin-top: 10px; width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>My Bookings</h2>
    <div class="booking-list">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): 
                $statusClass = strtolower($row["status"]) === "approved" ? "status-approved" :
                               (strtolower($row["status"]) === "pending" ? "status-pending" :
                               (strtolower($row["status"]) === "completed" ? "status-completed" : "status-canceled"));
                $imagePath = '../' . ltrim($row['service_image'], '/');
            ?>
                <div class="booking-card">
                    <?php if (!empty($row['service_image'])): ?>
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Service Image" class="service-img">
                    <?php endif; ?>
                    <div class="booking-info">
                        <h3>Customer Name: <?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><strong>Service Type:</strong> <?php echo htmlspecialchars($row['service_name']); ?></p>
                        <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($row['date'])); ?> -
                        <strong>Time:</strong> <?php echo date("h:i A", strtotime($row['booking_time'])); ?></p>
                        <p><strong>Price:</strong> ₱<?php echo number_format($row['service_price'], 2); ?></p>
                    </div>
                    <div class="status-container">
                        <span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span>
                        <?php if (strtolower($row["status"]) === "approved"): ?>
                            <a href="user-receipt.php?booking_id=<?php echo $row['id']; ?>" class="receipt-btn">Download Receipt</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No bookings found.</p>
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
