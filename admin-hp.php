<?php 
include 'admin-navigation.php'; 
include 'db.php'; // Database connection

// Handle status update (if ever used)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookingId = intval($_POST['booking_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $updateQuery = "UPDATE bookings SET status = '$status' WHERE id = $bookingId";
    mysqli_query($conn, $updateQuery);
}

// Fetch statistics
$totalCustomersQuery = "SELECT COUNT(DISTINCT name) AS total FROM bookings";
$totalAppointmentsQuery = "SELECT COUNT(*) AS total FROM bookings WHERE status IN ('pending', 'approved')";
$totalServicesQuery = "SELECT COUNT(*) AS total FROM services";

$totalCustomers = mysqli_fetch_assoc(mysqli_query($conn, $totalCustomersQuery))['total'];
$totalAppointments = mysqli_fetch_assoc(mysqli_query($conn, $totalAppointmentsQuery))['total'];
$totalServices = mysqli_fetch_assoc(mysqli_query($conn, $totalServicesQuery))['total'];

// Completed bookings per month
$completedBookings = array_fill(0, 12, 0);
$monthlyQuery = "SELECT MONTH(date) AS month, COUNT(*) AS count FROM bookings WHERE status = 'completed' GROUP BY MONTH(date)";
$monthlyResult = mysqli_query($conn, $monthlyQuery);
while ($row = mysqli_fetch_assoc($monthlyResult)) {
    $completedBookings[$row['month'] - 1] = $row['count'];
}

// Bookings per service
$serviceBookings = [];
$serviceQuery = "SELECT service_name, COUNT(*) AS count FROM bookings GROUP BY service_name";
$serviceResult = mysqli_query($conn, $serviceQuery);
while ($row = mysqli_fetch_assoc($serviceResult)) {
    $serviceBookings[$row['service_name']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Salon Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: rgb(236, 227, 218);
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      transition: margin-left 0.3s ease-in-out;
    }

    .main-content {
      flex: 1;
      padding: 20px;
      margin-top: 4em;
      margin-left: 50px;
      transition: margin-left 0.3s ease-in-out;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .header {
      width: 100%;
      text-align: center;
    }

    .stats-container {
      display: flex;
      gap: 20px;
      width: 100%;
    }

    .stat-card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      flex: 1;
      text-align: center;
      font-size: 1.2em;
    }

    .chart-container,
    .pie-chart-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .chart-container {
      width: 50%;
      max-width: 800px;
    }

    .pie-chart-container {
      width: 30%;
      max-width: 400px;
    }
  </style>
</head>
<body>

  <div class="main-content">
    <div class="header">
      <h2>Welcome to the Salon Dashboard</h2>
    </div>

    <div class="stats-container">
      <div class="stat-card">
        <h3>Total Customers</h3>
        <p><?php echo $totalCustomers; ?></p>
      </div>
      <div class="stat-card">
        <h3>Upcoming Appointments</h3>
        <p><?php echo $totalAppointments; ?></p>
      </div>
      <div class="stat-card">
        <h3>Services Offered</h3>
        <p><?php echo $totalServices; ?></p>
      </div>
    </div>

    <div class="chart-container">
      <h2>Completed Bookings (%)</h2>
      <canvas id="completedBookingsChart"></canvas>
    </div>

    <div class="pie-chart-container">
      <h2>Bookings by Service</h2>
      <canvas id="serviceBookingsChart"></canvas>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Completed Bookings Chart
      const completedCtx = document.getElementById('completedBookingsChart').getContext('2d');
      new Chart(completedCtx, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          datasets: [{
            label: 'Completed Bookings (%)',
            data: <?php echo json_encode($completedBookings); ?>,
            backgroundColor: 'rgba(31, 181, 28, 0.6)',
            borderColor: 'rgb(90, 235, 54)',
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
                label: function (tooltipItem) {
                  return tooltipItem.raw + ' Bookings';
                }
              }
            }
          },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });

      // Service Bookings Chart
      const serviceCtx = document.getElementById('serviceBookingsChart').getContext('2d');
      new Chart(serviceCtx, {
        type: 'pie',
        data: {
          labels: <?php echo json_encode(array_keys($serviceBookings)); ?>,
          datasets: [{
            label: 'Service Bookings',
            data: <?php echo json_encode(array_values($serviceBookings)); ?>,
            backgroundColor: [
              'rgba(255, 99, 132, 0.7)',
              'rgba(54, 162, 235, 0.7)',
              'rgba(255, 206, 86, 0.7)',
              'rgba(75, 192, 192, 0.7)',
              'rgba(153, 102, 255, 0.7)',
              'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
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
                label: function (tooltipItem) {
                  return tooltipItem.raw + ' Bookings';
                }
              }
            }
          }
        }
      });
    });
  </script>
</body>
</html>
