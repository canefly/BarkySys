<?php 
include_once __DIR__ . '/admin-navigation.php';
include_once __DIR__ . '/db.php'; // Database connection

// Handle status update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookingId = intval($_POST['booking_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $updateQuery = "UPDATE bookings SET status = '$status' WHERE id = $bookingId";
    if (!mysqli_query($conn, $updateQuery)) {
        echo "<p style='color:red;'>Error updating booking: " . mysqli_error($conn) . "</p>";
    }
}

// Fetch only approved bookings WITH service_image
$query = "
    SELECT b.*, s.service_image 
    FROM bookings b
    JOIN services s ON b.service_name = s.service_name
    WHERE b.status = 'approved'
    ORDER BY b.date DESC
";

$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h2>Approved Appointments</h2>
    <div class="booking-list">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="booking-card">
                <!-- Display Service Image -->
                <?php if (!empty($row['service_image'])) { ?>
                    <img src="<?php echo htmlspecialchars(str_replace('\\', '/', $row['service_image'])); ?>" alt="Service Image" class="service-img">
                <?php } ?>

                <div class="booking-info">
                    <h3>Customer Name: <?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><strong>Service:</strong> <?php echo htmlspecialchars($row['service_name']); ?></p>
                    <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($row['date'])); ?> - 
                       <strong>Time:</strong> <?php echo date("h:i A", strtotime($row['booking_time'])); ?></p>
                    <p><strong>Price:</strong> ₱<?php echo number_format($row['service_price'], 2); ?></p>
                </div>

                <div class="booking-actions">
                    <form method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="status" value="canceled" class="btn btn-cancel">Cancel</button>
                        <button type="submit" name="status" value="completed" class="btn btn-complete">Complete</button>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
