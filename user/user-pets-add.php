<?php include_once 'helpers/head.php' ?>

<?php
session_start();

/* ─────────────────────────────────────────────────────────────
   1. GUARD
   ───────────────────────────────────────────────────────────── */
if (!isset($_SESSION['email'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="user-login.php";</script>';
    exit();
}

include_once 'user-navigation.php';
include_once '../db.php';

/* ─────────────────────────────────────────────────────────────
   2. HELPERS
   ───────────────────────────────────────────────────────────── */
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

/* ─────────────────────────────────────────────────────────────
   3. LOAD WEIGHT CATEGORIES (one query)
   ───────────────────────────────────────────────────────────── */
$weightCats = []; // [{name,min,max}, ...]
$catTooltipParts = [];
$res = $conn->query("SELECT category_name, min_kg, max_kg FROM weight_categories ORDER BY min_kg ASC");
while ($row = $res->fetch_assoc()) {
    $weightCats[] = $row;
    $catTooltipParts[] = "{$row['category_name']} = {$row['min_kg']}-{$row['max_kg']} kg";
}
$weightTooltipAll = implode(', ', $catTooltipParts);

/* helper to pick category */
function classifyWeight(float $kg, array $cats): string {
    foreach ($cats as $c) {
        if ($kg >= (float)$c['min_kg'] && $kg <= (float)$c['max_kg']) {
            return $c['category_name'];
        }
    }
    return 'N/A';
}

$modalStatus = "";   // 'added' | 'edited' | 'deleted' | 'error'
$modalMsg    = "";

/* ─────────────────────────────────────────────────────────────
   4. HANDLE FORM ACTIONS
   ───────────────────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_pet'])) {
        $pet_name = trim($_POST['pet_name'] ?? '');
        $pet_type = $_POST['pet_type'] ?? 'Dog';
        $breed    = trim($_POST['breed'] ?? '');
        $age      = (int)($_POST['age'] ?? 0);
        $weight   = (float)($_POST['weight'] ?? 0);

        if ($pet_name && $breed && $age > 0 && $weight > 0) {
            $stmt = $conn->prepare(
                "INSERT INTO pets (user_id, pet_name, pet_type, breed, age, weight)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("isssid", $user_id, $pet_name, $pet_type, $breed, $age, $weight);
            $modalStatus = $stmt->execute() ? "added" : "error";
            $stmt->close();
        } else { $modalStatus = "error"; }

    } elseif (isset($_POST['edit_pet'])) {
        $pet_id   = (int)($_POST['pet_id'] ?? 0);
        $pet_name = trim($_POST['pet_name'] ?? '');
        $pet_type = $_POST['pet_type'] ?? 'Dog';
        $breed    = trim($_POST['breed'] ?? '');
        $age      = (int)($_POST['age'] ?? 0);
        $weight   = (float)($_POST['weight'] ?? 0);

        if ($pet_id && $pet_name && $breed && $age > 0 && $weight > 0) {
            $stmt = $conn->prepare(
                "UPDATE pets SET pet_name=?, pet_type=?, breed=?, age=?, weight=?
                 WHERE id=? AND user_id=?"
            );
            $stmt->bind_param("sssiddi", $pet_name, $pet_type, $breed, $age, $weight, $pet_id, $user_id);
            $modalStatus = $stmt->execute() ? "edited" : "error";
            $stmt->close();
        } else { $modalStatus = "error"; }

    } elseif (isset($_POST['delete_pet'])) {
        $pet_id = (int)($_POST['pet_id'] ?? 0);
        if ($pet_id) {
            $stmt = $conn->prepare("DELETE FROM pets WHERE id=? AND user_id=?");
            $stmt->bind_param("ii", $pet_id, $user_id);
            $modalStatus = $stmt->execute() ? "deleted" : "error";
            $stmt->close();
        } else { $modalStatus = "error"; }
    }

    $modalMsg = match($modalStatus) {
        'added'   => "New pet saved!",
        'edited'  => "Pet details updated!",
        'deleted' => "Pet deleted!",
        default   => ""
    };
}

/* ─────────────────────────────────────────────────────────────
   5. FETCH USER PETS
   ───────────────────────────────────────────────────────────── */
$pets = [];
if ($user_id) {
    $stmt = $conn->prepare(
        "SELECT id, pet_name, pet_type, breed, age, weight, created_at
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
    .card-pet       { border:0; box-shadow:0 3px 6px rgba(0,0,0,.08); position:relative; }
    .card-pet:hover { transform:translateY(-2px); transition:.2s; }
    .pet-avatar     { width:64px; height:64px; object-fit:cover; border-radius:50%; }
    .pet-actions    { position:absolute; top:.75rem; right:.75rem; }
  </style>
</head>
<body>
<div class="container py-5">

  <!-- Heading -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">
      <h2 class="fw-bold m-0">My Pets</h2>
      <button class="btn btn-outline-secondary btn-sm rounded-circle lh-1"
              data-bs-toggle="tooltip" data-bs-placement="right"
              title="Manage your registered pets. Each pet is classified automatically using weight categories: <?= $weightTooltipAll ?>.">
        <i class="bi bi-question-lg"></i>
      </button>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPetModal">
      <i class="bi bi-plus-circle me-1"></i>Add Pet
    </button>
  </div>

  <?php if (!$pets): ?>
    <div class="alert alert-info">No pets yet. Click “Add Pet” to register one!</div>
  <?php else: ?>
    <div class="row g-4">
    <?php foreach ($pets as $p):
        $icon         = $p['pet_type'] === 'Dog' ? '../img/pup-paws.png' : '../img/Cat-paws.png';
        $weightClass  = classifyWeight((float)$p['weight'], $weightCats);
    ?>
      <div class="col-md-6 col-lg-4">
        <div class="card card-pet p-3 h-100">
          <div class="pet-actions btn-group btn-group-sm">
            <button class="btn btn-outline-primary editBtn"
                    data-id="<?= $p['id'] ?>"
                    data-name="<?= htmlspecialchars($p['pet_name']) ?>"
                    data-type="<?= $p['pet_type'] ?>"
                    data-breed="<?= htmlspecialchars($p['breed']) ?>"
                    data-age="<?= $p['age'] ?>"
                    data-weight="<?= $p['weight'] ?>"
                    data-bs-toggle="modal" data-bs-target="#editPetModal">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-outline-danger deleteBtn"
                    data-id="<?= $p['id'] ?>"
                    data-name="<?= htmlspecialchars($p['pet_name']) ?>"
                    data-bs-toggle="modal" data-bs-target="#deletePetModal">
              <i class="bi bi-trash"></i>
            </button>
          </div>

          <div class="d-flex align-items-center gap-3">
            <img src="<?= $icon ?>" alt="avatar" class="pet-avatar">
            <div>
              <h5 class="mb-1"><?= htmlspecialchars($p['pet_name']) ?></h5>
              <span class="badge bg-secondary"><?= $p['pet_type'] ?></span>
            </div>
          </div>
          <ul class="list-unstyled mt-3 small">
            <li><strong>Breed:</strong> <?= htmlspecialchars($p['breed']) ?></li>
            <li><strong>Age:</strong> <?= $p['age'] ?> yr(s)</li>
            <li><strong>Weight:</strong> <?= number_format($p['weight'],1) ?> kg</li>
            <li>
              <strong>Category:</strong>
              <span class="badge bg-info"
                    data-bs-toggle="tooltip"
                    title="<?= $weightTooltipAll ?>">
                <?= $weightClass ?>
              </span>
            </li>
            <li class="text-muted">Added <?= date('M d, Y', strtotime($p['created_at'])) ?></li>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<!-- (Modals unchanged: Add, Edit, Delete, Results) -->
<!-- ADD PET MODAL -->
<div class="modal fade" id="addPetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content needs-validation" method="POST" novalidate>
      <input type="hidden" name="add_pet" value="1">
      <div class="modal-header"><h5 class="modal-title">Add a New Pet</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6"><label class="form-label">Pet Name</label>
          <input type="text" name="pet_name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Pet Type</label>
          <select name="pet_type" class="form-select" required>
            <option value="Dog">Dog</option><option value="Cat">Cat</option>
          </select></div>
        <div class="col-md-6"><label class="form-label">Breed</label>
          <input type="text" name="breed" class="form-control" required></div>
        <div class="col-md-3"><label class="form-label">Age (yrs)</label>
          <input type="number" name="age" class="form-control" min="0" required></div>
        <div class="col-md-3"><label class="form-label">Weight (kg)</label>
          <input type="number" name="weight" class="form-control" min="0" step=".1" required></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT PET MODAL -->
<div class="modal fade" id="editPetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content needs-validation" method="POST" novalidate>
      <input type="hidden" name="edit_pet" value="1">
      <input type="hidden" name="pet_id" id="edit_pet_id">
      <div class="modal-header"><h5 class="modal-title">Edit Pet</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6"><label class="form-label">Pet Name</label>
          <input type="text" name="pet_name" id="edit_pet_name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Pet Type</label>
          <select name="pet_type" id="edit_pet_type" class="form-select" required>
            <option value="Dog">Dog</option><option value="Cat">Cat</option>
          </select></div>
        <div class="col-md-6"><label class="form-label">Breed</label>
          <input type="text" name="breed" id="edit_breed" class="form-control" required></div>
        <div class="col-md-3"><label class="form-label">Age (yrs)</label>
          <input type="number" name="age" id="edit_age" class="form-control" min="0" required></div>
        <div class="col-md-3"><label class="form-label">Weight (kg)</label>
          <input type="number" name="weight" id="edit_weight" class="form-control" min="0" step=".1" required></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal fade" id="deletePetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="POST">
      <input type="hidden" name="delete_pet" value="1">
      <input type="hidden" name="pet_id" id="delete_pet_id">
      <div class="modal-body text-center p-4">
        <i class="bi bi-exclamation-circle fs-1 text-danger"></i>
        <h4 class="mt-3 mb-2">Delete <span id="delete_pet_name"></span>?</h4>
        <p class="text-muted mb-4">This action cannot be undone.</p>
        <button class="btn btn-secondary me-2" type="button" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" type="submit">Delete</button>
      </div>
    </form>
  </div>
</div>

<!-- RESULT MODALS (unchanged) -->
<div class="modal fade" id="resultSuccess" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-success">
      <div class="modal-body text-center py-5">
        <i class="bi bi-check-circle fs-1 text-success"></i>
        <h4 class="mt-3 mb-0"><?= htmlspecialchars($modalMsg) ?></h4>
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
/* Activate tooltips */
[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  .forEach(el => new bootstrap.Tooltip(el));

/* Validation */
(() => {
  const forms = document.querySelectorAll('.needs-validation');
  forms.forEach(f => f.addEventListener('submit', e => {
    if (!f.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
    f.classList.add('was-validated');
  }));
})();

/* Fill EDIT modal */
document.querySelectorAll('.editBtn').forEach(btn => {
  btn.addEventListener('click', () => {
    edit_pet_id.value   = btn.dataset.id;
    edit_pet_name.value = btn.dataset.name;
    edit_pet_type.value = btn.dataset.type;
    edit_breed.value    = btn.dataset.breed;
    edit_age.value      = btn.dataset.age;
    edit_weight.value   = btn.dataset.weight;
  });
});

/* Fill DELETE modal */
document.querySelectorAll('.deleteBtn').forEach(btn => {
  btn.addEventListener('click', () => {
    delete_pet_id.value = btn.dataset.id;
    delete_pet_name.textContent = btn.dataset.name;
  });
});

/* Result modal */
<?php if ($modalStatus && $modalStatus !== 'error'): ?>
  new bootstrap.Modal(resultSuccess).show();
<?php elseif ($modalStatus === 'error'): ?>
  new bootstrap.Modal(resultError).show();
<?php endif; ?>
</script>

<!-- Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
