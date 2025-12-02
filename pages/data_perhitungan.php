<?php
include('../config/db.php');
include('../includes/header.php');
include('../includes/sidebar.php');
?>

<div class="container mt-4">
<span class="welcome-pill">Lakukan perhitungan SAW — filter dan jalankan untuk melihat hasil</span>

<?php
$grade = $koneksi->query("SELECT * FROM grade");
$plant = $koneksi->query("SELECT * FROM plant");
$classification = $koneksi->query("SELECT * FROM classification");
$inspection_period = $koneksi->query("SELECT * FROM inspection_period");
$last_inspection = $koneksi->query("SELECT * FROM last_inspection ORDER BY year DESC");

// filter values
$f_grade = $_GET['id_grade'] ?? '';
$f_plant = $_GET['id_plant'] ?? '';
$f_classification = $_GET['id_classification'] ?? '';
$f_period = $_GET['id_inspection_period'] ?? '';
$f_last = $_GET['id_last_inspection'] ?? '';
?>

<!-- Filter form -->
<div class="card mb-3">
  <div class="card-body">
    <form method="get" class="row g-2 mb-0">
  <div class="col-md-2">
    <select name="id_grade" class="form-select">
      <option value="">-- Semua Grade --</option>
      <?php foreach($grade as $g) { $sel = ($f_grade== $g['id_grade'])? 'selected':''; echo "<option value='{$g['id_grade']}' $sel>{$g['grade_name']}</option>"; } ?>
    </select>
  </div>
  <div class="col-md-2">
    <select name="id_plant" class="form-select">
      <option value="">-- Semua Plant --</option>
      <?php foreach($plant as $p) { $sel = ($f_plant== $p['id_plant'])? 'selected':''; echo "<option value='{$p['id_plant']}' $sel>{$p['plant_name']}</option>"; } ?>
    </select>
  </div>
  <div class="col-md-2">
    <select name="id_classification" class="form-select">
      <option value="">-- Semua Classification --</option>
      <?php foreach($classification as $c) { $sel = ($f_classification== $c['id_classification'])? 'selected':''; echo "<option value='{$c['id_classification']}' $sel>{$c['classification_name']}</option>"; } ?>
    </select>
  </div>
  <div class="col-md-2">
    <select name="id_inspection_period" class="form-select">
      <option value="">-- Semua Period --</option>
      <?php foreach($inspection_period as $ip) { $sel = ($f_period== $ip['id_inspection_period'])? 'selected':''; echo "<option value='{$ip['id_inspection_period']}' $sel>{$ip['period_name']}</option>"; } ?>
    </select>
  </div>
  <div class="col-md-2">
    <select name="id_last_inspection" class="form-select">
      <option value="">-- Semua Last Inspection --</option>
      <?php foreach($last_inspection as $li) { $sel = ($f_last== $li['id_last_inspection'])? 'selected':''; echo "<option value='{$li['id_last_inspection']}' $sel>{$li['year']}</option>"; } ?>
    </select>
  </div>
  <div class="col-md-2">
    <button class="btn btn-secondary">Tampilkan</button>
  </div>
    </form>
  </div>
</div>

<!-- Hitung button  -->
<?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
  <div class="card mb-3">
    <div class="card-body">
    <form method="post" action="../process/compute_saw.php" class="mb-0">
    <input type="hidden" name="id_grade" value="<?=htmlspecialchars($f_grade)?>">
    <input type="hidden" name="id_plant" value="<?=htmlspecialchars($f_plant)?>">
    <input type="hidden" name="id_classification" value="<?=htmlspecialchars($f_classification)?>">
    <input type="hidden" name="id_inspection_period" value="<?=htmlspecialchars($f_period)?>">
    <input type="hidden" name="id_last_inspection" value="<?=htmlspecialchars($f_last)?>">
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="purge_prev" name="purge_previous" value="1">
        <label class="form-check-label" for="purge_prev">Hapus hasil perhitungan sebelumnya</label>
      </div>
      <button class="btn btn-primary">Hitung (hanya data terfilter)</button>
    </form>
    </div>
  </div>
<?php endif; ?>

