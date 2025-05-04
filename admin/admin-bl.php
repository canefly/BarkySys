<?php 
include_once 'admin-navigation.php'; 
include_once '../db.php'; // Database connection

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

    $updateQuery = "UPDATE bookings SET status = '$status' WHERE id = $bookingId";
    
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Booking status updated!'); window.location.href='admin-bl.php';</script>";
    } else {
        echo "<p>Error updating status: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Booking List</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color:rgb(236, 227, 218);
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      transition: margin-left 0.3s ease-in-out;
    }

    .container {
      width: 100vw;
      margin-left: 0;
      padding: 20px;
      transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    }

    body.sidebar-open .container {
      width: calc(100vw - 250px);
      margin-left: 250px;
    }

    h2 {
      text-align: center;
      font-weight: 600;
    }

    .service-img {
      width: 100px;
      height: 100px;
      border-radius: 8px;
      object-fit: cover;
      margin-right: 15px;
    }

    .booking-list {
      display: flex;
      flex-direction: column;
      gap: 15px;
      width: 90%;
      max-width: 1200px;
      margin: auto;
      margin-top: 20px;
    }

    .booking-card {
      background: #fff;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: 0.3s;
      flex-wrap: wrap;
      width: 100%;
    }

    .booking-card:hover {
      transform: translateY(-3px);
    }

    .booking-info {
      display: flex;
      flex-direction: column;
      gap: 5px;
      flex: 1;
    }

    .booking-info h3 {
      margin: 0;
      color: #333;
      font-size: 18px;
    }

    .booking-info p {
      margin: 0;
      color: #777;
      font-size: 14px;
    }

    .booking-actions {
      display: flex;
      gap: 10px;
    }

    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      font-size: 14px;
      transition: 0.3s;
    }

    .btn-accept {
      background: rgb(175, 162, 43);
      color: white;
    }

    .btn-accept:hover {
      background: rgb(202, 200, 54);
    }

    .btn-cancel {
      background: #e74c3c;
      color: white;
    }

    .btn-cancel:hover {
      background: #c0392b;
    }

    .btn-complete {
      background: rgb(47, 146, 60);
      color: white;
    }

    .btn-complete:hover {
      background: rgb(53, 185, 71);
    }

    @media (max-width: 768px) {
      .container {
        width: 100vw;
        margin-left: 0;
      }

      .booking-list {
        width: 95%;
      }

      .booking-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
      }

      .booking-actions {
        margin-top: 10px;
        width: 100%;
        display: flex;
        justify-content: center;
      }
    }
  </style>
</head>
<body>

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
            <p><strong>Scheduled on:</strong> <?php echo date("F j, Y", strtotime($row['date'])); ?> -
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

  <script>
    function toggleMenu() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const body = document.body;

      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
      body.classList.toggle("sidebar-open");
    }
  </script>

</body>
</html>
