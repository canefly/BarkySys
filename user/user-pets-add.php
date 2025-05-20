<?php
session_start();

// ---------- GUARD: logged-in? ----------
if (!isset($_SESSION['email'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="user-login.php";</script>';
    exit();
}

include_once 'user-navigation.php';   // top nav (unchanged)
include_once '../db.php';             // creates $conn (mysqli)

// ---------- HELPERS ----------
function getUserId(mysqli $conn, string $email): ?int {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($uid);
    $stmt->fetch();
    $stmt->close();
    return $uid ?: null;
}

$email   = $_SESSION['email'];
$user_id = getUserId($conn, $email);

$modalStatus = "";    // '', 'success', or 'error'

// ---------- HANDLE ADD-PET POST ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_pet'])) {
    // sanitise / cast
    $pet_name  = trim($_POST['pet_name'] ?? '');
    $pet_type  = $_POST['pet_type'] ?? 'Dog';                // enum
    $breed     = trim($_POST['breed'] ?? '');
    $age       = (int)($_POST['age'] ?? 0);
    $weight    = (float)($_POST['weight'] ?? 0);

    if ($pet_name && $breed && $age > 0 && $weight > 0 && $user_id) {
        $stmt = $conn->prepare(
            "INSERT INTO pets (user_id, pet_name, pet_type, breed, age, weight)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("isssid", $user_id, $pet_name, $pet_type, $breed, $age, $weight);
        $modalStatus = $stmt->execute() ? "success" : "error";
        $stmt->close();
    } else {
        $modalStatus = "error";
    }
}

// ---------- FETCH USER PETS ----------
$pets = [];
if ($user_id) {
    $stmt = $conn->prepare(
        "SELECT pet_name, pet_type, breed, age, weight, created_at
         FROM pets
         WHERE user_id = ?
         ORDER BY created_at DESC"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $pets[] = $row;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Pets — BarkSys</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body            { font-family: 'Poppins', sans-serif; background:#f8fafc; }
    .card-pet       { border:0; box-shadow:0 3px 6px rgba(0,0,0,.08); }
    .card-pet:hover { transform:translateY(-2px); transition:.2s; }
    .pet-avatar     { width:64px; height:64px; object-fit:cover; border-radius:50%; }
  </style>
</head>
<body>
<div class="container py-5">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold m-0">My Pets</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPetModal">
      <i class="bi bi-plus-circle me-1"></i>Add Pet
    </button>
  </div>

  <?php if (!$pets): ?>
    <div class="alert alert-info">No pets yet. Click “Add Pet” to register one!</div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($pets as $p): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card card-pet p-3 h-100">
            <div class="d-flex align-items-center gap-3">
              <img src="../img/pup-paws.png" alt="avatar" class="pet-avatar">
              <div>
                <h5 class="mb-1"><?= htmlspecialchars($p['pet_name']) ?></h5>
                <span class="badge bg-secondary"><?= $p['pet_type'] ?></span>
              </div>
            </div>
            <ul class="list-unstyled mt-3 small">
              <li><strong>Breed:</strong> <?= htmlspecialchars($p['breed']) ?></li>
              <li><strong>Age:</strong> <?= $p['age'] ?> yr(s)</li>
              <li><strong>Weight:</strong> <?= number_format($p['weight'],1) ?> kg</li>
              <li class="text-muted">Added <?= date('M d, Y', strtotime($p['created_at'])) ?></li>
            </ul>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<!-- ============ ADD PET MODAL ============ -->
<div class="modal fade" id="addPetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content needs-validation" method="POST" novalidate>
      <input type="hidden" name="add_pet" value="1">
      <div class="modal-header">
        <h5 class="modal-title">Add a New Pet</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Pet Name</label>
          <input type="text" name="pet_name" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Pet Type</label>
          <select name="pet_type" class="form-select" required>
            <option value="Dog">Dog</option>
            <option value="Cat">Cat</option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Breed</label>
          <input type="text" name="breed" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Age (yrs)</label>
          <input type="number" name="age" class="form-control" min="0" step="1" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Weight (kg)</label>
          <input type="number" name="weight" class="form-control" min="0" step=".1" required>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- ============ RESULT MODALS ============ -->
<div class="modal fade" id="resultSuccess" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-success">
      <div class="modal-body text-center py-5">
        <i class="bi bi-check-circle fs-1 text-success"></i>
        <h4 class="mt-3 mb-0">Pet saved!</h4>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="resultError" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-body text-center py-5">
        <i class="bi bi-x-circle fs-1 text-danger"></i>
        <h4 class="mt-3 mb-0">Something went wrong.</h4>
        <p class="small text-muted">Please check your inputs and try again.</p>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// client-side validation
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(f => {
    f.addEventListener('submit', e => {
      if (!f.checkValidity()) {
        e.preventDefault(); e.stopPropagation();
      }
      f.classList.add('was-validated');
    }, false);
  });
})();

// show result modal if needed
<?php if ($modalStatus): ?>
  const which = <?= json_encode($modalStatus === 'success' ? '#resultSuccess' : '#resultError') ?>;
  new bootstrap.Modal(document.querySelector(which)).show();
<?php endif; ?>
</script>

<!-- Bootstrap Icons CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
