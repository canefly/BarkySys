<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'user-navigation.php';
include('db.php');

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    echo '<script>alert("Please login to view your bookings."); window.location.href="login.php";</script>';
    exit();
}

$userEmail = $_SESSION['username']; // Get logged-in user's email

// Fetch bookings where email matches username
$query = "
    SELECT b.*, u.username, s.service_image
    FROM bookings b
    JOIN users u ON b.email = u.username 
    JOIN services s ON b.service_name = s.service_name 
    WHERE b.email = ?
    ORDER BY b.date DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $userEmail);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container">
    <h2>My Bookings</h2>
    <div class="booking-list">
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                // Determine status class for styling
                $statusClass = strtolower($row["status"]) === "approved" ? "status-approved" :
                               (strtolower($row["status"]) === "pending" ? "status-pending" : 
                               (strtolower($row["status"]) === "completed" ? "status-completed" : "status-canceled"));
            ?>
                <div class="booking-card">
                    <?php if (!empty($row['service_image'])) { ?>
                        <?php $imagePath = '../' . ltrim($row['service_image'], '/'); ?>
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Service Image" class="service-img">
                    <?php } ?>
                    <div class="booking-info">
                        <h3>Customer Name: <?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><strong>Service Type:</strong> <?php echo htmlspecialchars($row['service_name']); ?></p>
                        <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($row['date'])); ?> -
                        <strong>Time:</strong> <?php echo date("h:i A", strtotime($row['booking_time'])); ?></p>
                        <p><strong>Price:</strong> ₱<?php echo number_format($row['service_price'], 2); ?></p>
                    </div>
                    
                    <!-- Status Display -->
                    <div class="status-container">
                        <span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span>

                        <!-- Show receipt button only for approved bookings -->
                        <?php if (strtolower($row["status"]) === "approved") { ?>
                            <a href="receipt.php?booking_id=<?php echo $row['id']; ?>" class="receipt-btn">Download Receipt</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No bookings found.</p>
        <?php } ?>
    </div>
</div>

<style>
/* Status container to keep status and button together */
.status-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 10px;
}

/* Style for the receipt button */
.receipt-btn {
    display: inline-block;
    margin-top: 8px;
    padding: 6px 10px;
    background-color:rgb(108, 116, 110);
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    font-size: 14px;
    text-align: center;
}

.receipt-btn:hover {
    background-color:rgb(81, 86, 82);
}
</style>
