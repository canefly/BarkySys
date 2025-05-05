<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'admin-navigation.php';
include_once '../db.php';

if (!isset($_SESSION['admin'])) {
    echo '<script>alert("Please login to access this page."); window.location.href="admin-login.php";</script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bark & Wiggle – Pricing List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #F7F2EB; 
            font-family: 'Poppins', sans-serif; 
            margin: 0; padding: 40px; 
            display: flex; 
            justify-content: center; }
        
        .container {
        background: white;
        border-radius: 10px;
        width: 100vw;
        margin-left: 70px;
        margin-right: 1em;
        margin-top: 5em;
        padding: 20px;
        transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        body.sidebar-open .container {
            width: calc(100vw - 300px) !important;
            margin-left: 300px !important; 
        }

        .main-wrapper { 
            display: flex; 
            gap: 30px; 
            width: 100%; 
            max-width: 1600px; }

        .card-container { 
            background: #fff; 
            border-radius: 10px; 
            padding: 30px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
            flex: 1; display: flex; 
            flex-direction: column; }

        .card-container h4 { 
            color: #6E3387; 
            font-weight: bold; 
            margin-bottom: 20px; }

        table { 
            font-size: 14px;
             margin-top: 20px; }

        .toast-container { 
            position: fixed; 
            top: 1rem; 
            right: 1rem; 
            z-index: 1200; } 
    </style>
</head>
<body>
<div class="main-wrapper">
    <!-- Left Panel: Weight Services -->
    <div class="card-container">
        <h4>Add New Weight Service</h4>
        <form id="weightForm" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Service Name</label>
                    <select id="serviceName" class="form-select" required>
                        <option disabled selected>Select a size</option>
                        <option value="Small">Small</option>
                        <option value="Medium">Medium</option>
                        <option value="Large">Large</option>
                        <option value="XLarge">XLarge</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Min KG</label>
                    <input type="number" class="form-control" id="minKg" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Max KG</label>
                    <input type="number" class="form-control" id="maxKg" step="0.01" required>
                </div>
            </div>
            <button type="button" class="btn btn-primary mt-3" id="saveServiceBtn">Save Service</button>
        </form>
        <h4>Existing Weight Services</h4>
        <table class="table table-hover">
            <thead class="table-light">
                <tr><th>Service Name</th><th>Min KG</th><th>Max KG</th><th>Actions</th></tr>
            </thead>
            <tbody id="weightServicesTable">
                <tr>
                    <td>
    <select class="form-select form-select-sm weightServiceSelect">
        <option value="Small" selected>Small</option>
        <option value="Medium">Medium</option>
        <option value="Large">Large</option>
        <option value="XLarge">XLarge</option>
    </select>
</td>
                    <td><input type="number" class="form-control form-control-sm" value="0.0"></td>
                    <td><input type="number" class="form-control form-control-sm" value="5.0"></td>
                    <td>
                        <button class="btn btn-sm btn-success saveWeightBtn">Save</button>
                        <button class="btn btn-sm btn-danger deleteWeightBtn">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Right Panel: Pricing -->
    <div class="card-container">
        <h4>Assign Pricing to Service</h4>
        <div class="mb-3">
            <label class="form-label">Service Type</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="serviceType" value="DogGrooming" checked id="radioDog">
                <label class="form-check-label" for="radioDog">Dog Grooming</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="serviceType" value="CatGrooming" id="radioCat">
                <label class="form-check-label" for="radioCat">Cat Grooming</label>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Select Service</label>
            <select id="serviceSelect" class="form-select"><option selected disabled>Choose a Service</option></select>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-5">
                <label class="form-label">Select Weight Service</label>
                <select id="weightSelect" class="form-select"><option selected disabled>Choose a Service</option></select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Set Price (₱)</label>
                <input type="number" class="form-control" id="priceInput" step="0.01">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-success w-100" id="addPricingBtn">Add Pricing</button>
            </div>
        </div>
        <h4>Existing Pricing List</h4>
        <table class="table table-hover">
            <thead class="table-light">
                <tr><th>Service</th><th>Weight Service</th><th>Price</th><th>Actions</th></tr>
            </thead>
            <tbody id="existingPricingTable">
                <tr>
                    <td>Basic Bath</td>
                    <td>Small</td>
                    <td><input type="number" class="form-control form-control-sm" value="250.00"></td>
                    <td>
                        <button class="btn btn-sm btn-success savePricingBtn">Save</button>
                        <button class="btn btn-sm btn-danger deletePricingBtn">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="liveToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMessage"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Are you sure you want to delete? This action is unrecoverable.</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Confirm</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toast setup
  const toastEl = document.getElementById('liveToast');
  const toast = new bootstrap.Toast(toastEl);
  function showToast(msg) { document.getElementById('toastMessage').innerText = msg; toast.show(); }

  // Bind events
  document.getElementById('saveServiceBtn').addEventListener('click', () => showToast('Weight Service saved!'));
  document.querySelectorAll('.saveWeightBtn').forEach(btn => btn.addEventListener('click', () => showToast('Weight Service saved!')));
  document.getElementById('addPricingBtn').addEventListener('click', () => showToast('Pricing successfully added!'));
  document.querySelectorAll('.savePricingBtn').forEach(btn => btn.addEventListener('click', () => showToast('Pricing successfully added!')));
  document.querySelectorAll('.deleteWeightBtn, .deletePricingBtn').forEach(btn => btn.addEventListener('click', () => bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmModal')).show()));
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fetch_services'])) {
    header('Content-Type: application/json');
    $type = $_POST['type'] ?? '';
    $stmt = $conn->prepare("SELECT id, service_name FROM services WHERE service_type = ? AND is_deleted = 0");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    echo json_encode($services);
    exit;
}
?>
