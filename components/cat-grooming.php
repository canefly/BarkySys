<?php include 'user-navigation.php'; ?>
<?php include 'db.php'; // Include database connection ?>

<div class="container">
    <h2 style="color: #6E3387; text-align: center; font-weight: 600;">Cat Grooming Services</h2>
    <div class="services">
        <?php
        $query = "SELECT * FROM services WHERE service_type = 'HairColor'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="service-card" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; display: flex; flex-direction: column; align-items: center; width: 300px; transition: transform 0.3s;">
                    <img src="<?php echo htmlspecialchars($row['service_image']); ?>" alt="<?php echo htmlspecialchars($row['service_name']); ?>" style="width: 80px; height: 80px; margin-bottom: 15px; object-fit: contain;">
                    <h3 style="color: #6E3387; font-size: 20px; font-weight: 600; margin: 10px 0;"><?php echo htmlspecialchars($row['service_name']); ?></h3>
                    <p style="color: #777; font-size: 14px;"><?php echo htmlspecialchars($row['service_description']); ?></p>
                    <p><strong>₱<?php echo number_format($row['service_price'], 2); ?></strong></p>
                    <a href="bookingform.php?id=<?php echo $row['id']; ?>" class="book-btn" style="margin-top: 15px; padding: 12px 18px; background: #D6BE3E; color: #333; border-radius: 8px; font-weight: 500; text-decoration: none; transition: background 0.3s; border: none; display: inline-block;" onmouseover="this.style.background='#C5A634'" onmouseout="this.style.background='#D6BE3E'">Book Now</a>
                </div>
                <?php
            }
        } else {
            echo '<p style="color: #6E3387;">No cat grooming services available.</p>';
        }
        ?>
    </div>
</div>
