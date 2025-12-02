<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sistem Penunjang Keputusan - Periodical Equipment Maintenance</title>
  <link href="/SPK-PEM/assets/logo.png" rel="icon">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="/SPK-PEM/style.css" rel="stylesheet">
  <style>
  body { padding-top: 56px; font-family: 'Roboto', sans-serif; background: #f6f7fb; }
    .sidebar { min-width:220px; max-width:220px; }
    .material-icons { font-size: 20px; vertical-align: middle; margin-right:8px; }
    .navbar { background: linear-gradient(90deg,#538CDF,#6FA8E6) !important; }
    .navbar-brand { font-weight:700; color: #fff !important; }
    .navbar .ms-auto { color: #fff; }
    .navbar .btn-light { background: rgba(255,255,255,0.9); }
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
        <a href="/SPK-PEM/process/logout.php" class="btn btn-danger">Logout</a>
      </div>

    </div>
  </div>
</div>


<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top no-print">
  <div class="container-fluid">
    <a class="navbar-brand ms-3 d-flex align-items-center" href="/SPK-PEM/pages/dashboard.php">
      <img src="/SPK-PEM/assets/logo.png" alt="Logo" style="width:32px;height:32px;object-fit:contain;margin-right:8px;">
      <span>Sistem Penunjang Keputusan - Periodical Equipment Maintenance</span>
    </a>
    <button class="sidebar-toggle d-md-none" aria-label="Toggle menu" title="Menu">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <div class="ms-auto text-white">
      <?php if(isset($_SESSION['username'])):
          $displayName = $_SESSION['nama_pengguna'] ?? $_SESSION['username'] ?? '';
      ?>
        <span class="me-3"><strong><?=htmlspecialchars($displayName)?></strong></span>
        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#logoutModal">
          Logout
        </button>

      <?php else: ?>
        <a href="/SPK-PEM/login.php" class="btn btn-sm btn-light">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container-fluid">
  <div class="row">
