<?php
session_start();
include_once 'admin-navigation.php';
include_once '../db.php';
include_once '../helpers/path-helper.php'; // 👈 Add this line to use our path resolver

// Drop DELETE triggers to avoid missing procedure error
$triggerRes = mysqli_query($conn, "SHOW TRIGGERS FROM salon_db WHERE `Table`='services' AND `Event`='DELETE'");
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
        $resolvedPath = resolveUploadPath($img); // 🔍 Resolve to absolute path

        // Delete service row from DB
        if (mysqli_query($conn, "DELETE FROM services WHERE id='$service_id'")) {
            // Delete image file if exists
            if ($img && $resolvedPath && file_exists($resolvedPath)) {
                unlink($resolvedPath);
            }

            // Redirect back to the correct path (adjusted)
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
    <title>Bark & Wiggle – Services List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: rgb(236, 227, 218);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            transition: margin-left 0.3s ease-in-out;
        }

        .container {
            flex: 1;
            padding: 20px;
            margin-left: 70px;
            transition: margin-left 0.3s ease-in-out;
        }

        body.sidebar-open .container {
            width: calc(100vw - 290px);
            margin-left: 290px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .service-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .service-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
            flex-wrap: wrap;
        }

        .service-card img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 10px;
        }

        .service-info {
            flex: 1;
            text-align: left;
        }

        .service-info h3 {
            margin: 0;
            color: #6E3387;
        }

        .service-info p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        @media (max-width: 768px) {
            .container { margin-left: 0; }
            .service-list { flex-direction: column; }
            .service-card { flex: 1 1 100%; flex-direction: column; align-items: flex-start; }
            .delete-btn { margin-top: 10px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Services List</h2>
    <div class="service-list">
        <?php
        $list = $conn->query("SELECT id, service_name, service_description, service_price, service_image FROM services ORDER BY id DESC");
        if ($list->num_rows > 0) {
            while ($row = $list->fetch_assoc()) {
                echo '<div class="service-card">';
                $imagePath = '../' . ltrim($row['service_image'], '/');
                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Service Image">';
                echo '<div class="service-info">';
                echo '<h3>' . htmlspecialchars($row['service_name']) . '</h3>';
                echo '<p>' . htmlspecialchars($row['service_description']) . '</p>';
                echo '<p><strong>₱' . number_format($row['service_price'], 2) . '</strong></p>';
                echo '</div>';
                echo '<form method="POST" onsubmit="return confirm(\'Are you sure?\')">';
                echo '<input type="hidden" name="service_id" value="' . (int)$row['id'] . '">';
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
