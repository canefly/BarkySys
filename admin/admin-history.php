<?php
include_once 'admin-auth.php';
include_once 'admin-navigation.php';
include_once '../db.php';

// Fetch all canceled and completed bookings
$query = "
    SELECT b.*, u.email, s.service_image
    FROM bookings b
    JOIN users u    ON b.email = u.email
    JOIN services s ON b.service_name = s.service_name
    WHERE TRIM(LOWER(b.status)) IN ('canceled','completed')
    ORDER BY b.date DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Canceled &amp; Completed Bookings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

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
    }
    h2 {
      font-weight: bold;
    }
    .service-img {
      max-width: 100px;
      max-height: 100px;
      object-fit: cover;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="container-fluid py-4">
    <h2 class="text-center mb-4">All Canceled &amp; Completed Bookings</h2>
    <div class="row g-3">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)):
          $status = strtolower(trim($row['status']));
          $badgeClass = $status === 'completed' ? 'bg-success' : 'bg-danger';
          $imagePath = '../' . ltrim($row['service_image'], '/');
        ?>
          <div class="col-12">
            <div class="card shadow-sm">
              <div class="row g-0 align-items-center">
                
                <?php if (!empty($row['service_image'])): ?>
                  <div class="col-auto p-3">
                    <img
                      src="<?php echo htmlspecialchars($imagePath); ?>"
                      alt="Service Image"
                      class="service-img img-fluid"
                    >
                  </div>
                <?php endif; ?>

                <div class="col p-3">
                  <h5 class="card-title fw-bold mb-2">
                    Customer: <?php echo htmlspecialchars($row['name']); ?>
                  </h5>
                  <p class="mb-1">
                    <strong>Service:</strong>
                    <?php echo htmlspecialchars($row['service_name']); ?>
                  </p>
                  <p class="mb-1">
                    <strong>Date:</strong>
                    <?php echo date("F j, Y", strtotime($row['date'])); ?>
                    &mdash;
                    <strong>Time:</strong>
                    <?php echo date("h:i A", strtotime($row['booking_time'])); ?>
                  </p>
                  <p class="mb-1">
                    <strong>Email:</strong>
                    <?php echo htmlspecialchars($row['email']); ?>
                  </p>
                  <p class="mb-0">
                    <strong>Price:</strong>
                    ₱<?php echo number_format($row['service_price'], 2); ?>
                  </p>
                </div>

                <div class="col-auto p-3 text-center">
                  <span class="badge rounded-pill <?php echo $badgeClass; ?>">
                    <?php echo ucfirst($status); ?>
                  </span>
                </div>

              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center">No canceled or completed bookings found.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
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
