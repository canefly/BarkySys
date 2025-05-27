<?php
/* ============================================================
   SETUP
   ============================================================ */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once '../db.php';
include_once 'audit-log.php';
include_once 'admin-navigation.php';

$isAjax  = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']));
$adminId = $_SESSION['admin_id'] ?? null;   // make sure admin is logged-in

/* ============================================================
   =============== AJAX ENDPOINTS (JSON OUT) ===================
   ============================================================ */
if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'] ?? '';

    /* ---------- weights ---------- */
    if ($action === 'add_weight') {
        $stmt = $conn->prepare(
            "INSERT INTO weight_categories (category_name,min_kg,max_kg)
             VALUES (?,?,?)"
        );
        $stmt->bind_param("sdd", $_POST['name'], $_POST['min'], $_POST['max']);
        $ok = $stmt->execute();
        if ($ok) log_audit($adminId,'admin','add_weight',
            "Added weight cat '{$_POST['name']}' ({$_POST['min']}–{$_POST['max']} kg)",
            'weight_categories',$conn->insert_id);
        echo json_encode(['success'=>$ok,'id'=>$conn->insert_id]); exit;
    }
    if ($action === 'update_weight') {
        $stmt = $conn->prepare(
            "UPDATE weight_categories SET category_name=?,min_kg=?,max_kg=? WHERE id=?"
        );
        $stmt->bind_param("sddi", $_POST['name'], $_POST['min'], $_POST['max'], $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }
    if ($action === 'delete_weight') {
        $stmt = $conn->prepare("DELETE FROM weight_categories WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }

    /* ---------- ages ---------- */
    if ($action === 'add_age') {
        $stmt = $conn->prepare(
            "INSERT INTO age_categories (species,label,min_months,max_months)
             VALUES (?,?,?,?)"
        );
        $stmt->bind_param("ssii", $_POST['species'], $_POST['label'],
                                   $_POST['min'], $_POST['max']);
        $ok = $stmt->execute();
        if ($ok) log_audit($adminId,'admin','add_age',
            "Added age cat '{$_POST['label']}' ({$_POST['min']}–{$_POST['max']} mo, {$_POST['species']})",
            'age_categories',$conn->insert_id);
        echo json_encode(['success'=>$ok,'id'=>$conn->insert_id]); exit;
    }
    if ($action === 'update_age') {
        $stmt = $conn->prepare(
            "UPDATE age_categories SET species=?,label=?,min_months=?,max_months=? WHERE id=?"
        );
        $stmt->bind_param("ssiii", $_POST['species'], $_POST['label'],
                                    $_POST['min'], $_POST['max'], $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }
    if ($action === 'delete_age') {
        $stmt = $conn->prepare("DELETE FROM age_categories WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        echo json_encode(['success'=>$stmt->execute()]); exit;
    }

    /* ---------- pricing ---------- */
    if ($action === 'add_pricing') {
        $svc   = $_POST['service_id'];
        $price = $_POST['price'];
        $catId = $_POST['category_id'];
        $type  = $_POST['service_type'];           // DogGrooming | CatGrooming

        if ($type === 'DogGrooming') {
            $stmt = $conn->prepare(
              "INSERT INTO pricing (service_id,price,weight_category_id)
               VALUES (?,?,?)"
            );
        } else {                                   // CatGrooming
            $stmt = $conn->prepare(
              "INSERT INTO pricing (service_id,price,age_category_id)
               VALUES (?,?,?)"
            );
        }
        $stmt->bind_param("idi",$svc,$price,$catId);
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

    /* ---------- service list by type ---------- */
    if ($action === 'fetch_services') {
        $stmt = $conn->prepare(
            "SELECT id,service_name FROM services WHERE service_type=?"
        );
        $stmt->bind_param("s", $_POST['type']);
        $stmt->execute();
        echo json_encode(['success'=>true,
            'data'=>$stmt->get_result()->fetch_all(MYSQLI_ASSOC)]); exit;
    }

    echo json_encode(['success'=>false,'msg'=>'Invalid action']); exit;
}

/* ============================================================
   =========== PAGE-LOAD QUERIES (NON-AJAX) ====================
   ============================================================ */

/* weight + age lists */
$weightRows = $conn->query(
    "SELECT id,category_name,min_kg,max_kg FROM weight_categories ORDER BY id"
)->fetch_all(MYSQLI_ASSOC);

$ageRows = $conn->query(
    "SELECT id,species,label,min_months,max_months
     FROM age_categories
     /* remove WHERE if you plan to show dog-age rows too */
     WHERE species='Cat'
     ORDER BY id"
)->fetch_all(MYSQLI_ASSOC);


/* unified pricing list */
$pricingRows = $conn->query("
   SELECT p.id,
          s.service_name,
          s.service_type,
          COALESCE(wc.category_name, ac.label) AS category_name,
          p.price,
          p.service_id,
          COALESCE(p.weight_category_id,p.age_category_id) AS cat_id
   FROM pricing p
   JOIN services s ON p.service_id = s.id
   LEFT JOIN weight_categories wc ON p.weight_category_id = wc.id
   LEFT JOIN age_categories   ac ON p.age_category_id    = ac.id
   ORDER BY p.id
")->fetch_all(MYSQLI_ASSOC);

/* default service list = Dog grooming */
$serviceRows = $conn->query(
    "SELECT id,service_name FROM services WHERE service_type='DogGrooming'"
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bark & Wiggle – Pricing</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#F7F2EB;font-family:'Poppins',sans-serif;margin:0;padding:40px 20px;display:flex;justify-content:center;}
.main-wrapper{display:flex;flex-wrap:wrap;gap:30px;max-width:1800px;width:100%;}
.card-container{flex:1 1 480px;max-width:650px;background:#fff;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,.08);padding:30px;display:flex;flex-direction:column;}
.card-container h4{color:#6E3387;font-weight:600;margin-bottom:20px;}
table{font-size:14px;margin-top:20px;}
.view-only input,.view-only select{pointer-events:none;background:#f8f9fa;border:none;}
.edit-action{display:none;}
.toast-container{position:fixed;top:1rem;right:1rem;z-index:1200;}
/* === Fix species dropdown getting cut in Age Categories table === */
#ageTable td {               /* let the cell expand */
  white-space: nowrap;       /* prevent wrap-then-clip */
}

#ageTable select.form-select-sm {
  width: 100%;               /* fill the entire cell */
  min-width: 6rem;           /* don’t shrink below 6 rem (~96 px) */
  box-sizing: border-box;    /* include padding in width calc */
  padding: 0.25rem 0.5rem;   /* same comfy padding you used */
  background: #f8f9fa;
  border: none;
  overflow: visible;         /* ensure the text isn’t masked */
}

</style>
</head>
<body>
<div class="main-wrapper">

<!-- ==================== WEIGHT ==================== -->
<div class="card-container">
  <h4>Add Weight Category</h4>
  <form id="weightForm" class="mb-4" onsubmit="return false;">
    <div class="row g-3">
      <div class="col-md-4"><label class="form-label">Name</label>
        <input id="wName" class="form-control" required>
      </div>
      <div class="col-md-4"><label class="form-label">Min kg</label>
        <input id="wMin" type="number" step="0.01" class="form-control" required>
      </div>
      <div class="col-md-4"><label class="form-label">Max kg</label>
        <input id="wMax" type="number" step="0.01" class="form-control" required>
      </div>
    </div>
    <button class="btn btn-primary mt-3" onclick="addWeight()">Save</button>
  </form>

  <div class="d-flex justify-content-between align-items-center">
    <h4 class="m-0">Weight Categories</h4>
    <button class="btn btn-outline-secondary btn-sm toggle-edit" data-table="weightTable">Edit</button>
  </div>

  <table id="weightTable" class="table table-hover view-only">
    <thead class="table-light"><tr><th>Name</th><th>Min</th><th>Max</th><th></th></tr></thead>
    <tbody id="weightBody">
    <?php foreach($weightRows as $w): ?>
      <tr data-id="<?=$w['id']?>">
        <td><input class="form-control form-control-sm" value="<?=htmlspecialchars($w['category_name'])?>"></td>
        <td><input type="number" class="form-control form-control-sm" value="<?=$w['min_kg']?>"></td>
        <td><input type="number" class="form-control form-control-sm" value="<?=$w['max_kg']?>"></td>
        <td>
          <button class="btn btn-success btn-sm edit-action" onclick="saveWeight(this)">Save</button>
          <button class="btn btn-danger  btn-sm edit-action" onclick="delWeight(this)">Delete</button>
        </td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
</div>

<!-- ==================== AGE ==================== -->
<div class="card-container">
  <h4>Add Age Category</h4>
  <form id="ageForm" class="mb-4" onsubmit="return false;">
    <div class="row g-3">
      <div class="col-md-3"><label class="form-label">Species</label>
        <select id="aSpecies" class="form-select">
          <option value="Dog">Dog</option><option value="Cat">Cat</option>
        </select>
      </div>
      <div class="col-md-3"><label class="form-label">Label</label>
        <input id="aLabel" class="form-control" required>
      </div>
      <div class="col-md-3"><label class="form-label">Min mo</label>
        <input id="aMin" type="number" class="form-control" required>
      </div>
      <div class="col-md-3"><label class="form-label">Max mo</label>
        <input id="aMax" type="number" class="form-control" required>
      </div>
    </div>
    <button class="btn btn-primary mt-3" onclick="addAge()">Save</button>
  </form>

  <div class="d-flex justify-content-between align-items-center">
    <h4 class="m-0">Age Categories</h4>
    <button class="btn btn-outline-secondary btn-sm toggle-edit" data-table="ageTable">Edit</button>
  </div>

  <table id="ageTable" class="table table-hover view-only">
    <thead class="table-light"><tr><th>Species</th><th>Label</th><th>Min</th><th>Max</th><th></th></tr></thead>
    <tbody id="ageBody">
    <?php foreach($ageRows as $a): ?>
      <tr data-id="<?=$a['id']?>">
        <td>
          <select class="form-select form-select-sm">
            <option value="Dog"<?=$a['species']=='Dog'?' selected':''?>>Dog</option>
            <option value="Cat"<?=$a['species']=='Cat'?' selected':''?>>Cat</option>
          </select>
        </td>
        <td><input class="form-control form-control-sm" value="<?=htmlspecialchars($a['label'])?>"></td>
        <td><input type="number" class="form-control form-control-sm" value="<?=$a['min_months']?>"></td>
        <td><input type="number" class="form-control form-control-sm" value="<?=$a['max_months']?>"></td>
        <td>
          <button class="btn btn-success btn-sm edit-action" onclick="saveAge(this)">Save</button>
          <button class="btn btn-danger  btn-sm edit-action" onclick="delAge(this)">Delete</button>
        </td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
</div>

<!-- ==================== PRICING ==================== -->
<div class="card-container">
  <h4>Assign Pricing</h4>

  <div class="mb-3"><label class="form-label">Service Type</label><br>
    <div class="form-check form-check-inline">
      <input class="form-check-input service-type" type="radio" name="svcType"
             value="DogGrooming" id="tDog" checked>
      <label class="form-check-label" for="tDog">Dog Grooming</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input service-type" type="radio" name="svcType"
             value="CatGrooming" id="tCat">
      <label class="form-check-label" for="tCat">Cat Grooming</label>
    </div>
  </div>

  <div class="mb-3"><label class="form-label">Select Service</label>
    <select id="serviceSelect" class="form-select" required>
      <option selected disabled value="">Choose a service</option>
      <?php foreach($serviceRows as $s): ?>
        <option value="<?=$s['id']?>"><?=htmlspecialchars($s['service_name'])?></option>
      <?php endforeach;?>
    </select>
  </div>

  <div class="row g-3 mb-4">
    <!-- weight dropdown -->
    <div id="weightWrap" class="col-md-5">
      <label class="form-label">Weight Category</label>
      <select id="weightSelect" class="form-select">
        <option selected disabled value="">Choose weight</option>
        <?php foreach($weightRows as $w): ?>
          <option value="<?=$w['id']?>"><?=htmlspecialchars($w['category_name'])?></option>
        <?php endforeach;?>
      </select>
    </div>

    <!-- age dropdown -->
    <div id="ageWrap" class="col-md-5 d-none">
      <label class="form-label">Age Category</label>
      <select id="ageSelect" class="form-select">
        <option selected disabled value="">Choose age</option>
        <?php foreach($ageRows as $a): ?>
          <option value="<?=$a['id']?>"><?=htmlspecialchars($a['label'])?></option>
        <?php endforeach;?>
      </select>
    </div>

    <div class="col-md-4"><label class="form-label">Price (₱)</label>
      <input id="priceInput" type="number" step="0.01" class="form-control" required>
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button class="btn btn-success w-100" onclick="addPricing()">Add</button>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center">
    <h4 class="m-0">Pricing List</h4>
    <button class="btn btn-outline-secondary btn-sm toggle-edit" data-table="pricingTable">Edit</button>
  </div>

  <table id="pricingTable" class="table table-hover view-only">
    <thead class="table-light"><tr><th>Service</th><th>Category</th><th>Price</th><th></th></tr></thead>
    <tbody id="pricingBody">
    <?php foreach($pricingRows as $p): ?>
      <tr data-id="<?=$p['id']?>" data-service="<?=$p['service_id']?>" data-cat="<?=$p['cat_id']?>">
        <td><?=htmlspecialchars($p['service_name'])?></td>
        <td><?=htmlspecialchars($p['category_name'])?></td>
        <td><input type="number" class="form-control form-control-sm" value="<?=$p['price']?>"></td>
        <td>
          <button class="btn btn-success btn-sm edit-action" onclick="savePricing(this)">Save</button>
          <button class="btn btn-danger  btn-sm edit-action" onclick="delPricing(this)">Delete</button>
        </td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
</div>
</div><!-- /.main-wrapper -->

<!-- ==================== TOAST + MODAL ==================== -->
<div class="toast-container p-3">
  <div id="liveToast" class="toast text-bg-primary border-0" role="alert">
    <div class="d-flex">
      <div id="toastMsg" class="toast-body"></div>
      <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<div id="confirmModal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Confirm Delete</h5>
      <button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">Delete this item? This can’t be undone.</div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button id="confirmDeleteBtn" class="btn btn-danger">Confirm</button>
    </div>
  </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ===== helpers ===== */
const qs=s=>document.querySelector(s), qsa=s=>document.querySelectorAll(s);
const toast=new bootstrap.Toast('#liveToast');
const showToast=(msg,ok=true)=>{const t=qs('#liveToast');
  t.classList.toggle('text-bg-danger',!ok); t.classList.toggle('text-bg-primary',ok);
  qs('#toastMsg').innerText=msg; toast.show();};
async function post(data){
  const r=await fetch(location.href,{method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:new URLSearchParams(data)});
  return r.json().catch(()=>({success:false}));
}

/* ===== toggle view/edit ===== */
qsa('.toggle-edit').forEach(btn=>btn.onclick=()=>{
  const tbl=qs('#'+btn.dataset.table),view=tbl.classList.contains('view-only');
  tbl.classList.toggle('view-only',!view);
  btn.textContent=view?'Done':'Edit';
  tbl.querySelectorAll('input,select').forEach(i=>{
    i.disabled=view?false:true; i.style.background=view?'#fff':'#f8f9fa';
  });
  tbl.querySelectorAll('.edit-action').forEach(x=>x.style.display=view?'inline-block':'none');
});

/* ===== weight CRUD ===== */
async function addWeight(){
  const name=qs('#wName').value.trim(),min=qs('#wMin').value,max=qs('#wMax').value;
  if(!name||!min||!max) return showToast('Fill all weight fields',false);
  const r=await post({action:'add_weight',name,min,max});
  if(!r.success) return showToast('Add failed',false);

  qs('#weightBody').insertAdjacentHTML('beforeend',`
  <tr data-id="${r.id}">
    <td><input class="form-control form-control-sm" value="${name}"></td>
    <td><input type="number" class="form-control form-control-sm" value="${min}"></td>
    <td><input type="number" class="form-control form-control-sm" value="${max}"></td>
    <td>
      <button class="btn btn-success btn-sm edit-action" onclick="saveWeight(this)">Save</button>
      <button class="btn btn-danger  btn-sm edit-action" onclick="delWeight(this)">Delete</button>
    </td>
  </tr>`);
  qs('#weightSelect').insertAdjacentHTML('beforeend',`<option value="${r.id}">${name}</option>`);
  qs('#weightForm').reset(); showToast('Weight added');
}
async function saveWeight(btn){
  const tr=btn.closest('tr'),id=tr.dataset.id,
        [name,min,max]=[...tr.querySelectorAll('input')].map(i=>i.value);
  const r=await post({action:'update_weight',id,name,min,max});
  showToast(r.success?'Saved':'Fail',r.success);
}
function delWeight(btn){ confirmDel(btn,'delete_weight'); }

/* ===== age CRUD ===== */
async function addAge(){
  const species=qs('#aSpecies').value,label=qs('#aLabel').value.trim(),
        min=qs('#aMin').value,max=qs('#aMax').value;
  if(!label||!min||!max) return showToast('Fill all age fields',false);
  const r=await post({action:'add_age',species,label,min,max});
  if(!r.success) return showToast('Add failed',false);

  qs('#ageBody').insertAdjacentHTML('beforeend',`
  <tr data-id="${r.id}">
    <td><select class="form-select form-select-sm">
          <option value="Dog"${species==='Dog'?' selected':''}>Dog</option>
          <option value="Cat"${species==='Cat'?' selected':''}>Cat</option>
        </select></td>
    <td><input class="form-control form-control-sm" value="${label}"></td>
    <td><input type="number" class="form-control form-control-sm" value="${min}"></td>
    <td><input type="number" class="form-control form-control-sm" value="${max}"></td>
    <td>
      <button class="btn btn-success btn-sm edit-action" onclick="saveAge(this)">Save</button>
      <button class="btn btn-danger  btn-sm edit-action" onclick="delAge(this)">Delete</button>
    </td>
  </tr>`);
  qs('#ageSelect').insertAdjacentHTML('beforeend',`<option value="${r.id}">${label}</option>`);
  qs('#ageForm').reset(); showToast('Age added');
}
async function saveAge(btn){
  const tr=btn.closest('tr'),id=tr.dataset.id,
        species=tr.querySelector('select').value,
        [label,min,max]=[...tr.querySelectorAll('input')].map(i=>i.value);
  const r=await post({action:'update_age',id,species,label,min,max});
  showToast(r.success?'Saved':'Fail',r.success);
}
function delAge(btn){ confirmDel(btn,'delete_age'); }

/* ===== service type switch ===== */
qsa('.service-type').forEach(r=>r.onchange=async e=>{
  const type=e.target.value;
  qs('#weightWrap').classList.toggle('d-none',type==='CatGrooming');
  qs('#ageWrap'   ).classList.toggle('d-none',type==='DogGrooming');
  qs('#weightSelect').value=''; qs('#ageSelect').value='';
  const res=await post({action:'fetch_services',type});
  if(res.success){
     const sel=qs('#serviceSelect');
     sel.innerHTML='<option selected disabled value="">Choose a service</option>';
     res.data.forEach(s=>sel.insertAdjacentHTML('beforeend',
        `<option value="${s.id}">${s.service_name}</option>`));
  }
});

/* ===== pricing CRUD ===== */
async function addPricing(){
  const type=qs('.service-type:checked').value;
  const svc =qs('#serviceSelect').value;
  const cat =(type==='DogGrooming'?qs('#weightSelect').value:qs('#ageSelect').value);
  const price=qs('#priceInput').value;
  if(!svc||!cat||!price) return showToast('Fill all fields',false);

  const r=await post({action:'add_pricing',service_id:svc,service_type:type,
                      category_id:cat,price});
  if(!r.success) return showToast('Add failed',false);

  const sName=qs(`#serviceSelect option[value="${svc}"]`).textContent;
  const cName=(type==='DogGrooming'
              ? qs(`#weightSelect option[value="${cat}"]`).textContent
              : qs(`#ageSelect option[value="${cat}"]`).textContent);

  qs('#pricingBody').insertAdjacentHTML('beforeend',`
  <tr data-id="${r.id}" data-service="${svc}" data-cat="${cat}">
    <td>${sName}</td><td>${cName}</td>
    <td><input type="number" class="form-control form-control-sm" value="${price}"></td>
    <td>
      <button class="btn btn-success btn-sm edit-action" onclick="savePricing(this)">Save</button>
      <button class="btn btn-danger  btn-sm edit-action" onclick="delPricing(this)">Delete</button>
    </td>
  </tr>`);
  qs('#priceInput').value='';
  showToast('Pricing added');
}
async function savePricing(btn){
  const tr=btn.closest('tr'),id=tr.dataset.id,price=tr.querySelector('input').value;
  const r=await post({action:'update_pricing',id,price});
  showToast(r.success?'Saved':'Fail',r.success);
}
function delPricing(btn){ confirmDel(btn,'delete_pricing'); }

/* ===== shared delete modal ===== */
let delCtx=null;
function confirmDel(btn,action){
  delCtx={row:btn.closest('tr'),action};
  new bootstrap.Modal('#confirmModal').show();
}
qs('#confirmDeleteBtn').onclick=async ()=>{
  const id=delCtx.row.dataset.id;
  const r=await post({action:delCtx.action,id});
  if(r.success){
    delCtx.row.remove(); showToast('Deleted');
  }else showToast('Delete failed',false);
  bootstrap.Modal.getInstance('#confirmModal').hide();
};
</script>
</body>
</html>
