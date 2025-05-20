<?php
/* ============================================================
   Session + DB
   ============================================================ */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once '../db.php';

/* Detect if this is an AJAX (JSON) request */
$isAjax = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']));

/* Only load the sidebar/navigation on full-page GET requests */
if (!$isAjax) {
    include_once 'admin-navigation.php';
}

/* ============================================================
   Handle AJAX actions
   ============================================================ */
if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'];

    /* ---------- WEIGHT CATEGORIES ---------- */
    if ($action === 'add_weight') {
        $stmt = $conn->prepare("INSERT INTO weight_categories (category_name,min_kg,max_kg) VALUES (?,?,?)");
        $stmt->bind_param("sdd", $_POST['name'], $_POST['min'], $_POST['max']);
        $ok = $stmt->execute();
        echo json_encode(['success'=>$ok,'id'=>$conn->insert_id]); exit;
    }
    if ($action === 'update_weight') {
        $stmt = $conn->prepare("UPDATE weight_categories SET category_name=?, min_kg=?, max_kg=? WHERE id=?");
        $stmt->bind_param("sddi", $_POST['name'], $_POST['min'], $_POST['max'], $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }
    if ($action === 'delete_weight') {
        $stmt = $conn->prepare("DELETE FROM weight_categories WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }

    /* ---------- AGE CATEGORIES ---------- */
    if ($action === 'add_age') {
        $stmt = $conn->prepare("INSERT INTO age_categories (species,label,min_months,max_months) VALUES (?,?,?,?)");
        $stmt->bind_param("ssii", $_POST['species'], $_POST['label'], $_POST['min'], $_POST['max']);
        $ok = $stmt->execute();
        echo json_encode(['success'=>$ok,'id'=>$conn->insert_id]); exit;
    }
    if ($action === 'update_age') {
        $stmt = $conn->prepare("UPDATE age_categories SET species=?, label=?, min_months=?, max_months=? WHERE id=?");
        $stmt->bind_param("ssiii", $_POST['species'], $_POST['label'], $_POST['min'], $_POST['max'], $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }
    if ($action === 'delete_age') {
        $stmt = $conn->prepare("DELETE FROM age_categories WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }

    /* ---------- PRICING ---------- */
    if ($action === 'add_pricing') {
        $stmt = $conn->prepare("INSERT INTO pricing (service_id,price,category_id) VALUES (?,?,?)");
        $stmt->bind_param("idi", $_POST['service_id'], $_POST['price'], $_POST['category_id']);
        $ok = $stmt->execute();
        echo json_encode(['success'=>$ok,'id'=>$conn->insert_id]); exit;
    }
    if ($action === 'update_pricing') {
        $stmt = $conn->prepare("UPDATE pricing SET price=? WHERE id=?");
        $stmt->bind_param("di", $_POST['price'], $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }
    if ($action === 'delete_pricing') {
        $stmt = $conn->prepare("DELETE FROM pricing WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }

    /* ---------- SERVICE LIST BY TYPE ---------- */
    if ($action === 'fetch_services') {
        $stmt = $conn->prepare(
            "SELECT id, service_name 
             FROM services 
             WHERE service_type = ?"
        );
        $stmt->bind_param("s", $_POST['type']);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode(['success'=>true,'data'=>$rows]); exit;
    }

    /* ---------- FALLBACK ---------- */
    echo json_encode(['success'=>false,'msg'=>'Invalid action']); exit;
}

/* ============================================================
   (Only runs on a NORMAL page view – not on AJAX)
   ============================================================ */
$weightRows = $conn->query("SELECT * FROM weight_categories ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
$ageRows    = $conn->query("SELECT * FROM age_categories   ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
$pricingRows = $conn->query("
    SELECT p.id, s.service_name, wc.category_name, p.price, p.service_id, p.category_id
    FROM pricing p
    JOIN services s  ON p.service_id  = s.id
    JOIN weight_categories wc ON p.category_id = wc.id
    ORDER BY p.id ASC
")->fetch_all(MYSQLI_ASSOC);
$serviceRows = $conn->query("
    SELECT id,service_name 
    FROM services 
    WHERE service_type='DogGrooming'
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bark & Wiggle – Pricing List</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#F7F2EB;font-family:'Poppins',sans-serif;margin:0;padding:40px 20px;display:flex;justify-content:center;}
.main-wrapper{display:flex;flex-wrap:wrap;gap:30px;max-width:1800px;justify-content:center;width:100%;}
.card-container{background:#fff;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,.1);padding:30px;flex:1 1 480px;max-width:650px;display:flex;flex-direction:column;}
.card-container h4{color:#6E3387;font-weight:bold;margin-bottom:20px;}
table{font-size:14px;margin-top:20px;}
.edit-action{display:none;}
.view-only input,.view-only select{pointer-events:none;user-select:none;background:#f8f9fa;border:none;}
.toast-container{position:fixed;top:1rem;right:1rem;z-index:1200;}
#ageTable select.form-select-sm {
  padding: 0.25rem 0.5rem;
  font-size: 14px;
  height: auto !important;
  min-height: 36px;
  line-height: 1.5;
  background-color: #f8f9fa;
  border: none;
  width: 100%;
}

</style>
</head>
<body>
<div class="main-wrapper">

<!-- =======================  LEFT : WEIGHT ======================= -->
<div class="card-container">
    <h4>Add New Weight Category</h4>
    <form id="weightForm" class="mb-4" onsubmit="return false;">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="wName">Category Name</label>
                <input type="text" id="wName" name="wName" class="form-control" required placeholder="Small / XL / …">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="wMin">Min KG</label>
                <input type="number" id="wMin" name="wMin" class="form-control" step="0.01" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="wMax">Max KG</label>
                <input type="number" id="wMax" name="wMax" class="form-control" step="0.01" required>
            </div>
        </div>
        <button class="btn btn-primary mt-3" onclick="addWeight()">Save Category</button>
    </form>

    <div class="d-flex justify-content-between align-items-center">
        <h4 class="m-0">Existing Weight Categories</h4>
        <button class="btn btn-outline-secondary btn-sm toggle-edit" data-table="weightTable">Edit</button>
    </div>

    <table class="table table-hover view-only" id="weightTable">
        <thead class="table-light"><tr><th>Name</th><th>Min KG</th><th>Max KG</th><th>Actions</th></tr></thead>
        <tbody id="weightBody">
        <?php foreach($weightRows as $r): ?>
            <tr data-id="<?= $r['id'] ?>">
                <td><input class="form-control form-control-sm" value="<?= htmlspecialchars($r['category_name']) ?>"></td>
                <td><input type="number" class="form-control form-control-sm" value="<?= $r['min_kg'] ?>"></td>
                <td><input type="number" class="form-control form-control-sm" value="<?= $r['max_kg'] ?>"></td>
                <td>
                    <button class="btn btn-sm btn-success edit-action" onclick="saveWeight(this)">Save</button>
                    <button class="btn btn-sm btn-danger  edit-action" onclick="delWeight(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>

<!-- =======================  MIDDLE : AGE ======================= -->
<div class="card-container">
    <h4>Add New Age Category</h4>
    <form id="ageForm" class="mb-4" onsubmit="return false;">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label" for="aSpecies">Species</label>
                <select id="aSpecies" class="form-select" required>
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="aLabel">Label</label>
                <input type="text" id="aLabel" class="form-control" required placeholder="Kitten / Adult / Senior">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="aMin">Min (Months)</label>
                <input type="number" id="aMin" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="aMax">Max (Months)</label>
                <input type="number" id="aMax" class="form-control" required>
            </div>
        </div>
        <button class="btn btn-primary mt-3" onclick="addAge()">Save Category</button>
    </form>

    <div class="d-flex justify-content-between align-items-center">
        <h4 class="m-0">Existing Age Categories</h4>
        <button class="btn btn-outline-secondary btn-sm toggle-edit" data-table="ageTable">Edit</button>
    </div>

    <table class="table table-hover view-only" id="ageTable">
        <thead class="table-light"><tr><th>Species</th><th>Label</th><th>Min M</th><th>Max M</th><th>Actions</th></tr></thead>
        <tbody id="ageBody">
        <?php foreach($ageRows as $a): ?>
            <tr data-id="<?= $a['id'] ?>">
                <td>
                    <select class="form-select form-select-sm">
                        <option value="Dog"<?= $a['species']==='Dog'?' selected':'';?>>Dog</option>
                        <option value="Cat"<?= $a['species']==='Cat'?' selected':'';?>>Cat</option>
                    </select>
                </td>
                <td><input class="form-control form-control-sm" value="<?= htmlspecialchars($a['label']) ?>"></td>
                <td><input type="number" class="form-control form-control-sm" value="<?= $a['min_months'] ?>"></td>
                <td><input type="number" class="form-control form-control-sm" value="<?= $a['max_months'] ?>"></td>
                <td>
                    <button class="btn btn-sm btn-success edit-action" onclick="saveAge(this)">Save</button>
                    <button class="btn btn-sm btn-danger  edit-action" onclick="delAge(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>

<!-- =======================  RIGHT : PRICING ====================== -->
<div class="card-container">
    <h4>Assign Pricing to Service</h4>

    <div class="mb-3">
        <label class="form-label">Service Type</label><br>
        <div class="form-check form-check-inline">
            <input class="form-check-input service-type" type="radio" name="serviceType" value="DogGrooming" id="radioDog" checked>
            <label class="form-check-label" for="radioDog">Dog Grooming</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input service-type" type="radio" name="serviceType" value="CatGrooming" id="radioCat">
            <label class="form-check-label" for="radioCat">Cat Grooming</label>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="serviceSelect">Select Service</label>
        <select id="serviceSelect" class="form-select" required>
            <option selected disabled value="">Choose a Service</option>
            <?php foreach($serviceRows as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['service_name']) ?></option>
            <?php endforeach;?>
        </select>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-5">
            <label class="form-label" for="weightSelect">Select Weight Category</label>
            <select id="weightSelect" class="form-select" required>
                <option selected disabled value="">Choose a Category</option>
                <?php foreach($weightRows as $w): ?>
                    <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['category_name']) ?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="priceInput">Set Price (₱)</label>
            <input type="number" id="priceInput" class="form-control" step="0.01" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-success w-100" onclick="addPricing()">Add Pricing</button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <h4 class="m-0">Existing Pricing List</h4>
        <button class="btn btn-outline-secondary btn-sm toggle-edit" data-table="pricingTable">Edit</button>
    </div>

    <table class="table table-hover view-only" id="pricingTable">
        <thead class="table-light"><tr><th>Service</th><th>Weight</th><th>Price</th><th>Actions</th></tr></thead>
        <tbody id="pricingBody">
        <?php foreach($pricingRows as $p): ?>
            <tr data-id="<?= $p['id'] ?>" data-service="<?= $p['service_id'] ?>" data-weight="<?= $p['category_id'] ?>">
                <td><?= htmlspecialchars($p['service_name']) ?></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td><input type="number" class="form-control form-control-sm" value="<?= $p['price'] ?>"></td>
                <td>
                    <button class="btn btn-sm btn-success edit-action" onclick="savePricing(this)">Save</button>
                    <button class="btn btn-sm btn-danger  edit-action" onclick="delPricing(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>
</div><!-- /main-wrapper -->

<!-- =======================  TOAST + MODAL  ======================= -->
<div class="toast-container p-3">
  <div id="liveToast" class="toast align-items-center text-bg-primary border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Confirm Deletion</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">Delete this entry? This cannot be undone.</div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Confirm</button>
    </div>
  </div></div>
</div>

<!-- =======================  JS  ======================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ---------- Toast helpers ---------- */
const toastObj = new bootstrap.Toast(document.getElementById('liveToast'));
const showToast = (msg,isOk=true)=>{
  const t=document.getElementById('liveToast');
  t.classList.toggle('text-bg-danger',!isOk);
  t.classList.toggle('text-bg-primary',isOk);
  document.getElementById('toastMsg').innerText = msg;
  toastObj.show();
};

/* ---------- Modal helper ---------- */
const modal   = new bootstrap.Modal(document.getElementById('confirmModal'));
let toDelete  = null;

/* ---------- shorthands ---------- */
const qs  = s=>document.querySelector(s);
const qsa = s=>document.querySelectorAll(s);

/* ---------- Robust POST wrapper ---------- */
async function post(body){
  try{
    const res = await fetch(location.href,{
      method : 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body   : new URLSearchParams(body)
    });
    const txt = await res.text();
    try{ return JSON.parse(txt); }
    catch(e){ console.error('Non-JSON response:',txt); return {success:false}; }
  }catch(err){ console.error(err); return {success:false}; }
}

/* =====================================================
   EDIT / VIEW toggle
   ===================================================== */
qsa('.toggle-edit').forEach(btn=>btn.addEventListener('click',()=>toggleEdit(btn)));
function toggleEdit(btn){
  const tbl   = document.getElementById(btn.dataset.table);
  const view  = tbl.classList.contains('view-only');
  tbl.classList.toggle('view-only',!view);
  btn.textContent = view?'Done':'Edit';
  tbl.querySelectorAll('input,select').forEach(i=>{
    i.disabled = !view ? true:false;
    i.style.background = view? '#fff':'#f8f9fa';
  });
  tbl.querySelectorAll('.edit-action').forEach(el=>el.style.display=view?'inline-block':'none');
}

/* =====================================================
   Service dropdown loader
   ===================================================== */
async function loadServices(type){
  const res = await post({action:'fetch_services',type});
  if(!res.success){ showToast('Could not load services',false); return; }
  const sel = qs('#serviceSelect');
  sel.innerHTML='<option selected disabled value="">Choose a Service</option>';
  res.data.forEach(s=>sel.insertAdjacentHTML('beforeend',`<option value="${s.id}">${s.service_name}</option>`));
  showToast(`${type==='DogGrooming'?'Dog':'Cat'} services loaded`);
}
qsa('.service-type').forEach(r=>r.addEventListener('change',()=>loadServices(r.value)));

/* =====================================================
   WEIGHT CRUD
   ===================================================== */
async function addWeight(){
  const name=qs('#wName').value.trim(),min=qs('#wMin').value,max=qs('#wMax').value;
  if(!name||!min||!max){ showToast('Fill all weight fields',false); return; }
  const r=await post({action:'add_weight',name,min,max});
  if(r.success){
     qs('#weightBody').insertAdjacentHTML('beforeend',`
       <tr data-id="${r.id}">
         <td><input class="form-control form-control-sm" value="${name}"></td>
         <td><input type="number" class="form-control form-control-sm" value="${min}"></td>
         <td><input type="number" class="form-control form-control-sm" value="${max}"></td>
         <td>
           <button class="btn btn-sm btn-success edit-action" onclick="saveWeight(this)">Save</button>
           <button class="btn btn-sm btn-danger  edit-action" onclick="delWeight(this)">Delete</button>
         </td>
       </tr>`);
     qs('#weightSelect').insertAdjacentHTML('beforeend',`<option value="${r.id}">${name}</option>`);
     qs('#weightForm').reset();
     showToast('Weight category added');
  } else showToast('Add failed',false);
}
async function saveWeight(btn){
  const tr=btn.closest('tr'),id=tr.dataset.id,[name,min,max]=[...tr.querySelectorAll('input')].map(i=>i.value);
  const r=await post({action:'update_weight',id,name,min,max});
  showToast(r.success?'Weight updated':'Update failed',!!r.success);
  if(r.success&&qs('.toggle-edit[data-table="weightTable"]').textContent==='Done')toggleEdit(qs('.toggle-edit[data-table="weightTable"]'));
}
function delWeight(btn){ toDelete={row:btn.closest('tr'),action:'delete_weight'}; modal.show(); }

/* =====================================================
   AGE CRUD
   ===================================================== */
async function addAge(){
  const species=qs('#aSpecies').value,label=qs('#aLabel').value.trim(),min=qs('#aMin').value,max=qs('#aMax').value;
  if(!label||!min||!max){ showToast('Fill all age fields',false); return; }
  const r=await post({action:'add_age',species,label,min,max});
  if(r.success){
     qs('#ageBody').insertAdjacentHTML('beforeend',`
       <tr data-id="${r.id}">
         <td>
           <select class="form-select form-select-sm">
             <option value="Dog"${species==='Dog'?' selected':''}>Dog</option>
             <option value="Cat"${species==='Cat'?' selected':''}>Cat</option>
           </select>
         </td>
         <td><input class="form-control form-control-sm" value="${label}"></td>
         <td><input type="number" class="form-control form-control-sm" value="${min}"></td>
         <td><input type="number" class="form-control form-control-sm" value="${max}"></td>
         <td>
           <button class="btn btn-sm btn-success edit-action" onclick="saveAge(this)">Save</button>
           <button class="btn btn-sm btn-danger  edit-action" onclick="delAge(this)">Delete</button>
         </td>
       </tr>`);
     qs('#ageForm').reset();
     showToast('Age category added');
  } else showToast('Add failed',false);
}
async function saveAge(btn){
  const tr=btn.closest('tr'),id=tr.dataset.id,
        species=tr.querySelector('select').value,
        [label,min,max]=[...tr.querySelectorAll('input')].map(i=>i.value);
  const r=await post({action:'update_age',id,species,label,min,max});
  showToast(r.success?'Age updated':'Update failed',!!r.success);
  if(r.success&&qs('.toggle-edit[data-table="ageTable"]').textContent==='Done')toggleEdit(qs('.toggle-edit[data-table="ageTable"]'));
}
function delAge(btn){ toDelete={row:btn.closest('tr'),action:'delete_age'}; modal.show(); }

/* =====================================================
   PRICING CRUD
   ===================================================== */
async function addPricing(){
  const serviceId=qs('#serviceSelect').value, catId=qs('#weightSelect').value, priceVal=qs('#priceInput').value;
  if(!serviceId||!catId||!priceVal){ showToast('Fill all pricing fields',false); return; }
  const r=await post({action:'add_pricing',service_id:serviceId,category_id:catId,price:priceVal});
  if(r.success){
      const sName=qs(`#serviceSelect option[value="${serviceId}"]`).textContent,
            cName=qs(`#weightSelect  option[value="${catId}"]`).textContent;
      qs('#pricingBody').insertAdjacentHTML('beforeend',`
        <tr data-id="${r.id}" data-service="${serviceId}" data-weight="${catId}">
          <td>${sName}</td><td>${cName}</td>
          <td><input type="number" class="form-control form-control-sm" value="${priceVal}"></td>
          <td>
            <button class="btn btn-sm btn-success edit-action" onclick="savePricing(this)">Save</button>
            <button class="btn btn-sm btn-danger  edit-action" onclick="delPricing(this)">Delete</button>
          </td>
        </tr>`);
      qs('#priceInput').value='';
      showToast('Pricing added');
  } else showToast('Add pricing failed',false);
}
async function savePricing(btn){
  const tr=btn.closest('tr'),id=tr.dataset.id,price=tr.querySelector('input').value;
  const r=await post({action:'update_pricing',id,price});
  showToast(r.success?'Price updated':'Update failed',!!r.success);
  if(r.success&&qs('.toggle-edit[data-table="pricingTable"]').textContent==='Done')toggleEdit(qs('.toggle-edit[data-table="pricingTable"]'));
}
function delPricing(btn){ toDelete={row:btn.closest('tr'),action:'delete_pricing'}; modal.show(); }

/* =====================================================
   SHARED DELETE CONFIRM
   ===================================================== */
qs('#confirmDeleteBtn').addEventListener('click',async ()=>{
  const id=toDelete.row.dataset.id;
  const r =await post({action:toDelete.action,id});
  if(r.success){
      if(toDelete.action==='delete_weight')qs(`#weightSelect option[value="${id}"]`)?.remove();
      toDelete.row.remove();
      showToast('Deleted');
  }else showToast('Delete failed',false);
  modal.hide();
});
</script>
</body>
</html>
