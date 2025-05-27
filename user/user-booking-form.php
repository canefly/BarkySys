<?php
/**
 * Bark & Wiggle – Checkout (25 % Reservation Flow)
 * Von ❤️ Cane – 2025-05-27
 */

session_start();
require_once '../db.php';
include_once 'user-navigation.php';

/* ───── AUTH ───── */
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in first!'); window.location.href='user-login.php';</script>";
    exit();
}

$loggedInEmail = $_SESSION['email'];
$uStmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ? LIMIT 1");
$uStmt->bind_param("s", $loggedInEmail);
$uStmt->execute();
$user = $uStmt->get_result()->fetch_assoc() ?: [];

if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='user-login.php';</script>";
    exit();
}
$userId   = $user['id'];
$userName = $user['full_name'];

/* ───── SERVICE LOOK-UP ───── */
$serviceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($serviceId <= 0) die('<p>Invalid service.</p>');

$sStmt = $conn->prepare("SELECT * FROM services WHERE id = ? LIMIT 1");
$sStmt->bind_param("i", $serviceId);
$sStmt->execute();
$service = $sStmt->get_result()->fetch_assoc() ?: [];

if (!$service) die('<p>Service not found.</p>');

$serviceName  = $service['service_name'];
$serviceType  = $service['service_type'];                      /* DogGrooming | CatGrooming */
$serviceImage = '../' . ltrim($service['service_image'], '/');
$serviceMode  = $service['mode'];                              /* package | individual */
$isDog        = str_contains($serviceType, 'Dog');
$species      = $isDog ? 'Dog' : 'Cat';

/* ───── PET PRICE HELPER ───── */
function priceForPet(mysqli $c, int $svcId, array $pet, bool $isDog): float
{
    if ($isDog) {
        $q = $c->prepare("SELECT id FROM weight_categories WHERE ? >= min_kg AND ? < max_kg LIMIT 1");
        $q->bind_param("dd", $pet['weight'], $pet['weight']);
    } else {
        $months = $pet['age'] * 12;
        $q = $c->prepare("SELECT id FROM age_categories WHERE ? >= min_months AND ? < max_months LIMIT 1");
        $q->bind_param("ii", $months, $months);
    }
    $q->execute();
    $catId = $q->get_result()->fetch_assoc()['id'] ?? null;

    if (!$catId) return 0.0;

    $p = $c->prepare("SELECT price FROM pricing WHERE service_id = ? AND weight_category_id = ? LIMIT 1");
    $p->bind_param("ii", $svcId, $catId);
    $p->execute();
    return (float)($p->get_result()->fetch_assoc()['price'] ?? 0.0);
}

/* ───── FETCH USER PETS ───── */
$pStmt = $conn->prepare("SELECT * FROM pets WHERE user_id = ? AND pet_type = ?");
$pStmt->bind_param("is", $userId, $species);
$pStmt->execute();
$pets = [];
$petRes = $pStmt->get_result();
while ($row = $petRes->fetch_assoc()) {
    $row['calc_price'] = round(priceForPet($conn, $serviceId, $row, $isDog), 2);
    $pets[$row['id']] = $row;
}
if ($serviceMode === 'package' && empty($pets)) {
    echo "<script>alert('No registered {$species}s for this package.'); window.location.href='user-pets.php';</script>";
    exit();
}