<?php
// ambil semua equipment beserta nilai kriterianya
$whereClauses = [];
if($f_grade) $whereClauses[] = "e.id_grade=".(int)$f_grade;
if($f_plant) $whereClauses[] = "e.id_plant=".(int)$f_plant;
if($f_classification) $whereClauses[] = "e.id_classification=".(int)$f_classification;
if($f_period) $whereClauses[] = "e.id_inspection_period=".(int)$f_period;
if($f_last) $whereClauses[] = "e.id_last_inspection=".(int)$f_last;
$whereSql = count($whereClauses)? 'WHERE '.implode(' AND ', $whereClauses):'';

$q = $koneksi->query("SELECT 
    e.id_equipment,
    e.equipment_name,
    g.grade_point,
    c.classification_point,
    ip.period_point
  FROM equipment e
  LEFT JOIN grade g ON e.id_grade = g.id_grade
  LEFT JOIN classification c ON e.id_classification = c.id_classification
  LEFT JOIN inspection_period ip ON e.id_inspection_period = ip.id_inspection_period
  $whereSql");

$items = [];
while($r = $q->fetch_assoc()) $items[] = $r;
// ambil kriteria dan bobot
$kq = $koneksi->query("SELECT * FROM criteria_weight ORDER BY id_criteria");
$criteria = [];
while($k = $kq->fetch_assoc()) $criteria[$k['criteria_name']] = $k;

// bangun matrix nilai awal (berdasarkan nama kriteria)
$matrix = [];
foreach($items as $it){
  $id = $it['id_equipment'];
  $matrix[$id]['name'] = $it['equipment_name'];
  foreach($criteria as $key=>$meta){
    switch (strtolower($key)) {
      case 'grade': $matrix[$id]['raw'][$key] = $it['grade_point']; break;
      case 'classification': $matrix[$id]['raw'][$key] = $it['classification_point']; break;
      case 'inspection period':
      case 'period': $matrix[$id]['raw'][$key] = $it['period_point']; break;
      default: $matrix[$id]['raw'][$key] = 0;
    }
  }
}

// hitung normalisasi
$normalized = [];
foreach($criteria as $key=>$meta){
  $values = array_column(array_map(fn($r) => $r['raw'][$key], $matrix), null);
  $values = array_filter(array_column($matrix, 'raw'), fn($r) => isset($r[$key]));
  $vals = [];
  foreach ($matrix as $row) {
    $vals[] = $row['raw'][$key] ?? 0;
  }
  $max = count($vals) ? max($vals) : 0;
  $min = count($vals) ? min($vals) : 0;

  foreach($matrix as $id=>$row){
    $x = $row['raw'][$key];
    if ($meta['type'] == 'benefit') {
      $r = ($max > 0) ? ($x / $max) : 0;
    } else {
      $r = ($x > 0) ? ($min / $x) : 0;
    }
    $normalized[$id][$key] = $r;
  }
}

// hitung bobot dan total
$results = [];
foreach($matrix as $id=>$row){
  $total = 0;
  foreach($criteria as $key=>$meta){
    $w = (float)$meta['weight'];
    $r = $normalized[$id][$key] ?? 0;
    $weighted = $r * $w;
    $results[$id]['weights'][$key] = $weighted;
    $total += $weighted;
  }
  $results[$id]['total'] = $total;
  $results[$id]['name'] = $row['name'];
}

// tampilkan hasil dengan pagination (maks 10 item per halaman)
$perPage = 10; // maksimal item per halaman
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// gunakan urutan dari matrix (yang dibangun berdasarkan query $items)
$ids = array_keys($matrix);
$totalItems = count($ids);
$totalPages = $totalItems > 0 ? ceil($totalItems / $perPage) : 1;
$paged_ids = array_slice($ids, $offset, $perPage);

// base url untuk pagination (pertahankan filter params)
$baseParams = [];
if($f_grade) $baseParams['id_grade'] = $f_grade;
if($f_plant) $baseParams['id_plant'] = $f_plant;
if($f_classification) $baseParams['id_classification'] = $f_classification;
if($f_period) $baseParams['id_inspection_period'] = $f_period;
if($f_last) $baseParams['id_last_inspection'] = $f_last;
$baseUrl = 'data_perhitungan.php';
if (!empty($baseParams)) $baseUrl .= '?' . http_build_query($baseParams) . '&'; else $baseUrl .= '?';

// Matrix Nilai Awal (hanya paged ids)
echo "<div class='card mb-3'><div class='card-body'><h5 class='card-title'>Matrix Nilai Awal</h5>";
echo "<div class='table-responsive'><table class='table table-bordered table-sm mb-0'><thead><tr><th>Equipment</th>";
foreach($criteria as $k=>$m) echo "<th>{$m['criteria_name']}</th>";
echo "</tr></thead><tbody>";
foreach($paged_ids as $id){
  if(!isset($matrix[$id])) continue;
  $row = $matrix[$id];
  echo "<tr><td>{$row['name']}</td>";
  foreach($criteria as $k=>$m) echo "<td>{$row['raw'][$k]}</td>";
  echo "</tr>";
}
echo "</tbody></table></div></div></div>";

// Normalisasi (hanya paged ids)
echo "<div class='card mb-3'><div class='card-body'><h5 class='card-title'>Normalisasi</h5>";
echo "<div class='table-responsive'><table class='table table-bordered table-sm mb-0'><thead><tr><th>Equipment</th>";
foreach($criteria as $k=>$m) echo "<th>{$m['criteria_name']}</th>";
echo "</tr></thead><tbody>";
foreach($paged_ids as $id){
  if(!isset($normalized[$id])) continue;
  $vals = $normalized[$id];
  echo "<tr><td>{$matrix[$id]['name']}</td>";
  foreach($criteria as $k=>$m) echo "<td>".round($vals[$k],4)."</td>";
  echo "</tr>";
}
echo "</tbody></table></div></div></div>";

// Bobot & Total (hanya paged ids)
echo "<div class='card mb-3'><div class='card-body'><h5 class='card-title'>Bobot & Total</h5>";
echo "<div class='table-responsive'><table class='table table-bordered table-sm mb-0'><thead><tr><th>Equipment</th>";
foreach($criteria as $k=>$m) echo "<th>{$m['criteria_name']} (w={$m['weight']})</th>";
echo "<th>Total</th></tr></thead><tbody>";
foreach($paged_ids as $id){
  if(!isset($results[$id])) continue;
  $res = $results[$id];
  echo "<tr><td>{$res['name']}</td>";
  foreach($criteria as $k=>$m) echo "<td>".round($res['weights'][$k],4)."</td>";
  echo "<td>".round($res['total'],4)."</td></tr>";
}
echo "</tbody></table></div></div></div>";

// Pagination UI
if ($totalPages > 1) {
  echo "<div class='d-flex align-items-center justify-content-between mb-4'>";
  echo "<div>Menampilkan " . ($totalItems ? ($offset+1) : 0) . " - " . min($offset + $perPage, $totalItems) . " dari $totalItems</div>";
  echo "<nav aria-label='Pagination'><ul class='pagination mb-0'>";

  // Previous
  $prevClass = ($page <= 1) ? 'disabled' : '';
  $prevHref = ($page > 1) ? $baseUrl . 'page=' . ($page - 1) : '#';
  echo "<li class='page-item $prevClass'><a class='page-link' href='$prevHref'>&laquo; Prev</a></li>";

  // Page window
  $start = max(1, $page - 3);
  $end = min($totalPages, $page + 3);
  for ($i = $start; $i <= $end; $i++) {
    $active = ($i == $page) ? 'active' : '';
    $href = $baseUrl . 'page=' . $i;
    echo "<li class='page-item $active'><a class='page-link' href='$href'>$i</a></li>";
  }

  // Next
  $nextClass = ($page >= $totalPages) ? 'disabled' : '';
  $nextHref = ($page < $totalPages) ? $baseUrl . 'page=' . ($page + 1) : '#';
  echo "<li class='page-item $nextClass'><a class='page-link' href='$nextHref'>Next &raquo;</a></li>";

  echo "</ul></nav></div>";
}
?>
</div>

<?php include('../includes/footer.php'); ?>
