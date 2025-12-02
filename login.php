<?php
session_start();
include 'config/db.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    if ($u === '' || $p === '') {
        $err = 'Masukkan username dan password.';
    } else {
        $stmt = $koneksi->prepare("SELECT id_user, username, nama_pengguna, password, role FROM users WHERE username = ?");
        $stmt->bind_param('s', $u);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if ($p === $row['password']) { // plain text
                $_SESSION['id_user'] = $row['id_user'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama_pengguna'] = $row['nama_pengguna'];
                $_SESSION['role'] = $row['role'];
                header('Location: pages/dashboard.php'); 
                exit;
            } else {
                $err = 'Username atau password salah.';
            }
        } else {
            $err = 'Username tidak ditemukan.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>Login - SPK SAW</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Global CSS -->
    <link href="style.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* Background image with white transparent mask */
            background: linear-gradient(rgba(255,255,255,0.6), rgba(255,255,255,0.6)), url('/SPK-PEM/assets/img-bg.jpg') center/cover no-repeat;
            background-color: var(--accent-1);
            /* Default text color (blue) */
            color: #0d6efd;
        }

        .login-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(17,24,39,0.06);
        }

        /* Deskripsi aplikasi */
        .desc-app {
            color: #ffffff;
            font-size: 16px;
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
            <h1 style="color: #000000;font-size:48px;font-weight:700;">
                Sistem Penunjang Keputusan - Periodical Equipment Maintenance (PEM)
            </h1>

            <p class="desc-app" style="color: #000000;">
                Aplikasi penunjang keputusan periodical equipment maintenance dengan metode Simple Additive Weighting.<br>
                SPK-PEM dapat mengelola kriteria, alternatif, dan mencari hasil perhitungan rekomendasi equipment maintenance.
            </p>
        </div>

        <!-- Kolom Form Login -->
        <div class="col-12 col-md-6 d-flex justify-content-center">
            <div class="card login-card p-4 w-100" style="max-width:380px;">
                <div class="card-body">
                    <h4 class="mb-3 text-center">Login</h4>

                    <?php if($err): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($err) ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <input name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input name="password" type="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary">Login</button>
                        </div>
                    </form>

                    <div class="mt-3">
                        <a href="register.php" class="btn btn-outline-secondary w-100">Register</a>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
