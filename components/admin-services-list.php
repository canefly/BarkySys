<?php
session_start();
include 'db.php'; // Database connection

// Drop DELETE triggers to avoid missing procedure error
$triggerRes = mysqli_query($conn, "SHOW TRIGGERS FROM salon_db WHERE `Table`='services' AND `Event`='DELETE'");
while ($tr = mysqli_fetch_assoc($triggerRes)) {
    mysqli_query($conn, "DROP TRIGGER IF EXISTS `{$tr['Trigger']}`");
}

// Handle deletion submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'])) {
    $service_id = mysqli_real_escape_string($conn, $_POST['service_id']);

    // Fetch image path
    $res = mysqli_query($conn, "SELECT service_image FROM services WHERE id='$service_id'");
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $img = $row['service_image'];
        // Delete record
        if (mysqli_query($conn, "DELETE FROM services WHERE id='$service_id'")) {
            // Remove image file
            if ($img && file_exists($img)) unlink($img);
            echo '<script>alert("Service deleted successfully!"); window.location.href="services-list.php";</script>';
            exit;
        } else {
            echo '<script>alert("Error deleting service."); window.location.href="services-list.php";</script>';
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bark & Wiggle – Services List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Container & Sidebar spacing */
        .container {
            margin-left: 250px;
            padding: 20px;
        }
        body.sidebar-open .container {
            margin-left: 300px;
        }
        /* Services list layout */
        .service-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        /* Individual card sizing within flex */
        .service-card {
            flex: 1 1 calc(33.333% - 20px);
            box-sizing: border-box;
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
        }
        /* Responsive fallback */
        @media (max-width: 768px) {
            .service-card {
                flex: 1 1 calc(50% - 20px);
            }
        }
        @media (max-width: 576px) {
            .container { margin-left: 0; }
            .service-list { flex-direction: column; }
            .service-card { flex: 1 1 100%; }
        }
        .service-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 20px;
            flex-shrink: 0;
        }
        .service-info h3 {
            margin: 0 0 5px;
            color: #6E3387;
        }
        .service-info p { margin: 0; }
        .delete-btn {
            background: #D6BE3E;
            color: #000;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            margin-left: auto;
        }
        .delete-btn:hover { background: #C5A634; }
    </style>
</head>
<body>
    <?php include 'admin-navigation.php'; ?>
    <div class="container">
        <h2>Services List</h2>
        <div class="service-list">
            <?php
            $list = $conn->query(
                "SELECT id, service_name, service_description, service_price, service_image
                FROM services ORDER BY id DESC"
            );
            if ($list->num_rows > 0) {
                while ($row = $list->fetch_assoc()) {
                    echo '<div class="service-card">';
                    echo '<img src="'.htmlspecialchars($row['service_image']).'" alt="Service Image">';
                    echo '<div class="service-info">';
                    echo '<h3>'.htmlspecialchars($row['service_name']).'</h3>';
                    echo '<p>'.htmlspecialchars($row['service_description']).'</p>';
                    echo '<p><strong>₱'.number_format($row['service_price'],2).'</strong></p>';
                    echo '</div>';
                    echo '<form method="POST" onsubmit="return confirm(\'Are you sure?\')">';
                    echo '<input type="hidden" name="service_id" value="'.(int)$row['id'].'">';
                    echo '<button type="submit" class="delete-btn">Delete</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<p>No services available.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
