<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'admin-navigation.php';
include('db.php');

// Ensure user is logged in
if (!isset($_SESSION['admin'])) {
    echo '<script>alert("Please login to view bookings."); window.location.href="admin-login.php";</script>';
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

<div class="container">
    <h2>All Canceled & Completed Bookings</h2>
    <div class="booking-list">
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                // Assign status class
                $statusClass = strtolower($row["status"]) === "completed" ? "status-completed" : "status-canceled";
            ?>
                <div class="booking-card">
                    <?php if (!empty($row['service_image'])) { ?>
                        <img src="<?php echo htmlspecialchars($row['service_image']); ?>" alt="Service Image" class="service-img">
                    <?php } ?>
                    <div class="booking-info">
                        <h3>Customer Name: <?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><strong>Service Type:</strong> <?php echo htmlspecialchars($row['service_name']); ?></p>
                        <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($row['date'])); ?> -
                        <strong>Time:</strong> <?php echo date("h:i A", strtotime($row['booking_time'])); ?></p>
                        <p><strong>Price:</strong> ₱<?php echo number_format($row['service_price'], 2); ?></p>
                    </div>
                    <span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No canceled or completed bookings found.</p>
        <?php } ?>
    </div>
</div>
