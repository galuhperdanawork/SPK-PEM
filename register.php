<?php
session_start();
include('config/db.php');

$err = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = "mc-" . substr(md5(rand()), 0, 8);
    $username = trim($_POST['username']);
    $nama     = trim($_POST['nama_pengguna']);
    $password = $_POST['password']; 
    $role     = "user";

    $cek = $koneksi->prepare("SELECT username FROM users WHERE username=?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $res = $cek->get_result();

    if ($res->num_rows > 0) {
        $err = "Username sudah digunakan!";
    } else {
        $stmt = $koneksi->prepare("INSERT INTO users (id_user, username, nama_pengguna, password, role) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $id, $username, $nama, $password, $role);
        $stmt->execute();

        $success = "Registrasi berhasil! Silakan login.";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Global CSS -->
  <link href="style.css" rel="stylesheet">


  <style>
    body {
      background: #538CDF;
    }
    .login-card {
      border-radius: 12px;
      border: 1px solid #e1e1e1;
      background: #fff;
      box-shadow: 0 4px 18px rgba(0,0,0,0.05);
    }
        /* Deskripsi aplikasi */
    .desc-app {
      color: #ffffff;
      font-size: 18px;
      max-width: 420px;
      line-height: 1.6;
      margin-left: auto;
      margin-right: auto;
      text-align: center;
    }

    @media (min-width: 768px) {
      .desc-app {
        margin-left: 0;
        margin-right: 0;
        text-align: left;
      }
    }
  </style>
</head>

<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="row w-100 justify-content-center align-items-center" style="max-width:1100px;">

        <!-- Kolom Teks -->
        <div class="col-12 col-md-6 text-center text-md-start mb-4 mb-md-0">
            <h1 style="color: #ffffff;font-size:48px;font-weight:700;">
                Sistem Penunjang Keputusan
            </h1>

            <p class="desc-app">
                Aplikasi penunjang keputusan dengan metode Simple Additive Weighing.<br>
                Kelola kriteria, alternatif, dan hasil rekomendasi equipment.
            </p>
        </div>
        
    <!-- FORM REGISTER (KANAN) -->
    <div class="col-12 col-md-6 d-flex justify-content-center">
      <div class="card login-card p-4 w-100" style="max-width:380px;">
        <div class="card-body">

          <h4 class="mb-3 text-center">Register Account</h4>

          <?php if($err): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($err) ?></div>
          <?php endif; ?>

          <?php if($success): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-3">
              <input required name="nama_pengguna" class="form-control" placeholder="Nama Anda">
            </div>

            <div class="mb-3">
              <input required name="username" class="form-control" placeholder="Username">
            </div>

            <div class="mb-3">
              <input required name="password" type="password" class="form-control" placeholder="Password">
            </div>

            <div class="d-grid">
              <button class="btn btn-primary">Register</button>
            </div>
          </form>

          <div class="mt-3">
            <a href="login.php" class="btn btn-outline-secondary w-100">Sudah punya akun? Login</a>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
