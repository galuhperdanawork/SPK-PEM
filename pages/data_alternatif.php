<?php
session_start();
include '../config/db.php';

// === PROSES TAMBAH DATA ===
if (isset($_POST['simpan'])) {
  // Only admin 
  if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak: Anda tidak memiliki hak untuk menambah data.'); window.location='data_alternatif.php';</script>";
    exit;
  }
    $equipment_name = $_POST['equipment_name'];
    $inspection_name = $_POST['inspection_name'];
    $id_last_inspection = $_POST['id_last_inspection'];
    $id_grade = $_POST['id_grade'];
    $id_plant = $_POST['id_plant'];
    $id_classification = $_POST['id_classification'];
    $id_inspection_period = $_POST['id_inspection_period'];

        $query = "INSERT INTO equipment 
          (equipment_name, inspection_name, id_last_inspection, id_grade, id_plant, id_classification, id_inspection_period)
          VALUES 
          ('$equipment_name', '$inspection_name', '$id_last_inspection', '$id_grade', '$id_plant', '$id_classification', '$id_inspection_period')";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        // Ambil id equipment terakhir
        $id_equipment = $koneksi->insert_id;
        // Ambil semua kriteria
        $kq = $koneksi->query("SELECT * FROM criteria_weight ORDER BY id_criteria");
        while($kr = $kq->fetch_assoc()) {
            $id_criteria = $kr['id_criteria'];
            $nilai = 0;
            $cname = strtolower(trim($kr['criteria_name']));
            if ($cname == 'grade') {
              $q = $koneksi->query("SELECT grade_point FROM grade WHERE id_grade='$id_grade'");
              $d = $q->fetch_assoc();
              $nilai = $d ? $d['grade_point'] : 0;
            } elseif ($cname == 'classification') {
              $q = $koneksi->query("SELECT classification_point FROM classification WHERE id_classification='$id_classification'");
              $d = $q->fetch_assoc();
              $nilai = $d ? $d['classification_point'] : 0;
            } elseif ($cname == 'inspection period') {
              $q = $koneksi->query("SELECT period_point FROM inspection_period WHERE id_inspection_period='$id_inspection_period'");
              $d = $q->fetch_assoc();
              $nilai = $d ? $d['period_point'] : 0;
            }
            // Insert ke penilaian
            $insert = $koneksi->query("INSERT INTO penilaian (id_equipment, id_criteria, nilai) VALUES ('$id_equipment', '$id_criteria', '$nilai')");
            if(!$insert) {
                echo "<script>alert('Gagal insert penilaian: " . $koneksi->error . "');</script>";
            }
        }
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='data_alternatif.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data: " . mysqli_error($koneksi) . "');</script>";
    }
}

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'any';
$whereSql = '';
if($search){
  $s = $koneksi->real_escape_string($search);
  $sl = strtolower($s);
  switch($category){
    case 'equipment':
      $whereSql = "WHERE LOWER(e.equipment_name) LIKE '%$sl%'";
      break;
    case 'inspection':
      $whereSql = "WHERE LOWER(e.inspection_name) LIKE '%$sl%'";
      break;
    case 'plant':
      $whereSql = "WHERE LOWER(p.plant_name) LIKE '%$sl%'";
      break;
    case 'classification':
      $whereSql = "WHERE LOWER(c.classification_name) LIKE '%$sl%'";
      break;
    case 'period':
      $whereSql = "WHERE LOWER(ip.period_name) LIKE '%$sl%'";
      break;
    case 'last_inspection':
      $whereSql = "WHERE LOWER(li.year) LIKE '%$sl%'";
      break;
    default:
      $whereSql = "WHERE (LOWER(e.equipment_name) LIKE '%$sl%' OR LOWER(e.inspection_name) LIKE '%$sl%' OR LOWER(p.plant_name) LIKE '%$sl%' OR LOWER(c.classification_name) LIKE '%$sl%' OR LOWER(ip.period_name) LIKE '%$sl%' OR LOWER(li.year) LIKE '%$sl%')";
  }
}

$perPage = 10; // baris per halaman
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

$fromJoins = "FROM equipment e
          LEFT JOIN grade g ON e.id_grade = g.id_grade
          LEFT JOIN plant p ON e.id_plant = p.id_plant
          LEFT JOIN classification c ON e.id_classification = c.id_classification
          LEFT JOIN inspection_period ip ON e.id_inspection_period = ip.id_inspection_period
          LEFT JOIN last_inspection li ON e.id_last_inspection = li.id_last_inspection";

