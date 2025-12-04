<?php include('../config/db.php'); include('../includes/header.php'); include('../includes/sidebar.php'); ?>

<div class="container mt-3">
  <span class="welcome-pill">Selamat Datang <?=htmlspecialchars($_SESSION['nama_pengguna'] ?? 'Name')?> !</span>

  <div class="row dash-grid">
  <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div class="col-sm-6 col-md-4 mb-3 d-flex align-items-stretch">
      <a href="data_kriteria.php" class="text-decoration-none w-100">
        <div class="dash-card h-100">
          <div class="dash-accent accent-blue"></div>
          <div>
            <div class="title">Data Kriteria</div>
            <div class="sub">Kelola bobot kriteria</div>
          </div>
          <!--<div class="card-icon">✓</div>-->
        </div>
      </a>
    </div>

    <div class="col-sm-6 col-md-4 mb-3 d-flex align-items-stretch">
      <a href="data_subkriteria.php" class="text-decoration-none w-100">
        <div class="dash-card h-100">
          <div class="dash-accent accent-purple"></div>
          <div>
            <div class="title">Data Sub- Kriteria</div>
            <div class="sub">Atur nilai sub-kriteria</div>
          </div>
          <!--<div class="card-icon">■</div>-->
        </div>
      </a>
    </div>

    <div class="col-sm-6 col-md-4 mb-3 d-flex align-items-stretch">
      <a href="data_alternatif.php" class="text-decoration-none w-100">
        <div class="dash-card h-100">
          <div class="dash-accent accent-green"></div>
          <div>
            <div class="title">Data Alternatif</div>
            <div class="sub">Daftar equipment</div>
          </div>
          <!--<div class="card-icon">➡</div>-->
        </div>
      </a>
    </div>

    <div class="col-sm-6 col-md-4 mb-3 d-flex align-items-stretch">
      <a href="data_perhitungan.php" class="text-decoration-none w-100">
        <div class="dash-card h-100">
          <div class="dash-accent accent-orange"></div>
          <div>
            <div class="title">Data Perhitungan</div>
            <div class="sub">Hitung SAW & filter</div>
          </div>
          <!--<div class="card-icon">✓</div>-->
        </div>
      </a>
    </div>

    <div class="col-sm-6 col-md-4 mb-3 d-flex align-items-stretch">
      <a href="data_hasil.php" class="text-decoration-none w-100">
        <div class="dash-card h-100">
          <div class="dash-accent accent-red"></div>
          <div>
            <div class="title">Data Hasil Akhir</div>
            <div class="sub">Riwayat & hasil perhitungan</div>
          </div>
          <!--<div class="card-icon">★</div>-->
        </div>
      </a>
    </div>

    <div class="col-sm-6 col-md-4 mb-3 d-flex align-items-stretch">
      <a href="profil.php" class="text-decoration-none w-100">
        <div class="dash-card h-100">
          <div class="dash-accent accent-yellow"></div>
          <div>
            <div class="title">Profil</div>
            <div class="sub">Akun & pengaturan</div>
          </div>
          <!--<div class="card-icon">⚙️</div>-->
        </div>
      </a>
    </div>
  <?php else: ?>
    <div class="col-12 col-md-6 mb-3">
        <a href="data_alternatif.php" class="text-decoration-none w-100">
          <div class="dash-card h-100">
            <div class="dash-accent accent-green"></div>
          <div>
            <div class="title">Data Alternatif</div>
            <div class="sub">Daftar equipment</div>
          </div>
          <!-- <div class="card-icon">➡</div>-->
        </div>
      </a>
    </div>
    <div class="col-12 col-md-6 mb-3">
        <a href="data_hasil.php" class="text-decoration-none w-100">
          <div class="dash-card h-100">
            <div class="dash-accent accent-red"></div>
          <div>
            <div class="title">Data Hasil Akhir</div>
            <div class="sub">Riwayat & hasil perhitungan</div>
          </div>
          <!-- <div class="card-icon">★</div> -->
        </div>
      </a>
    </div>
    <div class="col-12 col-md-6 mb-3">
        <a href="profil.php" class="text-decoration-none w-100">
          <div class="dash-card h-100">
            <div class="dash-accent accent-yellow"></div>
          <div>
            <div class="title">Profil</div>
            <div class="sub">Akun & pengaturan</div>
          </div>
          <!-- <div class="card-icon">⚙️</div> -->
        </div>
      </a>
    </div>
  <?php endif; ?>
  </div>

  </div>

<?php include('../includes/footer.php'); ?>
