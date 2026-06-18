<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sistem Pendukung Keputusan - Periodical Equipment Maintenance</title>
  <link href="/SPK-PEM/assets/logo.png" rel="icon">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
  <link href="/SPK-PEM/style.css" rel="stylesheet">
  <style>
  body { padding-top: 56px; font-family: 'Roboto', sans-serif; background: #f6f7fb; }
    .sidebar { min-width:220px; max-width:220px; }
    .material-symbols-outlined { font-size: 20px; vertical-align: middle; margin-right:8px; }
    .navbar { background: linear-gradient(90deg,#538CDF,#6FA8E6) !important; }
    .navbar-brand { font-weight:700; color: #fff !important; }
    .navbar .ms-auto { color: #fff; }
    .navbar .btn-light { background: rgba(255,255,255,0.9); }
    /* Global simple/modern UI utilities used across pages */
    .simple-card, .profile-card { border-radius: 12px; box-shadow: 0 8px 24px rgba(16,24,40,0.06); }
    .page-title { font-weight:700; font-size:1.15rem; }
    .muted-small, .muted { color:#6c757d; }
    .avatar { width:72px; height:72px; border-radius:50%; background:#0d6efd; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:20px; }
    .role-badge { background: rgba(13,110,253,0.12); color:#0d6efd; padding:6px 10px; border-radius:999px; font-weight:600; }
    .table-small td, .table-small th { padding:0.5rem 0.75rem; }
    .actions-cell .material-symbols-outlined { vertical-align:middle; font-size:18px; }
    .welcome-pill { display:inline-block; padding:6px 12px; background:#fff; border-radius:999px; box-shadow:0 2px 8px rgba(0,0,0,0.04); margin-bottom:12px; }
    /* Material flat button style for logout/controls */
    .btn-flat { background: transparent; border: none; color: inherit; padding: .25rem .5rem; display: inline-flex; align-items: center; gap: .4rem; font-weight: 500; }
    .btn-flat .material-symbols-outlined { font-size:20px; vertical-align:middle; margin-right:4px; }
    /* User control styles */
    .user-controls { display:flex; align-items:center; gap:8px; }
    .avatar-sm { width:36px; height:36px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; }
    .user-name { font-weight:700; }
    .user-role { font-size:12px; opacity:0.85; }
    @media (max-width:767px){ .page-header{flex-direction:column;align-items:flex-start;} }
  </style>
</head>
<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        Anda yakin ingin logout dari aplikasi?
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" onclick="location.href='/SPK-PEM/process/logout.php'">Logout</button>
      </div>

    </div>
  </div>
</div>


<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top no-print">
  <div class="container-fluid">
    <a class="navbar-brand ms-3 d-flex align-items-center" href="/SPK-PEM/pages/dashboard.php">
      <img src="/SPK-PEM/assets/logo.png" alt="Logo" style="width:32px;height:32px;object-fit:contain;margin-right:8px;">
      <span>Sistem Pendukung Keputusan - Periodical Equipment Maintenance</span>
    </a>
    <button class="sidebar-toggle d-md-none" aria-label="Toggle menu" title="Menu">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <div class="ms-auto text-white">
      <?php if(isset($_SESSION['username'])):
          $displayName = $_SESSION['nama_pengguna'] ?? $_SESSION['username'] ?? '';
          $roleName = $_SESSION['role'] ?? '';
          $initials = '';
          if ($displayName) {
            $parts = preg_split('/\s+/', trim($displayName));
            foreach ($parts as $p) { if ($p) $initials .= mb_strtoupper(mb_substr($p,0,1)); }
            $initials = mb_substr($initials, 0, 2);
          }
      ?>
        <div class="user-controls">
          <div class="avatar-sm" style="background:#0d6efd;color:#fff;"><?= htmlspecialchars($initials ?: 'U') ?></div>
          <div class="d-none d-sm-block">
            <div class="user-name text-white"><?= htmlspecialchars($displayName) ?></div>
            <div class="user-role text-white muted-small"><?= htmlspecialchars(ucfirst($roleName)) ?></div>
          </div>
          <button class="btn-flat btn-sm text-white ms-2" data-bs-toggle="modal" data-bs-target="#logoutModal" title="Logout">
            <span class="material-symbols-outlined">logout</span>
            Logout
          </button>
        </div>

      <?php else: ?>
        <a href="/SPK-PEM/login.php" class="btn btn-sm btn-light">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container-fluid">
  <div class="row">