/* ───── HANDLE PAYMENT (POST) ───── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_payment'])) {
    $selPets = $_POST['pets'] ?? [];
    if (!$selPets) {
        echo "<script>alert('Please select at least one pet.'); history.back();</script>";
        exit();
    }

    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);

    /* booking cap: 20 / day */
    $cap = $conn->query("SELECT COUNT(*) AS cnt FROM bookings WHERE date = '$date'")->fetch_assoc()['cnt'] ?? 0;
    if ($cap >= 20) {
        echo "<script>alert('Booking limit reached for this date.'); window.location.href='user-hp.php';</script>";
        exit();
    }

    /* totals */
    $total = 0.0;
    foreach ($selPets as $pid) {
        if (isset($pets[$pid])) $total += $pets[$pid]['calc_price'];
    }
    $down  = round($total * 0.25, 2);
    $bal   = round($total - $down, 2);

    $ins = $conn->prepare(
        "INSERT INTO bookings
         (service_name, service_price, name, email, date, booking_time, payment_method, balance, status)
         VALUES (?,?,?,?,?,?,?,?,'pending')"
    );
    $method = 'SimulatedPay';
    $ins->bind_param("sdssssds", $serviceName, $total, $userName, $loggedInEmail,
                     $date, $time, $method, $bal);

    if ($ins->execute()) {
        echo "<script>alert('Booking saved!'); window.location.href='user-bl.php';</script>";
        exit();
    }
    echo "<p>Error: ".$conn->error."</p>";
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Checkout – <?= htmlspecialchars($serviceName) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body{background:#f6f4f1;font-family:'Poppins',sans-serif}
.pet-card:hover{border-color:#6c757d;cursor:pointer}
</style>
</head>
<body>
<div class="container py-5">
  <div class="row g-4">

    <!-- Service / Pricing Card -->
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <img src="<?= htmlspecialchars($serviceImage) ?>" class="card-img-top" alt="package">
        <div class="card-body text-center">
          <h5 class="fw-semibold mb-2"><?= htmlspecialchars($serviceName) ?></h5>
          <p class="small text-muted">Package for <?= $species ?></p>
          <div id="priceInfo" class="mt-2 d-none">
            <span id="origLabel"  class="d-block fw-semibold"></span>
            <span id="payLabel"   class="d-block text-success fw-semibold"></span>
            <small id="noteLabel" class="text-muted"></small>
          </div>
        </div>
      </div>
    </div>

    <!-- Booking Form -->
    <div class="col-lg-8">
      <form id="bookingForm" class="card shadow-sm" method="post">
        <div class="card-body p-4">

          <h4 class="fw-bold text-center mb-4">Select Pets &amp; Book</h4>

          <!-- Pet selection -->
          <div class="mb-4">
            <label class="form-label fw-semibold">Your <?= strtolower($species) ?>s:</label>
            <div class="row g-3">
            <?php foreach ($pets as $pid => $pet): ?>
              <div class="col-md-6">
                <div class="form-check border rounded p-3 pet-card h-100">
                  <input type="checkbox"
                         class="form-check-input me-2 pet-checkbox"
                         id="pet<?= $pid ?>"
                         name="pets[]"
                         value="<?= $pid ?>"
                         data-price="<?= $pet['calc_price'] ?>">
                  <label class="form-check-label w-100" for="pet<?= $pid ?>">
                    <span class="fw-semibold d-block"><?= htmlspecialchars(ucfirst($pet['pet_name'])) ?></span>
                    <small class="text-muted d-block mb-1">Breed: <?= htmlspecialchars($pet['breed']) ?></small>
                    <?php if ($isDog): ?>
                      <small class="d-block">Weight: <?= $pet['weight'] ?> kg</small>
                    <?php else: ?>
                      <small class="d-block">Age: <?= $pet['age'] ?> yr(s)</small>
                    <?php endif ?>
                    <span class="badge bg-secondary mt-2">₱<?= number_format($pet['calc_price'],2) ?></span>
                  </label>
                </div>
              </div>
            <?php endforeach ?>
            </div>
          </div>

          <!-- Details -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($userName) ?>" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" value="<?= htmlspecialchars($loggedInEmail) ?>" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Preferred Date</label>
              <input type="date" name="date" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Preferred Time</label>
              <select name="time" class="form-select" required>
                <option>10:00 AM</option><option>11:00 AM</option>
                <option>1:00 PM</option><option>2:00 PM</option><option>3:00 PM</option>
              </select>
            </div>
          </div>

          <!-- Summary -->
          <div class="border rounded p-3 mb-4 bg-light">
            <h6 class="fw-semibold mb-2">Summary</h6>
            <ul class="list-group list-group-flush" id="breakdown"></ul>
            <div class="d-flex justify-content-between mt-2 fw-semibold">
              <span>Total</span><span id="totalPrice">₱0.00</span>
            </div>
          </div>

          <input type="hidden" name="proceed_payment" value="1">
          <button type="button" id="payBtn" class="btn btn-primary w-100">
            Proceed to Payment
          </button>

        </div>
      </form>
    </div>

  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Payment Success ✅</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>You’ve paid the reservation fee. A receipt email is on its way.<br>
           The remaining balance must be settled at the counter once grooming finishes.</p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" id="finishBtn" class="btn btn-success" data-bs-dismiss="modal">
          Got it!
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/*  ------ SUMMARY NOW SHOWS 25 % ONLY  ------ */

const petCheckboxes = document.querySelectorAll('.pet-checkbox');
const breakdown     = document.getElementById('breakdown');
const totalLabel    = document.getElementById('totalPrice');

const priceBox  = document.getElementById('priceInfo');
const origLabel = document.getElementById('origLabel');   // full package total
const payLabel  = document.getElementById('payLabel');    // 25 % to pay now
const noteLabel = document.getElementById('noteLabel');   // remaining balance

const currency  = new Intl.NumberFormat('en-PH', { style:'currency', currency:'PHP' });

function updateUI () {
  let fullTotal  = 0;   // 100 %
  let downTotal  = 0;   // 25 %

  breakdown.innerHTML = '';

  petCheckboxes.forEach(cb => {
    if (cb.checked) {
      const priceFull = parseFloat(cb.dataset.price);   // full price per pet
      const priceDown = Math.round(priceFull * 0.25 * 100) / 100;

      fullTotal += priceFull;
      downTotal += priceDown;

      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between';
      li.innerHTML = `Pet #${cb.value}<span>${currency.format(priceDown)}</span>`;
      breakdown.appendChild(li);
    }
  });

  /* update labels */
  totalLabel.textContent = currency.format(downTotal);

  if (downTotal > 0) {
    const remaining = Math.round((fullTotal - downTotal) * 100) / 100;

    origLabel.textContent = `Package Total: ${currency.format(fullTotal)}`;
    payLabel.textContent  = `Pay Now (25 %): ${currency.format(downTotal)}`;
    noteLabel.textContent = `Remaining ${currency.format(remaining)} will be paid at the counter upon completion.`;

    priceBox.classList.remove('d-none');
  } else {
    priceBox.classList.add('d-none');
  }
}

petCheckboxes.forEach(cb => cb.addEventListener('change', updateUI));

/* ----- payment modal flow ----- */
const payBtn       = document.getElementById('payBtn');
const successModal = new bootstrap.Modal('#successModal');
const finishBtn    = document.getElementById('finishBtn');

payBtn.addEventListener('click', () => {
  if (!document.querySelector('.pet-checkbox:checked')) {
    alert('Select at least one pet.');
    return;
  }
  updateUI();               // ensure totals are fresh
  successModal.show();
});

finishBtn.addEventListener('click', () => {
  document.getElementById('bookingForm').submit();
});
</script>

</body>
</html>
