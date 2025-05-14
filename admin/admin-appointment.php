<?php
include_once 'admin-navigation.php';
include_once '../db.php'; // Database connection

// Handle status update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookingId   = intval($_POST['booking_id']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);
    $updateQuery = "UPDATE bookings SET status = '$status' WHERE id = $bookingId";
    if (!mysqli_query($conn, $updateQuery)) {
        echo "<p class='alert alert-danger'>Error updating booking: " . mysqli_error($conn) . "</p>";
    }
}

// Fetch approved bookings with service images
$query  = "
    SELECT b.*, s.service_image
    FROM bookings b
    JOIN services s ON b.service_name = s.service_name
    WHERE b.status = 'approved'
    ORDER BY b.date DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Approved Appointments</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >

  <style>
    body {
      font-family: "Helvetica", Arial, sans-serif;
      background-color: #ECE3DA;
      margin: 0;
      padding: 0;
      transition: margin-left 0.3s ease-in-out;
    }
    h2, h5 {
      font-family: "Helvetica", Arial, sans-serif;
      font-weight: bold;
    }
    .card {
      border-radius: 8px;
    }
    .service-img {
      max-height: 100px;
      object-fit: cover;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="container-fluid py-4">
    <h2 class="text-center mb-4">Approved Appointments</h2>

    <div class="row g-3">
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <?php
          $imagePath = '../' . ltrim($row['service_image'], '/');
          $hasImage  = !empty($row['service_image']);
        ?>
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="row g-0 align-items-center">
              
              <?php if ($hasImage): ?>
                <div class="col-auto p-3">
                  <img
                    src="<?php echo htmlspecialchars($imagePath); ?>"
                    alt="Service Image"
                    class="service-img"
                  >
                </div>
              <?php endif; ?>

              <div class="col p-3">
                <h5 class="card-title mb-2">Customer: <?php echo htmlspecialchars($row['name']); ?></h5>
                <p class="mb-1">
                  <strong>Service:</strong> <?php echo htmlspecialchars($row['service_name']); ?>
                </p>
                <p class="mb-1">
                  <strong>Date:</strong>
                  <?php echo date("F j, Y", strtotime($row['date'])); ?>
                  &mdash;
                  <strong>Time:</strong>
                  <?php echo date("h:i A", strtotime($row['booking_time'])); ?>
                </p>
                <p class="mb-0">
                  <strong>Price:</strong> ₱<?php echo number_format($row['service_price'], 2); ?>
                </p>
              </div>

              <div class="col-auto p-3 text-center">
                <form method="POST">
                  <input
                    type="hidden"
                    name="booking_id"
                    value="<?php echo $row['id']; ?>"
                  >
                  <button
                    type="submit"
                    name="status"
                    value="canceled"
                    class="btn btn-danger btn-sm w-100 mb-2"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    name="status"
                    value="completed"
                    class="btn btn-success btn-sm w-100"
                  >
                    Complete
                  </button>
                </form>
              </div>

            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Bootstrap JS Bundle (includes Popper) -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
  <script>
    function toggleMenu() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      document.body.classList.toggle('sidebar-open');
      sidebar?.classList.toggle('show');
      overlay?.classList.toggle('show');
    }
  </script>
</body>
</html>
