<?php
include_once 'admin-navigation.php';
include_once '../db.php'; // Database connection

// Handle status update (if ever used)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookingId   = intval($_POST['booking_id']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);
    $updateQuery = "UPDATE bookings SET status = '$status' WHERE id = $bookingId";
    mysqli_query($conn, $updateQuery);
}

// Fetch statistics
$totalCustomersQuery    = "SELECT COUNT(DISTINCT name) AS total FROM bookings";
$totalAppointmentsQuery = "SELECT COUNT(*) AS total FROM bookings WHERE status IN ('pending','approved')";
$totalServicesQuery     = "SELECT COUNT(*) AS total FROM services";

$totalCustomers    = mysqli_fetch_assoc(mysqli_query($conn, $totalCustomersQuery))['total'];
$totalAppointments = mysqli_fetch_assoc(mysqli_query($conn, $totalAppointmentsQuery))['total'];
$totalServices     = mysqli_fetch_assoc(mysqli_query($conn, $totalServicesQuery))['total'];

// Completed bookings per month
$completedBookings = array_fill(0, 12, 0);
$monthlyQuery      = "SELECT MONTH(date) AS month, COUNT(*) AS count 
                      FROM bookings WHERE status = 'completed' 
                      GROUP BY MONTH(date)";
$monthlyResult     = mysqli_query($conn, $monthlyQuery);
while ($row = mysqli_fetch_assoc($monthlyResult)) {
    $completedBookings[$row['month'] - 1] = $row['count'];
}

// Bookings per service
$serviceBookings = [];
$serviceQuery    = "SELECT service_name, COUNT(*) AS count FROM bookings GROUP BY service_name";
$serviceResult   = mysqli_query($conn, $serviceQuery);
while ($row = mysqli_fetch_assoc($serviceResult)) {
    $serviceBookings[$row['service_name']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bark&Wiggle Dashboard</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      font-family: "Helvetica", Arial, sans-serif;
      background-color: #ECE3DA;
    }
    h2, h5 {
      font-family: "Helvetica", Arial, sans-serif;
      font-weight: bold;
    }
    .card {
      border-radius: 8px;
    }
    .card-chart canvas {
      max-height: 250px;
      width: 100% !important;
    }
  </style>
</head>
<body>
  <div class="container-fluid py-4">
    <h2 class="text-center mb-4">Welcome to the Bark&amp;Wiggle Dashboard</h2>

    <!-- Stats Row -->
    <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
      <div class="col">
        <div class="card shadow-sm text-center p-3">
          <h5>Total Customers</h5>
          <p class="display-6 mb-0"><?php echo $totalCustomers; ?></p>
        </div>
      </div>
      <div class="col">
        <div class="card shadow-sm text-center p-3">
          <h5>Upcoming Appointments</h5>
          <p class="display-6 mb-0"><?php echo $totalAppointments; ?></p>
        </div>
      </div>
      <div class="col">
        <div class="card shadow-sm text-center p-3">
          <h5>Services Offered</h5>
          <p class="display-6 mb-0"><?php echo $totalServices; ?></p>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row row-cols-1 row-cols-lg-2 g-4">
      <div class="col">
        <div class="card shadow-sm card-chart p-3">
          <h5 class="text-center mb-3">Completed Bookings (Monthly)</h5>
          <canvas id="completedBookingsChart"></canvas>
        </div>
      </div>
      <div class="col">
        <div class="card shadow-sm card-chart p-3">
          <h5 class="text-center mb-3">Bookings by Service</h5>
          <canvas id="serviceBookingsChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Bar chart: Completed bookings
      new Chart(
        document.getElementById('completedBookingsChart').getContext('2d'),
        {
          type: 'bar',
          data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
              label: 'Completed',
              data: <?php echo json_encode($completedBookings); ?>,
              backgroundColor: 'rgba(47,146,60,0.6)',
              borderColor: 'rgba(47,146,60,1)',
              borderWidth: 1,
              borderRadius: 5
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: {
                  label: (ctx) => ctx.raw + ' bookings'
                }
              }
            },
            scales: {
              y: { beginAtZero: true }
            }
          }
        }
      );

      // Pie chart: Service bookings
      new Chart(
        document.getElementById('serviceBookingsChart').getContext('2d'),
        {
          type: 'pie',
          data: {
            labels: <?php echo json_encode(array_keys($serviceBookings)); ?>,
            datasets: [{
              data: <?php echo json_encode(array_values($serviceBookings)); ?>,
              backgroundColor: [
                'rgba(255,99,132,0.7)',
                'rgba(54,162,235,0.7)',
                'rgba(255,206,86,0.7)',
                'rgba(75,192,192,0.7)',
                'rgba(153,102,255,0.7)',
                'rgba(255,159,64,0.7)'
              ],
              borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54,162,235,1)',
                'rgba(255,206,86,1)',
                'rgba(75,192,192,1)',
                'rgba(153,102,255,1)',
                'rgba(255,159,64,1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'bottom' },
              tooltip: {
                callbacks: {
                  label: (ctx) => ctx.raw + ' bookings'
                }
              }
            }
          }
        }
      );
    });
  </script>
</body>
</html>
