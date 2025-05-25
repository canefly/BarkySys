<?php
/* ============================================================
   SESSION + DB + AUDIT
   ============================================================ */
session_start();
include_once '../db.php';
include_once 'audit-log.php';   // optional

$errorMessage = '';

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin  = $result->fetch_assoc();
    $stmt->close();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id']    = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_name']  = $admin['full_name'];

        log_audit($admin['id'], 'admin', 'login', 'Admin logged in', 'admin');
        header('Location: admin-hp.php');   // straight to dashboard
        exit();
    } else {
        $errorMessage = 'Invalid credentials. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bark &amp; Wiggle – Admin Portal</title>

  <!-- Bootstrap & fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --bw-purple:rgb(126, 67, 156);
      --bw-purple-dark:rgb(140, 64, 175);
      --bw-gold:   #D6BE3E;
      --bw-gold-dark: #b4a135;
      --bw-bg:     #f4f0f8;
    }
    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: var(--bw-bg);
      display: flex;
      min-height: 100vh;
    }

    /* ===== Split layout ===== */
    .sidebar {
      flex: 0 0 40%;
      background: linear-gradient(145deg, var(--bw-purple-dark), var(--bw-purple));
      color: #fff;
      padding: 60px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }
      .sidebar::after {           /* subtle paw background */
        content: '';
        position: absolute;
        inset: 0;
        background: url('../img/Paw_Print.svg') center/45% no-repeat;
        opacity: 0.05;
        transform: rotate(-20deg);
      }
      .brand {
        z-index: 1;
        text-align: center;
      }
        .brand img { width: 200px; }
        .brand h1 {
          margin-top: 30px;
          font-size: 2.2rem;
          letter-spacing: 1px;
        }
        .admin-badge {
          display: inline-block;
          margin-top: 6px;
          padding: 4px 10px;
          background: var(--bw-gold);
          color: #000;
          font-size: 0.85rem;
          font-weight: 600;
          border-radius: 999px;
        }

    .login-pane {
      flex: 1 1 60%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 30px;
    }

    .login-card {
      width: 100%;
      max-width: 420px;
      background: #fff;
      padding: 40px 35px 45px;
      border-radius: 20px;
      box-shadow: 0 12px 25px rgba(0,0,0,.12);
    }
      .login-card h2 {
        text-align: center;
        font-weight: 600;
        margin-bottom: 30px;
        color: var(--bw-purple-dark);
      }

    /* form controls */
    .form-label { color: var(--bw-purple-dark); font-weight: 500; }
    .btn-primary {
      background: var(--bw-gold);
      border-color: var(--bw-gold);
      color: #000;
      font-weight: 600;
      transition: background .2s, border .2s;
    }
    .btn-primary:hover {
      background: var(--bw-gold-dark);
      border-color: var(--bw-gold-dark);
    }

    .footer-note {
      position: absolute;
      bottom: 15px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.8rem;
      color: #fff;
      opacity: .6;
    }

    /* responsive tweak */
    @media (max-width: 992px) {
      .sidebar { display:none; }
      body      { background: var(--bw-purple); }
      .login-pane { flex-basis: 100%; }
    }
  </style>
</head>
<body>

  <!-- purple brand side -->
  <aside class="sidebar">
      <div class="brand">
          <img src="../img/logo_cropped.png" alt="Bark & Wiggle logo">
          <h1>Bark &amp; Wiggle</h1>
          <span class="admin-badge">ADMIN PORTAL</span>
      </div>
      <p class="footer-note">&copy; 2025 Bark &amp; Wiggle</p>
  </aside>

  <!-- login side -->
  <main class="login-pane">
      <div class="login-card">
          <h2>Sign in to continue</h2>

          <form method="POST">
              <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email" required autofocus>
              </div>
              <div class="mb-4">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
          </form>
      </div>
  </main>

  <!-- error modal -->
  <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                  <h5 class="modal-title">Login Failed</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body"><?php echo htmlspecialchars($errorMessage); ?></div>
          </div>
      </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <?php if ($errorMessage): ?>
  <script>
      document.addEventListener('DOMContentLoaded', () => {
          new bootstrap.Modal(document.getElementById('errorModal')).show();
      });
  </script>
  <?php endif; ?>
</body>
</html>
