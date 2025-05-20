<?php
/* ============================================================
   Bark & Wiggle – System-wide Audit Log (Admin)
   ============================================================ */
include_once '../helpers/admin-auth.php';
include_once 'admin-navigation.php';
include_once '../db.php';

/* ----------  FILTER HANDLING  ---------- */
$actor   = $_GET['actor']   ?? 'all';          // admin | user | all
$keyword = trim($_GET['kw'] ?? '');            // free-text search

$sql = "
  SELECT
    al.id,
    COALESCE(a.full_name, u.full_name, 'Unknown')            AS actor_name,
    CASE WHEN a.id IS NOT NULL THEN 'Admin' ELSE 'User' END  AS actor_type,
    al.action_type,
    al.action_description,
    al.table_affected,
    al.record_id,
    al.created_at
  FROM audit_logs al
  LEFT JOIN admin a ON a.id = al.user_id
  LEFT JOIN users u ON u.id = al.user_id
  WHERE 1
";

$types  = '';
$params = [];

/* Filter by actor type */
if ($actor !== 'all') {
    $sql   .= " AND (CASE WHEN a.id IS NOT NULL THEN 'Admin' ELSE 'User' END) = ? ";
    $types .= 's';
    $params[] = $actor;
}

/* Free-text keyword search (actor name, action, table) */
if ($keyword !== '') {
    $sql   .= " AND (COALESCE(a.full_name,u.full_name) LIKE ? 
                 OR al.action_type LIKE ? 
                 OR al.table_affected LIKE ?) ";
    $types  .= 'sss';
    $kw = "%{$keyword}%";
    $params = array_merge($params, [$kw, $kw, $kw]);
}

$sql .= " ORDER BY al.created_at DESC";

$stmt = $conn->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$logs = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>System Audit Log • Bark & Wiggle</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap & DataTables -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <h1 class="mb-4 fw-bold"><i class="fa-solid fa-clipboard-list me-2"></i>System Audit Log</h1>

  <!-- ===== Search / Filter Bar ===== -->
  <form class="row g-2 mb-3" method="GET">
      <div class="col-md-auto">
          <select name="actor" class="form-select">
              <option value="all"  <?= $actor==='all'   ? 'selected' : '' ?>>All Actors</option>
              <option value="admin"<?= $actor==='admin' ? 'selected' : '' ?>>Admins</option>
              <option value="user" <?= $actor==='user'  ? 'selected' : '' ?>>Users</option>
          </select>
      </div>
      <div class="col-md">
          <input type="text" name="kw" value="<?= htmlspecialchars($keyword) ?>"
                 class="form-control" placeholder="Search by name, action, table…">
      </div>
      <div class="col-md-auto d-grid">
          <button class="btn btn-primary"><i class="fa-solid fa-search me-1"></i>Search</button>
      </div>
      <div class="col-md-auto d-grid">
          <a href="admin-audit-log.php" class="btn btn-outline-secondary">Reset</a>
      </div>
  </form>

  <!-- ===== Audit Log Table ===== -->
  <div class="table-responsive shadow-sm border rounded">
    <table id="auditTable" class="table table-striped table-hover align-middle mb-0">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Actor</th>
                <th>Role</th>
                <th>Action</th>
                <th>Description</th>
                <th>Table</th>
                <th>Record&nbsp;ID</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $logs->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['actor_name']) ?></td>
                <td>
                    <span class="badge <?= $row['actor_type']==='Admin'?'bg-danger':'bg-info' ?>">
                        <?= $row['actor_type'] ?>
                    </span>
                </td>
                <td class="text-uppercase fw-semibold"><?= $row['action_type'] ?></td>
                <td><?= htmlspecialchars($row['action_description'] ?? '-') ?></td>
                <td><code><?= $row['table_affected'] ?? '-' ?></code></td>
                <td><?= $row['record_id'] ?? '-' ?></td>
                <td><?= date('Y-m-d H:i:s', strtotime($row['created_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(function () {
      $('#auditTable').DataTable({
          order: [[7, 'desc']],          // default sort by timestamp desc
          pageLength: 25,
          lengthMenu: [10,25,50,100]
      });
  });
</script>
</body>
</html>
