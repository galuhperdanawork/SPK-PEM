<?php include('../config/db.php'); include('../includes/header.php'); include('../includes/sidebar.php'); ?>

<style>
  .welcome-pill{display:block;max-width:760px;margin:6px auto 24px auto;background:#e9f2ff;padding:12px 18px;border-radius:12px;color:#08386b;font-weight:600;text-align:center;box-shadow:0 6px 14px rgba(83,140,223,0.12)}
  .dash-grid{margin-top:18px}
  .dash-card{position:relative;padding:18px 18px 18px 26px;border-radius:12px;background:#fff;box-shadow:0 10px 24px rgba(17,24,39,0.06);min-height:84px;display:flex;align-items:center;gap:12px}
  .dash-card .title{font-weight:700;color:#222}
  .dash-card .sub{font-size:12px;color:#666}
  .dash-accent{position:absolute;left:-10px;top:50%;transform:translateY(-50%);width:10px;height:64px;border-radius:8px}
  .accent-blue{background:#34a0ff}
  .accent-green{background:#00c875}
  .accent-orange{background:#ff8d28}
  .accent-yellow{background:#ffcc00}
  .accent-purple{background:#6155f5}
  .accent-red{background:#ff383c}
  .card-icon{margin-left:auto;background:#f5f7fb;border-radius:8px;padding:8px}
  @media(max-width:767px){ .welcome-pill{margin:10px 12px} .dash-card{min-height:72px} }
</style>

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
