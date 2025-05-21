<?php
/*────────────────────────────────────────────────────────────────────────────
  BARKSYS — ADMIN SERVICES LIST
  • Drag-to-reorder rows  (display_order persists)
  • Floating Bootstrap Toasts for success / error
  • Unified Add / Edit modal
  • IDs hidden (drag handle instead)
────────────────────────────────────────────────────────────────────────────*/
session_start();
include_once 'admin-auth.php';
include_once 'admin-navigation.php';
include_once '../db.php';
include_once '../helpers/path-helper.php';

/*─────────────────────────── ensure `display_order` column exists ─────────────────────────*/
mysqli_query(
    $conn,
    "ALTER TABLE services
     ADD COLUMN IF NOT EXISTS display_order INT AFTER id"
);
mysqli_query(
    $conn,
    "UPDATE services
       SET display_order = id
     WHERE display_order IS NULL"
);

/*─────────────────────────── helper functions ─────────────────────────*/
function flash($type, $msg)  { $_SESSION["flash_$type"] = $msg; }
function clean($c, $k, $d=''){ return isset($_POST[$k]) ? mysqli_real_escape_string($c, trim($_POST[$k])) : $d; }

/*─────────────────────────── AJAX reorder endpoint ────────────────────*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['ajax']) && $_GET['ajax'] === 'reorder') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['order']) || !is_array($data['order'])) {
        http_response_code(400);
        echo json_encode(['ok'=>false]);
        exit;
    }
    $stmt = mysqli_prepare($conn, "UPDATE services SET display_order=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ii', $pos, $id);
    foreach ($data['order'] as $pos => $id) { $pos++; mysqli_stmt_execute($stmt); }
    echo json_encode(['ok'=>true]);
    exit;
}

/*─────────────────────────── ADD / UPDATE / DELETE ────────────────────*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $act = clean($conn, 'action');
    $sid = clean($conn, 'service_id');

    /* ---------- ADD ---------- */
    if ($act === 'add') {
        $stype = clean($conn, 'servicesType');
        $mode  = clean($conn, 'mode', 'individual');
        $name  = clean($conn, 'service_name');
        $desc  = clean($conn, 'service_description');
        $pRaw  = clean($conn, 'service_price', null);

        if (!isset($_FILES['service_image']) || $_FILES['service_image']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Please upload a valid image.'); header('Location: '.$_SERVER['PHP_SELF']); exit;
        }
        $upDir = "../uploads/"; if (!is_dir($upDir)) mkdir($upDir, 0777, true);
        $uniq  = time().'_'.preg_replace('/\s+/', '_', basename($_FILES['service_image']['name']));
        $rel   = "uploads/$uniq"; $full = $upDir.$uniq;
        $ext   = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png']) || !move_uploaded_file($_FILES['service_image']['tmp_name'], $full)) {
            flash('error', 'Image upload failed or wrong type.'); header('Location: '.$_SERVER['PHP_SELF']); exit;
        }
        $priceVal = ($mode === 'package') ? 'NULL' : "'".sprintf('%.2f', $pRaw)."'";
        $sql = "INSERT INTO services
                   (service_type, mode, service_name, service_description, service_price, service_image, display_order)
                 VALUES
                   ('$stype', '$mode', '$name', '$desc', $priceVal, '$rel',
                    (SELECT IFNULL(MAX(display_order),0)+1 FROM (SELECT display_order FROM services) AS t))";
        if (mysqli_query($conn, $sql)) flash('success', 'Service added.');
        else                           flash('error',  'Add failed: '.mysqli_error($conn));
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }

    /* ---------- UPDATE ---------- */
    if ($act === 'update') {
        $stype = clean($conn, 'servicesType');
        $mode  = clean($conn, 'mode', 'individual');
        $name  = clean($conn, 'service_name');
        $desc  = clean($conn, 'service_description');
        $pRaw  = clean($conn, 'service_price', null);
        $priceVal = ($mode === 'package' || $pRaw === '') ? 'NULL' : "'".sprintf('%.2f', $pRaw)."'";

        $imgSet = '';
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
            $upDir = "../uploads/"; if (!is_dir($upDir)) mkdir($upDir, 0777, true);
            $uniq  = time().'_'.preg_replace('/\s+/', '_', basename($_FILES['service_image']['name']));
            $rel   = "uploads/$uniq"; $full = $upDir.$uniq;
            $ext   = strtolower(pathinfo($full, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png']) && move_uploaded_file($_FILES['service_image']['tmp_name'], $full)) {
                $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT service_image FROM services WHERE id='$sid'"));
                if ($old) { $op = resolveUploadPath($old['service_image']); if ($op && file_exists($op)) unlink($op); }
                $imgSet = ", service_image='$rel'";
            }
        }

        $sql = "UPDATE services SET
                  service_type='$stype',
                  mode='$mode',
                  service_name='$name',
                  service_description='$desc',
                  service_price=$priceVal
                  $imgSet
                WHERE id='$sid'";
        if (mysqli_query($conn, $sql)) flash('success', 'Service updated.');
        else                           flash('error',  'Update failed: '.mysqli_error($conn));
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }

    /* ---------- DELETE ---------- */
    if ($act === 'delete') {
        $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT service_image FROM services WHERE id='$sid'"));
        mysqli_query($conn, "DELETE FROM services WHERE id='$sid'");
        if ($old) { $p = resolveUploadPath($old['service_image']); if ($p && file_exists($p)) unlink($p); }
        flash('success', 'Service deleted.'); header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
}

