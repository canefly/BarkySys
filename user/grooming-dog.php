<?php
/*──────────────────────────────────────────────────────────
  DOG GROOMING SERVICES — USER VIEW (Bootstrap 5)
──────────────────────────────────────────────────────────*/
session_start();

if (!isset($_SESSION['email'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="user-login.php";</script>';
    exit();
}

include_once 'user-navigation.php';   // sidebar / navbar
include_once '../db.php';             // -> $conn (mysqli)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bark & Wiggle – Dog Grooming</title>

    <!-- Bootstrap CSS (no SRI to dodge integrity mismatch) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary: #6E3387;
            --accent:  #D6BE3E;
            --accent-hover: #C5A634;
            --bg-muted: #F7F2EB;
            --text-main: #333333;
            --text-muted: #777777;
        }
        body{background: var(--bg-muted);font-family: 'Poppins',sans-serif;}

        /* Card layout tweaks */
        .card-service{
            height: 100%;                       /* equal heights */
            display: flex; flex-direction: column;
            border: 1px solid rgba(0,0,0,.08);
            border-radius: 1rem;
            transition: transform .2s, box-shadow .2s;
        }
        .card-service:hover{
            transform: translateY(-4px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }
        .card-service img{
            object-fit: contain;height: 100px;width: 100px;margin: 0 auto;
        }
        .card-body{
            flex: 1 1 auto;                     /* let body stretch */
            display: flex; flex-direction: column;
        }
        /* Price + Button wrapper pushed to bottom */
        .price-btn-wrapper{margin-top: auto;}

        .book-btn{
            background: var(--accent);border: none;
            color: var(--text-main);font-weight: 500;width: 100%;
        }
        .book-btn:hover{background: var(--accent-hover);color: var(--text-main);}
    </style>
</head>
<body class="pt-4">

<div class="container">
    <h2 class="text-center fw-bold mb-4" style="color: var(--primary);">
        Dog Grooming Services
    </h2>

    <div class="row g-4 justify-content-center">
    <?php
        $sql = "
            SELECT *
            FROM services
            WHERE service_type = 'DogGrooming'
            ORDER BY display_order ASC, id ASC
        ";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $imagePath  = '../' . ltrim($row['service_image'], '/');
                $hasPrice   = !is_null($row['service_price']);   // numeric price?
    ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card card-service p-3">
                <img src="<?= htmlspecialchars($imagePath) ?>"
                     alt="<?= htmlspecialchars($row['service_name']) ?>" />

                <div class="card-body">
                    <h5 class="card-title text-center fw-semibold"
                        style="color: var(--primary);">
                        <?= htmlspecialchars($row['service_name']) ?>
                    </h5>

                    <p class="card-text text-muted small mb-3">
                        <?= htmlspecialchars($row['service_description']) ?>
                    </p>

                    <!-- Price + Button pinned at bottom -->
                    <div class="price-btn-wrapper">
                        <?php if ($hasPrice): ?>
                            <p class="fw-semibold text-center mb-2">
                                ₱<?= number_format($row['service_price'], 2) ?>
                            </p>
                        <?php endif; ?>

                        <a href="user-booking-form.php?id=<?= $row['id'] ?>"
                           class="btn book-btn">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php
            }
        } else {
            echo '<p class="text-center" style="color: var(--primary);">
                    No dog grooming services available.
                  </p>';
        }
    ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
