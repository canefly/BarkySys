<?php
session_start();
include_once 'admin-navigation.php';
include_once '../db.php';
include_once '../helpers/path-helper.php';

// Drop DELETE triggers to avoid missing procedure error
$triggerRes = mysqli_query($conn, "SHOW TRIGGERS FROM barksys_db WHERE `Table`='services' AND `Event`='DELETE'");
while ($tr = mysqli_fetch_assoc($triggerRes)) {
    mysqli_query($conn, "DROP TRIGGER IF EXISTS `{$tr['Trigger']}`");
}

// Handle deletion submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'])) {
    $service_id = mysqli_real_escape_string($conn, $_POST['service_id']);

    // Fetch image path from DB
    $res = mysqli_query($conn, "SELECT service_image FROM services WHERE id='$service_id'");
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $img = $row['service_image'];
        $resolvedPath = resolveUploadPath($img);

        // Delete service row
        if (mysqli_query($conn, "DELETE FROM services WHERE id='$service_id'")) {
            if ($img && $resolvedPath && file_exists($resolvedPath)) {
                unlink($resolvedPath);
            }
            echo '<script>alert("Service deleted successfully!"); window.location.href="admin-services-list.php";</script>';
            exit;
        } else {
            echo '<script>alert("Error deleting service."); window.location.href="admin-services-list.php";</script>';
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bark &amp; Wiggle – Services List</title>
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
      transition: margin-left 0.3s ease-in-out;
    }
    h2 {
      font-weight: bold;
    }
    .card {
      border-radius: 8px;
    }
    .service-img {
      max-width: 80px;
      max-height: 80px;
      object-fit: cover;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="container-fluid py-4">
    <h2 class="text-center mb-4">Services List</h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php
      $list = $conn->query("SELECT id, service_name, service_description, service_price, service_image FROM services ORDER BY id DESC");
      if ($list->num_rows > 0):
        while ($row = $list->fetch_assoc()):
          $imagePath = '../' . ltrim($row['service_image'], '/');
      ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <div class="row g-0 align-items-center">
            <div class="col-auto p-3">
              <img
                src="<?php echo htmlspecialchars($imagePath); ?>"
                alt="Service Image"
                class="service-img img-fluid"
              >
            </div>
            <div class="col">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title mb-2 fw-bold">
                  <?php echo htmlspecialchars($row['service_name']); ?>
                </h5>
                <p class="card-text flex-grow-1">
                  <?php echo htmlspecialchars($row['service_description']); ?>
                </p>
                <p class="card-text mb-3">
                  <strong>₱<?php echo number_format($row['service_price'], 2); ?></strong>
                </p>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                  <input type="hidden" name="service_id" value="<?php echo (int)$row['id']; ?>">
                  <button type="submit" class="btn btn-danger btn-sm">
                    Delete
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php
        endwhile;
      else:
      ?>
        <p class="text-center">No services available.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Bootstrap JS Bundle (with Popper) -->
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