/*─────────────────────────── SEARCH / FETCH LIST ─────────────────────────*/
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = $search ? "WHERE service_name LIKE '%".mysqli_real_escape_string($conn, $search)."%'
                     OR service_type LIKE '%".mysqli_real_escape_string($conn, $search)."%'" : '';
$list = $conn->query("SELECT * FROM services $filter ORDER BY display_order ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Services</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<style>
 body{font-family:Helvetica,Arial,sans-serif;background:#ECE3DA}
 .table-img{width:64px;height:64px;object-fit:cover;border-radius:6px}
 .badge-mode{background:#795548}.badge-cat{background:#f48fb1}.badge-dog{background:#90caf9}
 .drag-handle{cursor:grab;color:#888;width:20px;text-align:center}
 .toast-container{z-index:1080}
</style>
</head>
<body>
<div class="container-fluid py-4">

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
     <h2 class="fw-bold mb-0">Services</h2>

     <form class="d-flex align-items-center" method="get">
        <input class="form-control me-2" type="search" name="q"
               placeholder="Search…" value="<?= htmlspecialchars($search); ?>" />
        <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
     </form>

     <button class="btn btn-success" data-bs-toggle="modal"
             data-bs-target="#serviceModal" id="addBtn">
       <i class="fas fa-plus me-1"></i>Add Service
     </button>
  </div>

  <?php if($list->num_rows): ?>
  <div class="table-responsive shadow-sm">
    <table class="table align-middle table-hover bg-white" id="servicesTable">
      <thead class="table-dark">
        <tr>
          <th style="width:35px;"></th>
          <th>Image</th><th>Service</th><th>Cat.</th>
          <th>Mode</th><th>Price</th>
          <th class="text-center" style="width:130px;">Actions</th>
        </tr>
      </thead>
      <tbody id="servicesBody">
      <?php while($r=$list->fetch_assoc()):
        $cat = stripos($r['service_type'],'cat')!==false?'Cat':'Dog';
        $badge=$cat==='Cat'?'badge-cat':'badge-dog';
        $price=is_null($r['service_price'])?'<span class="text-muted">Weight-based</span>'
                                          :'₱'.number_format($r['service_price'],2);
      ?>
      <tr data-id="<?= $r['id']; ?>">
        <td class="drag-handle"><i class="fas fa-grip-vertical"></i></td>
        <td><img src="../<?= ltrim($r['service_image'],'/'); ?>" class="table-img"></td>
        <td style="max-width:260px;">
            <div class="fw-semibold"><?= htmlspecialchars($r['service_name']); ?></div>
            <small class="text-muted"><?= htmlspecialchars($r['service_description']); ?></small>
        </td>
        <td><span class="badge <?= $badge; ?>"><?= $cat; ?></span></td>
        <td><span class="badge badge-mode"><?= ucfirst($r['mode']); ?></span></td>
        <td><?= $price; ?></td>
        <td class="text-center">
          <button class="btn btn-outline-primary btn-sm me-1 editBtn"
                  data-bs-toggle="modal" data-bs-target="#serviceModal"
                  data-id="<?= $r['id']; ?>"
                  data-type="<?= $r['service_type']; ?>"
                  data-mode="<?= $r['mode']; ?>"
                  data-name="<?= htmlspecialchars($r['service_name'],ENT_QUOTES); ?>"
                  data-desc="<?= htmlspecialchars($r['service_description'],ENT_QUOTES); ?>"
                  data-price="<?= $r['service_price']; ?>">
            <i class="fas fa-pen"></i>
          </button>
          <button class="btn btn-outline-danger btn-sm"
                  data-bs-toggle="modal" data-bs-target="#deleteModal"
                  data-id="<?= $r['id']; ?>"
                  data-name="<?= htmlspecialchars($r['service_name'],ENT_QUOTES); ?>">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?><p class="text-center">No services found.</p><?php endif; ?>
</div>

<!-- Toasts -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <?php if(!empty($_SESSION['flash_success'])): ?>
    <div class="toast align-items-center text-white bg-success border-0" role="alert" data-bs-delay="4000">
       <div class="d-flex">
         <div class="toast-body"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
         <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
       </div>
    </div>
  <?php endif; ?>
  <?php if(!empty($_SESSION['flash_error'])): ?>
    <div class="toast align-items-center text-white bg-danger border-0" role="alert" data-bs-delay="4000">
       <div class="d-flex">
         <div class="toast-body"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
         <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
       </div>
    </div>
  <?php endif; ?>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal fade" id="serviceModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" method="POST" enctype="multipart/form-data" id="serviceForm" novalidate>
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="service_id" value="">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalTitle">Add New Service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Type of Service</label>
          <select class="form-select" name="servicesType" required>
            <option value="" disabled>Select an option</option>
            <option value="DogGrooming">Dog Grooming</option>
            <option value="CatGrooming">Cat Grooming</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Service Mode</label><br>
          <div class="form-check form-check-inline">
            <input class="form-check-input mode-radio" type="radio" name="mode" value="individual" id="radioIndiv">
            <label class="form-check-label" for="radioIndiv">Individual</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input mode-radio" type="radio" name="mode" value="package" id="radioPack">
            <label class="form-check-label" for="radioPack">Package</label>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Service Name</label>
          <input type="text" class="form-control" name="service_name" required>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Service Description</label>
          <textarea rows="3" class="form-control" name="service_description" required></textarea>
        </div>
        <div class="col-md-6 price-div">
          <label class="form-label fw-semibold">Service Price (₱)</label>
          <input type="number" step="0.01" min="0" class="form-control" name="service_price" placeholder="0.00">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Service Image</label>
          <input type="file" class="form-control" name="service_image" accept=".jpg,.jpeg,.png">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="POST">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="service_id" id="deleteServiceId">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold">Delete Service</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Delete <strong id="deleteServiceName"></strong>?</p>
        <p class="text-danger small mb-0">This cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger">Yes, delete it</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* activate toasts */
document.querySelectorAll('.toast').forEach(t=>new bootstrap.Toast(t).show());

/* sortable rows */
$('#servicesBody').sortable({
  axis:'y',
  handle:'.drag-handle',
  update:function(){
    const order=$(this).children().map(function(){return $(this).data('id');}).get();
    fetch('?ajax=reorder',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({order})});
  }
}).disableSelection();

/* mode toggle price input */
const modal=document.getElementById('serviceModal');
const priceDiv=modal.querySelector('.price-div');
const priceInp=priceDiv.querySelector('input');
modal.addEventListener('change',e=>{
  if(e.target.classList.contains('mode-radio')){
    if(e.target.value==='package'){priceDiv.style.display='none';priceInp.removeAttribute('required');}
    else {priceDiv.style.display='block';priceInp.setAttribute('required','');}
  }
});

/* ADD button */
document.getElementById('addBtn').addEventListener('click',()=>{
  modal.querySelector('#modalTitle').textContent='Add New Service';
  modal.querySelector('input[name="action"]').value='add';
  modal.querySelector('input[name="service_id"]').value='';
  modal.querySelector('select[name="servicesType"]').selectedIndex=0;
  modal.querySelector('#radioIndiv').checked=true;
  priceDiv.style.display='block';priceInp.setAttribute('required','');priceInp.value='';
  modal.querySelector('input[name="service_name"]').value='';
  modal.querySelector('textarea[name="service_description"]').value='';
  modal.querySelector('input[name="service_image"]').setAttribute('required','');
});

/* EDIT buttons */
document.querySelectorAll('.editBtn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    modal.querySelector('#modalTitle').textContent='Edit Service';
    modal.querySelector('input[name="action"]').value='update';
    modal.querySelector('input[name="service_id"]').value=btn.dataset.id;
    modal.querySelector('select[name="servicesType"]').value=btn.dataset.type;
    const isPkg=btn.dataset.mode==='package';
    modal.querySelector('#radioIndiv').checked=!isPkg;
    modal.querySelector('#radioPack').checked=isPkg;
    if(isPkg){priceDiv.style.display='none';priceInp.removeAttribute('required');}
    else {priceDiv.style.display='block';priceInp.setAttribute('required','');}
    modal.querySelector('input[name="service_name"]').value=btn.dataset.name;
    modal.querySelector('textarea[name="service_description"]').value=btn.dataset.desc;
    priceInp.value=(btn.dataset.price===''||btn.dataset.price==='null')?'':parseFloat(btn.dataset.price).toFixed(2);
    modal.querySelector('input[name="service_image"]').removeAttribute('required');
  });
});

/* fill delete modal */
document.getElementById('deleteModal').addEventListener('show.bs.modal',e=>{
  const btn=e.relatedTarget;
  document.getElementById('deleteServiceId').value=btn.dataset.id;
  document.getElementById('deleteServiceName').textContent=btn.dataset.name;
});
</script>
</body>
</html>
