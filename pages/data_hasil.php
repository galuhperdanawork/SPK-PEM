<?php
include('../config/db.php'); 
include('../includes/header.php'); 
include('../includes/sidebar.php');
?>

<div class="container mt-4">
  <span class="welcome-pill no-print">Daftar histori perhitungan ranking equipment</span>

  <!-- Print-only header: logo + app name (hidden on-screen, visible only when printing) -->
  <div class="printable text-center mb-3 d-none d-print-block">
    <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
      <img src="/SPK-SAW/assets/logo.png" alt="Logo" style="width:48px;height:48px;object-fit:contain;">
      <div style="text-align:left;">
        <div style="font-size:18pt;font-weight:700;">Sistem Penunjang Keputusan</div>
        <div style="font-size:10pt;color:#333;">Maintenance Equipment</div>
      </div>
    </div>
    <hr style="margin-top:12px;border-top:2px solid #333;">
  </div>

<?php
if (!empty($_GET['msg'])) {
  $msg = $_GET['msg'];
  if ($msg === 'deleted') echo "<div class='alert alert-success auto-dismiss no-print'>History perhitungan berhasil dihapus.</div>";
  if ($msg === 'error') echo "<div class='alert alert-danger auto-dismiss no-print'>Gagal menghapus history.</div>";
}

?>

<?php
// print data
if (!empty($_GET['history_id'])) {
  $hid = (int)$_GET['history_id'];
  $h = $koneksi->query("SELECT * FROM compute_history WHERE id_history=$hid")->fetch_assoc();

  if (!$h) { 
      echo "<div class='alert alert-warning'>History tidak ditemukan.</div>"; 
  } else {

    echo "<div class='mb-2'>Perhitungan oleh <strong>".htmlspecialchars($h['computed_by'])."</strong> pada <strong>".htmlspecialchars($h['computed_at'])."</strong></div>";
    echo "<div class='mb-2'>";
    echo "<button class='btn btn-sm btn-outline-primary' onclick='window.print()'>Cetak</button> ";

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
      $formId = 'delete-form-'.intval($hid);
      echo "<form id='".$formId."' method='POST' action='/SPK-SAW/process/delete_history.php' style='display:inline'>";
      echo "<input type='hidden' name='history_id' value='".htmlspecialchars($hid)."'>";
      echo "<button type='button' data-form='".$formId."' class='btn btn-icon btn-sm btn-danger ms-2 btn-delete-history' title='Hapus History'>";
      echo '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 6v14c0 1.1.9 2 2 2h4c1.1 0 2-.9 2-2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      echo "</button>";
      echo "</form>";
    }

    echo "</div>";

    // ===================== TABEL HASIL =====================
    echo "<div class='card'><div class='card-body p-0'>
          <table class='table table-bordered mb-0'>
            <thead>
              <tr>
                <th>Rank</th>
                <th>ID</th>
                <th>Equipment</th>
                <th>Inspection Item</th>
                <th>Plant</th>
                <th>Score</th>
              </tr>
            </thead>
            <tbody>";

    // LEFT JOIN untuk mengambil equipment + plant
    $q = $koneksi->query("
          SELECT 
            cr.*, 
            e.equipment_name AS live_name, 
            e.inspection_name AS live_inspection,
            p.plant_name AS live_plant
          FROM compute_results cr 
          LEFT JOIN equipment e ON cr.id_equipment = e.id_equipment 
          LEFT JOIN plant p ON e.id_plant = p.id_plant
          WHERE cr.history_id = $hid 
          ORDER BY cr.score DESC
    ");

    $rank = 1;

    while($row = $q->fetch_assoc()){

      // Nama equipment
      if(!empty($row['live_name'])){
        $displayName = htmlspecialchars($row['live_name']);
        $displayInspection = htmlspecialchars($row['live_inspection'] ?? '');
      } else {
        $det = json_decode($row['details'], true);
        $displayName = htmlspecialchars($det['name'] ?? '(deleted)');
        $displayInspection = htmlspecialchars($det['inspection'] ?? '');
      }

      // PLANT
      if (!empty($row['live_plant'])) {
          $displayPlant = htmlspecialchars($row['live_plant']);
      } else {
          $det = json_decode($row['details'], true);
          $displayPlant = htmlspecialchars($det['plant'] ?? '(unknown)');
      }

      echo "<tr>
              <td>$rank</td>
              <td>".$row['id_equipment']."</td>
              <td>".$displayName."</td>
              <td>".$displayInspection."</td>
              <td>".$displayPlant."</td>
              <td>".round($row['score'],4)."</td>
            </tr>";

      $rank++;
    }

    echo "</tbody></table></div></div>";
  }

} else {

  // ===================== LIST RIWAYAT =====================
  $qh = $koneksi->query("SELECT * FROM compute_history ORDER BY computed_at DESC");

  echo "<div class='card'><div class='card-body'><ul class='list-group list-group-flush'>";

  while($hh = $qh->fetch_assoc()){
    $date = $hh['computed_at'];
    $who = htmlspecialchars($hh['computed_by']);

    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
    echo "<div><a href='?history_id={$hh['id_history']}'>".htmlspecialchars($date)."</a> &nbsp;<small>by $who</small></div>";

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
      $fid = 'delete-form-'.intval($hh['id_history']);
      echo "<div>";
      echo "<form id='".$fid."' method='POST' action='/SPK-SAW/process/delete_history.php' style='margin:0'>";
      echo "<input type='hidden' name='history_id' value='".htmlspecialchars($hh['id_history'])."'>";
      echo "<button type='button' data-form='".$fid."' class='btn btn-icon btn-sm btn-danger btn-delete-history' title='Hapus'>";
      echo '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 6v14c0 1.1.9 2 2 2h4c1.1 0 2-.9 2-2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      echo "</button>";
      echo "</form>";
      echo "</div>";
    }

    echo "</li>";
  }

  echo "</ul></div></div>";
}
?>

</div>

<?php include('../includes/footer.php'); ?>
