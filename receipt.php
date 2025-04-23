<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in first!'); window.location.href='login.php';</script>";
    exit();
}

// Get Booking ID
if (!isset($_GET['booking_id'])) {
    echo "<script>alert('Invalid Booking.'); window.location.href='Customer-bl.php';</script>";
    exit();
}

$bookingId = intval($_GET['booking_id']);
$userEmail = $_SESSION['username'];

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
    echo "<script>alert('Booking not found.'); window.location.href='Customer-bl.php';</script>";
    exit();
}

$booking = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 2px 2px 10px #aaa;
            background-color: #fff;
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
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-approved { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-canceled { color: red; font-weight: bold; }
        .download-btn {
            display: block;
            width: 100%;
            text-align: center;
            background: #28a745;
            color: #fff;
            padding: 10px;
            text-decoration: none;
            margin-top: 15px;
            border-radius: 5px;
        }
        .download-btn:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="receipt-header">
        <img src="img/autohublogo.png" alt="AutoHub Logo">
        <h2>AutoHub Services</h2>
        <p>123 Auto Street, City, State, ZIP</p>
        <p>Phone: (123) 456-7890 | Email: support@autohub.com</p>
        <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($booking['created_at'])); ?></p>
        <p><strong>Receipt No:</strong> #<?php echo str_pad($booking['id'], 6, "0", STR_PAD_LEFT); ?></p>
    </div>
    
    <h3>Customer: <?php echo htmlspecialchars($booking['name']); ?></h3>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
    
    <table>
        <tr>
            <th>Service</th>
            <th>Price</th>
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
            <td><strong>Balance</strong></td>
            <td>₱<?php echo number_format($booking['balance'], 2); ?></td>
        </tr>
    </table>

    <p><strong>Booking Date:</strong> <?php echo date("F j, Y", strtotime($booking['date'])); ?></p>
    <p><strong>Booking Time:</strong> <?php echo date("h:i A", strtotime($booking['booking_time'])); ?></p>
    <p><strong>Status:</strong> <span class="status-<?php echo strtolower($booking['status']); ?>"><?php echo ucfirst($booking['status']); ?></span></p>
    
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($booking['payment_method']); ?></p>
    <p><strong>Transaction Number:</strong> <?php echo htmlspecialchars($booking['payment_number']); ?></p>
    
    <p>Thank you for choosing AutoHub Services!</p>
    
    <!-- Button to download as PDF -->
    <button id="download-btn" class="download-btn">Download as PDF</button>


</body>
</html>

<script>
document.getElementById("download-btn").addEventListener("click", function () {
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF();
    
    // Capture the receipt content
    html2canvas(document.body, {
        scale: 2
    }).then(canvas => {
        let imgData = canvas.toDataURL("image/png");
        let imgWidth = 190; 
        let pageHeight = 297;
        let imgHeight = (canvas.height * imgWidth) / canvas.width;
        let position = 10;

        // Add the image to the PDF
        doc.addImage(imgData, "PNG", 10, position, imgWidth, imgHeight);
        doc.save("receipt_<?php echo $bookingId; ?>.pdf");
    });
});
</script>

