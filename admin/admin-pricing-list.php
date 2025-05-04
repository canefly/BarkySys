<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'admin-navigation.php';
include_once '../db.php';

// Ensure user is logged in
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
        margin: 0;
        padding: 0;
        transition: margin-left 0.3s ease-in-out;
    }

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
        width: calc(100vw - 300px);
        margin-left: 300px;
    }

    h2 {
        margin-bottom: 20px;
        color: #6E3387;
        font-size: 24px;
        font-weight: bold;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }


    </style>
</head>
<body>
    <div class="container">
        <!-- Weight Category Form -->
        <div class="card p-4">
            <h4>Add New Weight Category</h4>
            <form id="weightForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="category_name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Min KG</label>
                        <input type="number" class="form-control" name="min_kg" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Max KG</label>
                        <input type="number" class="form-control" name="max_kg" step="0.01" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save Category</button>
            </form>
        </div>

        <!-- Pricing Table by Service -->
        <div class="card p-4">
            <h4>Assign Pricing to Service</h4>
            <div class="mb-3">
                <label class="form-label">Service Type</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="serviceType" id="dogGrooming" value="DogGrooming" checked>
                    <label class="form-check-label" for="dogGrooming">Dog Grooming</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="serviceType" id="catGrooming" value="CatGrooming">
                    <label class="form-check-label" for="catGrooming">Cat Grooming</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="serviceSelect" class="form-label">Select Service</label>
                <select id="serviceSelect" class="form-select">
                    <option selected disabled>Choose a Service</option>
                    <!-- Options will be filled via JavaScript -->
                </select>
            </div>

            <div id="pricingTable">
                <!-- JS-generated pricing rows per category will appear here -->
            </div>
        </div>
    </div>

    <script>
        const pricingTable = document.getElementById('pricingTable');
        const serviceSelect = document.getElementById('serviceSelect');
        const serviceRadios = document.querySelectorAll('input[name="serviceType"]');

        function loadServices(type) {
            fetch(`get_services_by_type.php?type=${type}`)
                .then(res => res.json())
                .then(data => {
                    serviceSelect.innerHTML = '<option disabled selected>Choose a Service</option>';
                    data.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.id;
                        option.textContent = service.service_name;
                        serviceSelect.appendChild(option);
                    });
                });
        }

        // Load initial services (DogGrooming by default)
        loadServices('DogGrooming');

        serviceRadios.forEach(radio => {
            radio.addEventListener('change', e => {
                loadServices(e.target.value);
                pricingTable.innerHTML = '';
            });
        });

        serviceSelect.addEventListener('change', async () => {
            const serviceId = serviceSelect.value;
            if (!serviceId) return;

            const response = await fetch(`get_pricing_by_service.php?service_id=${serviceId}`);
            const data = await response.json();

            pricingTable.innerHTML = `
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th>Price (PHP)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map(row => `
                            <tr>
                                <td>${row.category_name}</td>
                                <td><input type="number" class="form-control" value="${row.price}" data-id="${row.id}" /></td>
                                <td><button class="btn btn-sm btn-success">Save</button></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        });
        // Initial Load
        loadServices('DogGrooming');
    </script>
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


</body>
</html>
