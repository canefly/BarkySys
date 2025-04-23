<?php
include 'C:\xampp\htdocs\IM\db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['service_id'])) {
    $service_id = mysqli_real_escape_string($conn, $_POST['service_id']);

    // Fetch the image path to delete from the folder
    $query = "SELECT service_image FROM services WHERE id='$service_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $image_path = $row['service_image'];

        // Delete the record from the database
        $delete_query = "DELETE FROM services WHERE id='$service_id'";
        if (mysqli_query($conn, $delete_query)) {
            // Remove the image file from the server
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            echo '<script>alert("Service deleted successfully!"); window.location.href="services-list.php";</script>';
        } else {
            echo '<script>alert("Error deleting service."); window.location.href="services-list.php";</script>';
        }
    }
}
?>


<?php 
include 'C:\xampp\htdocs\IM\admin-navigation.php'; 
include 'C:\xampp\htdocs\IM\db.php'; // Include database connection
?>

<div class="container">
    <h2>Services List</h2>
    <div class="service-list" id="service-list">

        <?php
        $query = "SELECT * FROM services ORDER BY id DESC"; // Fetch all services
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="service-card">
                    <img src="<?php echo $row['service_image']; ?>" alt="Service Image" width="80">
                    <div class="service-info">
                        <h3><?php echo htmlspecialchars($row['service_name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['service_description']); ?></p>
                        <p><strong>₱<?php echo number_format($row['service_price'], 2); ?></strong></p>
                    </div>
                    <form action="services-list.php" method="POST" style="display:inline;">
                        <input type="hidden" name="service_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo "<p>No services available.</p>";
        }
        ?>

    </div>
</div>
