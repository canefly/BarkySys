<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="user-login.php";</script>';
    exit();
}

include_once 'user-navigation.php';
include_once '../db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bark & Wiggle – Dog Grooming</title>
    <style>
        :root {
            --primary: #6E3387;
            --accent: #D6BE3E;
            --accent-hover: #C5A634;
            --bg-muted: #F7F2EB;
            --text-main: #333333;
            --text-muted: #777777;
        }

        body {
            background-color: var(--bg-muted);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-open {
            margin-left: 250px;
        }

        .container {
            padding: 20px;
            max-width: 100%;
            margin-left: 150px;
        }

        .services {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            padding: 10px;
            align-items: flex-start;
        }

        .service-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 300px;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-card img {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
            object-fit: contain;
        }

        .service-card h3 {
            margin: 10px 0;
            color: var(--primary);
            font-size: 20px;
            font-weight: 600;
        }

        .service-card p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .book-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 18px;
            background: var(--accent);
            color: var(--text-main);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }

        .book-btn:hover {
            background: var(--accent-hover);
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    <h2 style="color: var(--primary); text-align: center; font-weight: 600;">Dog Grooming Services</h2>
    <div class="services">
        <?php
        $query = "SELECT * FROM services WHERE service_type = 'DogGrooming'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $imagePath = '../' . ltrim($row['service_image'], '/');
                ?>
                <div class="service-card">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['service_name']); ?>">
                    <h3><?php echo htmlspecialchars($row['service_name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['service_description']); ?></p>
                    <p><strong>₱<?php echo number_format($row['service_price'], 2); ?></strong></p>
                    <a href="user-booking-form.php?id=<?php echo $row['id']; ?>" class="book-btn">Book Now</a>
                </div>
                <?php
            }
        } else {
            echo '<p style="color: var(--primary);">No dog grooming services available.</p>';
        }
        ?>
    </div>
</div>

</body>
</html>
