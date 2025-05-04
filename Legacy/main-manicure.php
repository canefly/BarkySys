<?php include 'user-navigation.php'; ?>
<?php include 'db.php'; // Include database connection ?>

<div class="container">
    <h2>Our Haircut Services</h2>
    <div class="services">
        <?php
        // Fetch only Haircut services
        $query = "SELECT * FROM services WHERE service_type = 'Manicure'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="service-card">';
                echo '<img src="' . htmlspecialchars($row['service_image']) . '" alt="' . htmlspecialchars($row['service_name']) . '">';
                echo '<h3>' . htmlspecialchars($row['service_name']) . '</h3>';
                echo '<p>' . htmlspecialchars($row['service_description']) . '</p>';
                echo '<p><strong>₱' . number_format($row['service_price'], 2) . '</strong></p>';
                echo '<a href="bookingform.php?id=' . $row['id'] . '" class="book-btn">Book Now</a>';

                echo '</div>';
            }
        } else {
            echo '<p>No haircut services available.</p>';
        }
        ?>
    </div>
</div>