// Hitung total rows
$countSql = "SELECT COUNT(*) AS total $fromJoins $whereSql";
$countRes = mysqli_query($koneksi, $countSql);
$total = 0;
if ($countRes) {
  $r = mysqli_fetch_assoc($countRes);
  $total = intval($r['total']);
}
$totalPages = $total > 0 ? ceil($total / $perPage) : 1;

// base url untuk link pagination (pertahankan parameter search jika ada)
$baseUrl = 'data_alternatif.php?';
if ($search) $baseUrl .= 'search=' . urlencode($search) . '&';
if ($category) $baseUrl .= 'category=' . urlencode($category) . '&';

$query = "SELECT e.id_equipment, e.equipment_name, e.inspection_name,
                 g.grade_name, p.plant_name,
                 c.classification_name, ip.period_name,
                 li.year AS last_year
          FROM equipment e
          LEFT JOIN grade g ON e.id_grade = g.id_grade
          LEFT JOIN plant p ON e.id_plant = p.id_plant
          LEFT JOIN classification c ON e.id_classification = c.id_classification
          LEFT JOIN inspection_period ip ON e.id_inspection_period = ip.id_inspection_period
          LEFT JOIN last_inspection li ON e.id_last_inspection = li.id_last_inspection
          $whereSql
          LIMIT $offset, $perPage";
$result = mysqli_query($koneksi, $query);

// === DROPDOWN DATA ===
$grade = mysqli_query($koneksi, "SELECT * FROM grade");
$plant = mysqli_query($koneksi, "SELECT * FROM plant");
$classification = mysqli_query($koneksi, "SELECT * FROM classification");
$inspection_period = mysqli_query($koneksi, "SELECT * FROM inspection_period");
$last_inspection = mysqli_query($koneksi, "SELECT * FROM last_inspection ORDER BY year DESC");

// edit mode data
$editRow = null;
if (isset($_GET['edit'])) {
  $eid = intval($_GET['edit']);
  $er = mysqli_query($koneksi, "SELECT * FROM equipment WHERE id_equipment = $eid");
  if ($er) $editRow = mysqli_fetch_assoc($er);
}
?>

<?php include('../includes/header.php'); include('../includes/sidebar.php'); ?>

