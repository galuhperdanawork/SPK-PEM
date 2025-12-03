<?php
session_start();
require 'config/db.php';

// Jika sudah login, redirect
if (!empty($_SESSION['id_user'])) {
    header('Location: pages/dashboard.php');
    exit;
}

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');

    if ($u === '' || $p === '') {
        $err = 'Masukkan username dan password.';
    } else {
        // Query langsung username + password (plain text)
        $stmt = $koneksi->prepare(
            "SELECT id_user, username, nama_pengguna, role 
             FROM users WHERE username = ? AND password = ?"
        );
        $stmt->bind_param('ss', $u, $p);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();

            session_regenerate_id(true);

            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama_pengguna'] = $row['nama_pengguna'];
            $_SESSION['role'] = $row['role'];

            header('Location: pages/dashboard.php');
            exit;
        } else {
            $err = 'Username atau password salah.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login - SPK SAW | PEM System</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/SPK-PEM/assets/logo.png">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
                        url('/SPK-PEM/assets/img-bg.jpg') center/cover no-repeat;
            color: white;
        }
        .login-card {
            border-radius: 12px;
            background: white;
            color: #000;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
    </style>
</head>

<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="row w-100 justify-content-center align-items-center" style="max-width:1100px;">

        <!-- Text Left -->
        <div class="col-12 col-md-6 text-center text-md-start mb-4 mb-md-0">
            <h1 class="fw-bold" style="font-size:40px;">
                Sistem Penunjang Keputusan<br>Maintenance Equipment
            </h1>
            <p class="mt-3" style="max-width:450px;">
                Menggunakan metode SAW untuk menentukan prioritas maintenance secara objektif, cepat dan akurat.
            </p>
        </div>

        <!-- Login Form -->
        <div class="col-12 col-md-6 d-flex justify-content-center">
            <div class="card login-card p-4 w-100" style="max-width:380px;">
                <h4 class="text-center mb-3">Login</h4>

                <?php if ($err): ?>
                    <div class="alert alert-danger py-2 text-center">
                        <?= htmlspecialchars($err) ?>
                    </div>
                <?php endif; ?>

                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <input name="username" class="form-control" placeholder="Username" value="<?= htmlspecialchars($u ?? '') ?>" autofocus required />
                    </div>

                    <div class="mb-3">
                        <input name="password" type="password" class="form-control" placeholder="Password" required />
                    </div>

                    <button class="btn btn-primary w-100" id="login-btn">
                        Login
                    </button>
                </form>

                <hr>

                <a href="register.php" class="btn btn-outline-secondary w-100">
                    Register
                </a>
            </div>
        </div>

    </div>
</div>

<script>
const btn = document.getElementById('login-btn');
document.querySelector('form').addEventListener('submit', () => {
    btn.disabled = true;
    btn.innerHTML = "Loading...";
});
</script>
</body>
</html>
