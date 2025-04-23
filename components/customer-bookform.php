<?php
session_start();
include 'user-navigation.php';
include 'db.php';

if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in first!'); window.location.href='login.php';</script>";
    exit();
}

// Get user details from session
$loggedInEmail = $_SESSION['username']; 
$loggedInName = $_SESSION['name']; 

// Get service ID from URL
$serviceId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch selected service details
if ($serviceId > 0) {
    $query = "SELECT * FROM services WHERE id = $serviceId";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $service = mysqli_fetch_assoc($result);
        $serviceName = $service['service_name'];
        $servicePrice = $service['service_price'];
        $serviceImage = $service['service_image'];
    } else {
        echo "<p>Service not found.</p>";
        exit();
    }
} else {
    echo "<p>No service selected.</p>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $paymentMethod = mysqli_real_escape_string($conn, $_POST['payment-method']);
    $paymentNumber = isset($_POST['payment-number']) ? mysqli_real_escape_string($conn, $_POST['payment-number']) : NULL;
    
    // Calculate Downpayment and Balance
    $downPayment = $servicePrice * 0.25;
    $balance = $servicePrice - $downPayment;
    
    // Check the number of bookings for the selected date
    $checkQuery = "SELECT COUNT(*) AS total_bookings FROM bookings WHERE date = '$date'";
    $checkResult = mysqli_query($conn, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);
    
    if ($row['total_bookings'] >= 20) {
        echo "<script>alert('Booking limit reached for this date. Please select another day.'); window.location.href='Customer-hp.php';</script>";
        exit();
    }

    // Insert booking data with balance
    $insertQuery = "INSERT INTO bookings (service_name, service_price, name, email, date, booking_time, payment_method, payment_number, balance, status) 
                    VALUES ('$serviceName', '$servicePrice', '$name', '$loggedInEmail', '$date', '$time', '$paymentMethod', '$paymentNumber', '$balance', 'pending')";

    if (mysqli_query($conn, $insertQuery)) {
        echo "<script>alert('Booking Confirmed!'); window.location.href='Customer-bl.php';</script>";
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}


?>

<div class="container">
    <!-- Selected Service -->
    <div class="selected-service">
    <h2>Selected Service</h2>
    <img src="<?php echo htmlspecialchars($serviceImage); ?>" alt="<?php echo htmlspecialchars($serviceName); ?>" style="max-width: 300px; border-radius: 10px;">
    <h3><?php echo htmlspecialchars($serviceName); ?></h3>
    <p><strong>Price: ₱<?php echo number_format($servicePrice, 2); ?></strong></p>

    <!-- Downpayment Notice -->
    <?php if (!empty($loggedInName)) { 
        $downPayment = $servicePrice * 0.25;
        $balance = $servicePrice - $downPayment;
        $dueDate = date('F j, Y', strtotime('-1 day', strtotime($_POST['date'] ?? date('Y-m-d')))); ?>
        
        <p style="color:rgb(235, 7, 7); font-weight: bold; margin-top: 10px;">
            Dear <?php echo htmlspecialchars($loggedInName); ?>, please pay 25% (₱<?php echo number_format($downPayment, 2); ?>) by <?php echo $dueDate; ?> to confirm your booking. 
            Remaining Balance: <strong>₱<?php echo number_format($balance, 2); ?></strong>.
        </p>
    <?php } ?>
</div>


    <!-- Booking Form -->
    <div class="booking-form">
        <h2>Book Your Appointment</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
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
                        <input type="radio" id="gcash" name="payment-method" value="GCash" required>
                        <img src="img/gcashlogo.png" alt="GCash Logo">
                    </label>
                    <label class="payment-option">
                        <input type="radio" id="paymaya" name="payment-method" value="Paymaya" required>
                        <img src="img/Mayalogo.png" alt="Paymaya Logo">
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="payment-number">Account Number</label>
                <input type="text" class="form-control" placeholder="Enter Here.." maxlength="11" pattern="\d{11}" id="payment-number" name="payment-number" required>
            </div>

            <button type="submit" class="submit-btn">Confirm Booking</button>
        </form>
    </div>
</div>

<style>
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
        }

        .selected-service img {
            max-width: 300px;
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .payment-container {
            display: flex;
            align-items: center;
            gap: 20px; /* Adjust spacing between payment options */
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px; /* Space between radio button and image */
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

        
    </style>

<script>
function updateDueDate() {
    let dateInput = document.getElementById('date').value;
    if (dateInput) {
        let dueDate = new Date(dateInput);
        dueDate.setDate(dueDate.getDate() - 1);

        let formattedDate = dueDate.toLocaleDateString('en-US', {
            month: 'long', day: 'numeric', year: 'numeric'
        });

        let paymentNotice = document.querySelector('.selected-service p:nth-of-type(2)');
        if (paymentNotice) {
            paymentNotice.innerHTML = `Dear <?php echo htmlspecialchars($loggedInName); ?>, please pay 25% (₱<?php echo number_format($servicePrice * 0.25, 2); ?>) by ${formattedDate} to confirm your booking. Thank you!`;
        }
    }
}
</script>

