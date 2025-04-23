<?php 
include 'C:\xampp\htdocs\IM\admin-navigation.php'; 
include 'C:\xampp\htdocs\IM\db.php'; // Database connection

$query = "
    SELECT b.*, s.service_image 
    FROM bookings b
    JOIN services s ON b.service_name = s.service_name
    WHERE b.status = 'pending'
    ORDER BY b.date DESC
";

$result = mysqli_query($conn, $query);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookingId = intval($_POST['booking_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Update booking status
    $updateQuery = "UPDATE bookings SET status = '$status' WHERE id = $bookingId";
    
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Booking status updated!'); window.location.href='admin-bl.php';</script>";
    } else {
        echo "<p>Error updating status: " . mysqli_error($conn) . "</p>";
    }
}
?>

<div class="container">
    <h2>Bookings List</h2>
    <div class="booking-list">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="booking-card">
                <?php if (!empty($row['service_image'])) { ?>
                    <img src="<?php echo htmlspecialchars($row['service_image']); ?>" alt="Service Image" class="service-img">
                <?php } ?>
                <div class="booking-info">
                    <h3>Customer Name: <?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><strong>Service Type:</strong> <?php echo htmlspecialchars($row['service_name']); ?></p>
                    <p><strong>Scheduled on</strong> <?php echo date("F j, Y", strtotime($row['date'])); ?> -
                    <strong>Time:</strong> <?php echo date("h:i A", strtotime($row['booking_time'])); ?></p>
                    <p><strong>Price:</strong> ₱<?php echo number_format($row['service_price'], 2); ?></p>
                </div>
                <div class="booking-actions">
                    <form method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="status" value="approved" class="btn btn-accept">Accept</button>
                        <button type="submit" name="status" value="canceled" class="btn btn-cancel">Cancel</button>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
