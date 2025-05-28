<?php include_once 'helpers/head.php' ?>

<?php
session_start();
include_once '../db.php';

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in first!'); window.location.href='user-login.php';</script>";
    exit();
}

// Get Booking ID
if (!isset($_GET['booking_id'])) {
    echo "<script>alert('Invalid Booking.'); window.location.href='user-bl.php';</script>";
    exit();
}

$bookingId = intval($_GET['booking_id']);
$userEmail = $_SESSION['email'];

// Fetch booking details
$query = "
    SELECT id, service_name, service_price, name, email, booking_time, created_at, date, status, 
           payment_method, payment_number, COALESCE(balance, service_price * 0.75) AS balance
    FROM bookings
    WHERE id = ? AND email = ?
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "is", $bookingId, $userEmail);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<script>alert('Booking not found.'); window.location.href='user-bl.php';</script>";
    exit();
}

$booking = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bark & Wiggle – Booking Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #e0d8cd;
            box-shadow: 2px 2px 15px #b9b0a3;
            background-color: #fefcfb;
            color: #4a3e3e;
        }
        h2, p {
            text-align: center;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-header img {
            max-width: 80px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 14px;
        }
        th {
            background-color: #f5f0e8;
        }
        .status-approved { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-canceled { color: red; font-weight: bold; }
        .download-btn {
            display: block;
            width: 100%;
            text-align: center;
            background: #D6BE3E;
            color: #000;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 15px;
        }
        .download-btn:hover {
            background: #c5a634;
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <img src="../img/logo.png" alt="Bark & Wiggle Logo">
        <h2>Bark & Wiggle Pet Spa</h2>
        <p>Unit 2F, Pawfect Plaza, Doggo St., Cat City</p>
        <p>Email: barkandwiggle@petmail.com</p>
        <p><strong>Date Issued:</strong> <?php echo date("F j, Y", strtotime($booking['created_at'])); ?></p>
        <p><strong>Receipt No:</strong> #<?php echo str_pad($booking['id'], 6, "0", STR_PAD_LEFT); ?></p>
    </div>

    <h3>Customer: <?php echo htmlspecialchars($booking['name']); ?></h3>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>

    <table>
        <tr>
            <th>Service</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
            <td>₱<?php echo number_format($booking['service_price'], 2); ?></td>
        </tr>
        <tr>
            <td>Down Payment (25%)</td>
            <td>₱<?php echo number_format($booking['service_price'] * 0.25, 2); ?></td>
        </tr>
        <tr>
            <td><strong>Remaining Balance</strong></td>
            <td><strong>₱<?php echo number_format($booking['balance'], 2); ?></strong></td>
        </tr>
    </table>

    <p><strong>Scheduled Date:</strong> <?php echo date("F j, Y", strtotime($booking['date'])); ?></p>
    <p><strong>Time Slot:</strong> <?php echo date("h:i A", strtotime($booking['booking_time'])); ?></p>
    <p><strong>Status:</strong> 
        <span class="status-<?php echo strtolower($booking['status']); ?>">
            <?php echo ucfirst($booking['status']); ?>
        </span>
    </p>

    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($booking['payment_method']); ?></p>
    <p><strong>Reference Number:</strong> <?php echo htmlspecialchars($booking['payment_number']); ?></p>

    <p style="text-align:center; margin-top: 30px;">Thank you for trusting Bark & Wiggle!<br>Where pets get pampered, not just groomed. 🐾</p>

    <button id="download-btn" class="download-btn">Download Receipt as PDF</button>

    <script>
    document.getElementById("download-btn").addEventListener("click", function () {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF();

        html2canvas(document.body, { scale: 2 }).then(canvas => {
            let imgData = canvas.toDataURL("image/png");
            let imgWidth = 190;
            let pageHeight = 297;
            let imgHeight = (canvas.height * imgWidth) / canvas.width;
            let position = 10;

            doc.addImage(imgData, "PNG", 10, position, imgWidth, imgHeight);
            doc.save("barkwiggle_receipt_<?php echo $bookingId; ?>.pdf");
        });
    });
    </script>
</body>
</html>