<div class="container mt-4">
  <span class="welcome-pill">Daftar Equipment — Kelola data dan inspection item</span>


  <!-- Form Tambah -->
  <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
  <div class="card mb-4">
    <div class="card-body">
  <form method="POST" class="mt-3 mb-0" action="<?= isset($editRow) ? '../process/update_alternatif.php' : '' ?>">
      <div class="row">
        <div class="col-md-4">
          <label>Nama Equipment</label>
          <input type="text" name="equipment_name" class="form-control" required value="<?= htmlspecialchars($editRow['equipment_name'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label>Inspection Item</label>
          <input type="text" name="inspection_name" class="form-control" required value="<?= htmlspecialchars($editRow['inspection_name'] ?? '') ?>">
        </div>

        

        <div class="col-md-4">
          <label>Grade</label>
          <select name="id_grade" class="form-select" required>
            <option value="">-- Pilih Grade --</option>
            <?php while($row=mysqli_fetch_assoc($grade)) { ?>
              <option value="<?= $row['id_grade'] ?>" <?= (isset($editRow) && $editRow['id_grade']==$row['id_grade'])? 'selected':'' ?>><?= $row['grade_name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-4">
          <label>Plant</label>
          <select name="id_plant" class="form-select" required>
            <option value="">-- Pilih Plant --</option>
            <?php while($row=mysqli_fetch_assoc($plant)) { ?>
              <option value="<?= $row['id_plant'] ?>" <?= (isset($editRow) && $editRow['id_plant']==$row['id_plant'])? 'selected':'' ?>><?= $row['plant_name'] ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-4">
          <label>Classification</label>
          <select name="id_classification" class="form-select" required>
            <option value="">-- Pilih Classification --</option>
            <?php while($row=mysqli_fetch_assoc($classification)) { ?>
              <option value="<?= $row['id_classification'] ?>" <?= (isset($editRow) && $editRow['id_classification']==$row['id_classification'])? 'selected':'' ?>><?= $row['classification_name'] ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-4">
          <label>Inspection Period</label>
          <select name="id_inspection_period" class="form-select" required>
            <option value="">-- Pilih Period --</option>
            <?php while($row=mysqli_fetch_assoc($inspection_period)) { ?>
              <option value="<?= $row['id_inspection_period'] ?>" <?= (isset($editRow) && $editRow['id_inspection_period']==$row['id_inspection_period'])? 'selected':'' ?>><?= $row['period_name'] ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-4 mt-3">
          <label>Last Inspection (Year)</label>
          <select name="id_last_inspection" class="form-select">
            <option value="">-- Pilih Tahun --</option>
            <?php while($rli = mysqli_fetch_assoc($last_inspection)) { ?>
              <option value="<?= $rli['id_last_inspection'] ?>" <?= (isset($editRow) && $editRow['id_last_inspection']==$rli['id_last_inspection'])? 'selected':'' ?>><?= $rli['year'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

          <div class="mt-4">
            <?php if (isset($editRow)): ?>
              <input type="hidden" name="id_equipment" value="<?= htmlspecialchars($editRow['id_equipment']) ?>">
              <button type="submit" class="btn btn-primary" formaction="../process/update_alternatif.php">Simpan</button>
              <a href="data_alternatif.php" class="btn btn-danger ms-2">Batal</a>
            <?php else: ?>
              <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
    <?php else: ?>
      <div class="alert alert-info">Anda login sebagai <strong><?=htmlspecialchars($_SESSION['role'] ?? 'Tamu')?></strong>. Anda hanya dapat melihat data.</div>
    <?php endif; ?>

    <!-- Tabel Data -->
    <form method="get" class="mb-3">
      <div class="input-group">
        <select name="category" class="form-select" style="max-width:200px">
          <option value="any" <?= ($category=='any')? 'selected':'' ?>>Any Search</option>
              <option value="equipment" <?= ($category=='equipment')? 'selected':'' ?>>Nama Equipment</option>
              <option value="inspection" <?= ($category=='inspection')? 'selected':'' ?>>Inspection Item</option>
              <option value="plant" <?= ($category=='plant')? 'selected':'' ?>>Plant</option>
              <option value="classification" <?= ($category=='classification')? 'selected':'' ?>>Classification</option>
              <option value="period" <?= ($category=='period')? 'selected':'' ?>>Inspection Period</option>
              <option value="last_inspection" <?= ($category=='last_inspection')? 'selected':'' ?>>Last Inspection (Year)</option>
            </select>
        <input type="search" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search ?? '') ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </div>
    </form>
    <div class="card">
      <div class="card-body p-0">
    <table class="table table-bordered mb-0">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama Equipment</th>
          <th>Inspection Item</th>
          <th>Grade</th>
          <th>Plant</th>
          <th>Classification</th>
          <th>Last Inspection</th>
          <th>Inspection Period</th>
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?><th style="width:120px">Aksi</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php $no = $offset + 1; while($row=mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $row['equipment_name'] ?></td>
          <td><?= $row['inspection_name'] ?></td>
          <td><?= $row['grade_name'] ?></td>
          <td><?= $row['plant_name'] ?></td>
          <td><?= $row['classification_name'] ?></td>
          <td><?= htmlspecialchars($row['last_year'] ?? '') ?></td>
          <td><?= $row['period_name'] ?></td>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <td class="actions-cell">
              <a href="data_alternatif.php?edit=<?= $row['id_equipment'] ?>" class="btn btn-icon btn-sm btn-warning" title="Edit">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" stroke="currentColor" stroke-width="0" fill="currentColor"/></svg>
              </a>
              <a href="../process/delete_alternatif.php?id=<?= $row['id_equipment'] ?>" class="btn btn-icon btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus equipment ini?')">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 6v14c0 1.1.9 2 2 2h4c1.1 0 2-.9 2-2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
          </td>
        <?php endif; ?>
        </tr>
        <?php } ?>
      </tbody>
    </table>
      </div>
      </div>

      <?php if (!empty($totalPages) && $totalPages > 1): ?>
        <div class="mt-3">
          <nav aria-label="Pagination">
            <ul class="pagination">
              <!-- Previous -->
              <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= ($page > 1) ? $baseUrl . 'page=' . ($page - 1) : '#' ?>">&laquo; Prev</a>
              </li>

              <!-- Pages -->
              <?php
                // show a window of pages around current to avoid too many links
                $start = max(1, $page - 3);
                $end = min($totalPages, $page + 3);
                for ($i = $start; $i <= $end; $i++):
              ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                  <a class="page-link" href="<?= $baseUrl . 'page=' . $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <!-- Next -->
              <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= ($page < $totalPages) ? $baseUrl . 'page=' . ($page + 1) : '#' ?>">Next &raquo;</a>
              </li>
            </ul>
          </nav>
        </div>
      <?php endif; ?>

  </div>

<?php include('../includes/footer.php'); ?>