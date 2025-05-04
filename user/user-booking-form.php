<?php
session_start();
include_once 'user-navigation.php';
include_once '../db.php';

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in first!'); window.location.href='user-login.php';</script>";
    exit();
}

$loggedInEmail = $_SESSION['email'];
$loggedInName = $_SESSION['name'] ?? '';

// Get service details
$serviceId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($serviceId > 0) {
    $query = "SELECT * FROM services WHERE id = $serviceId";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $service = mysqli_fetch_assoc($result);
        $serviceName = $service['service_name'];
        $servicePrice = $service['service_price'];
        $serviceImage = '../' . ltrim($service['service_image'], '/');
    } else {
        echo "<p>Service not found.</p>";
        exit();
    }
} else {
    echo "<p>No service selected.</p>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $paymentMethod = mysqli_real_escape_string($conn, $_POST['payment-method']);
    $paymentNumber = isset($_POST['payment-number']) ? mysqli_real_escape_string($conn, $_POST['payment-number']) : NULL;

    $downPayment = $servicePrice * 0.25;
    $balance = $servicePrice - $downPayment;

    $checkQuery = "SELECT COUNT(*) AS total_bookings FROM bookings WHERE date = '$date'";
    $checkResult = mysqli_query($conn, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['total_bookings'] >= 20) {
        echo "<script>alert('Booking limit reached for this date. Please select another day.'); window.location.href='user-hp.php';</script>";
        exit();
    }

    $insertQuery = "INSERT INTO bookings (service_name, service_price, name, email, date, booking_time, payment_method, payment_number, balance, status) 
                    VALUES ('$serviceName', '$servicePrice', '$name', '$loggedInEmail', '$date', '$time', '$paymentMethod', '$paymentNumber', '$balance', 'pending')";

    if (mysqli_query($conn, $insertQuery)) {
        echo "<script>alert('Booking Confirmed!'); window.location.href='user-bl.php';</script>";
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: rgb(236, 227, 218);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: margin-left 0.3s ease-in-out;
        }

        .container {
            display: flex;
            max-width: 900px;
            width: 100%;
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .selected-service {
            width: 40%;
            padding: 20px;
            text-align: center;
        }

        .selected-service img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .selected-service p {
            margin-top: 10px;
            font-weight: bold;
            color: rgb(235, 7, 7);
        }

        .booking-form {
            width: 60%;
            padding: 20px;
        }

        .booking-form h2 {
            margin-bottom: 10px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .payment-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .payment-option input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .payment-option img {
            width: 50px;
            height: auto;
        }

        .submit-btn {
            width: 100%;
            padding: 10px;
            background: #7a6f5d;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background: #5f5445;
        }

        body.sidebar-open {
            margin-left: 250px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="selected-service">
        <h2>Selected Service</h2>
        <img src="<?php echo htmlspecialchars($serviceImage); ?>" alt="<?php echo htmlspecialchars($serviceName); ?>">
        <h3><?php echo htmlspecialchars($serviceName); ?></h3>
        <p>Price: ₱<?php echo number_format($servicePrice, 2); ?></p>

        <?php if (!empty($loggedInName)) {
            $downPayment = $servicePrice * 0.25;
            $balance = $servicePrice - $downPayment;
            $dueDate = date('F j, Y', strtotime('-1 day', strtotime($_POST['date'] ?? date('Y-m-d')))); ?>
            <p class="payment-notice">
                Dear <?php echo htmlspecialchars($loggedInName); ?>, please pay 25% (₱<?php echo number_format($downPayment, 2); ?>)
                by <?php echo $dueDate; ?> to confirm your booking.
                Remaining Balance: ₱<?php echo number_format($balance, 2); ?>
            </p>
        <?php } ?>
    </div>

    <div class="booking-form">
        <h2>Book Your Appointment</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($loggedInName); ?>" required readonly>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($loggedInEmail); ?>" required readonly>
            </div>

            <div class="form-group">
                <label for="date">Preferred Date:</label>
                <input type="date" id="date" name="date" required onchange="updateDueDate()">
            </div>

            <div class="form-group">
                <label for="time">Preferred Time:</label>
                <select id="time" name="time">
                    <option value="10:00 AM">10:00 AM</option>
                    <option value="11:00 AM">11:00 AM</option>
                    <option value="1:00 PM">1:00 PM</option>
                    <option value="2:00 PM">2:00 PM</option>
                    <option value="3:00 PM">3:00 PM</option>
                </select>
            </div>

            <div class="form-group">
                <label for="payment-method">Payment Method</label>
                <div class="payment-container">
                    <label class="payment-option">
                        <input type="radio" name="payment-method" value="GCash" required>
                        <img src="../img/gcashlogo.png" alt="GCash Logo">
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment-method" value="Paymaya" required>
                        <img src="../img/Mayalogo.png" alt="Paymaya Logo">
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="payment-number">Account Number</label>
                <input type="text" maxlength="11" pattern="\d{11}" id="payment-number" name="payment-number" placeholder="Enter Here..." required>
            </div>

            <button type="submit" class="submit-btn">Confirm Booking</button>
        </form>
    </div>
</div>

<script>
function updateDueDate() {
    const dateInput = document.getElementById('date').value;
    if (dateInput) {
        const dueDate = new Date(dateInput);
        dueDate.setDate(dueDate.getDate() - 1);
        const formattedDate = dueDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

        const notice = document.querySelector('.payment-notice');
        if (notice) {
            notice.innerHTML = `Dear <?php echo htmlspecialchars($loggedInName); ?>, please pay 25% (₱<?php echo number_format($servicePrice * 0.25, 2); ?>) by ${formattedDate} to confirm your booking.
            Remaining Balance: ₱<?php echo number_format($servicePrice - ($servicePrice * 0.25), 2); ?>.`;
        }
    }
}
</script>

</body>
</html>
