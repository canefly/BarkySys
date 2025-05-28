<?php include_once '../helpers/head.php' ?>
<?php
session_start();
require_once('tcpdf/tcpdf.php'); // Include TCPDF library
include_once 'db.php';

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

// Create new PDF document
$pdf = new TCPDF();
$pdf->SetCreator('AutoHub');
$pdf->SetAuthor('AutoHub Services');
$pdf->SetTitle('Booking Receipt');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Logo
$pdf->Image('img/autohublogo.png', 80, 10, 50); // Adjust the path accordingly
$pdf->Ln(25); // Space after logo

// Receipt Header
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "AutoHub Services", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, "123 Auto Street, City, State, ZIP", 0, 1, 'C');
$pdf->Cell(0, 8, "Phone: (123) 456-7890 | Email: support@autohub.com", 0, 1, 'C');
$pdf->Ln(5);

// Booking Details
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, "Booking Receipt", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 6, "Date: " . date("F j, Y", strtotime($booking['created_at'])), 0, 1, 'C');
$pdf->Cell(0, 6, "Receipt No: #" . str_pad($booking['id'], 6, "0", STR_PAD_LEFT), 0, 1, 'C');
$pdf->Ln(5);

// Customer Info
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 6, "Customer: " . htmlspecialchars($booking['name']), 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 6, "Email: " . htmlspecialchars($booking['email']), 0, 1);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(100, 8, "Service", 1);
$pdf->Cell(50, 8, "Price (₱)", 1, 1, 'C');

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(100, 8, htmlspecialchars($booking['service_name']), 1);
$pdf->Cell(50, 8, "₱" . number_format($booking['service_price'], 2), 1, 1, 'C');

$pdf->Cell(100, 8, "Down Payment (25%)", 1);
$pdf->Cell(50, 8, "₱" . number_format($booking['service_price'] * 0.25, 2), 1, 1, 'C');

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(100, 8, "Balance", 1);
$pdf->Cell(50, 8, "₱" . number_format($booking['balance'], 2), 1, 1, 'C');

$pdf->Ln(5);

// Additional Details
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 6, "Booking Date: " . date("F j, Y", strtotime($booking['date'])), 0, 1);
$pdf->Cell(0, 6, "Booking Time: " . date("h:i A", strtotime($booking['booking_time'])), 0, 1);
$pdf->Cell(0, 6, "Status: " . ucfirst($booking['status']), 0, 1);
$pdf->Cell(0, 6, "Payment Method: " . htmlspecialchars($booking['payment_method']), 0, 1);
$pdf->Cell(0, 6, "Transaction Number: " . htmlspecialchars($booking['payment_number']), 0, 1);
$pdf->Ln(5);

// Thank You Note
$pdf->SetFont('helvetica', 'I', 12);
$pdf->Cell(0, 10, "Thank you for choosing AutoHub Services!", 0, 1, 'C');

// Output PDF
$pdf->Output("Booking_Receipt_$bookingId.pdf", "D"); // Forces download
?>
