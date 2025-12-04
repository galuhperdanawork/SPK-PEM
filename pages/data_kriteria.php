
<?php
include('../config/db.php');
include('../includes/header.php');
include('../includes/sidebar.php');
?>
 

<div class="container mt-3">
  <div class="page-header">
    <div>
      <span class="welcome-pill">Melihat dan mengelola data Kriteria — Kelola bobot dan tipe kriteria untuk perhitungan</span>
    </div>
    <div class="criteria-badge">Total: <?php $cnt = $koneksi->query("SELECT COUNT(*) AS c FROM criteria_weight")->fetch_assoc()['c'] ?? 0; echo intval($cnt); ?></div>
  </div>

  <!-- Form edit -->
  <div class="card mb-3 simple-card">
    <div class="card-body">
        <?php
    // edit mode
    if (isset($_GET['edit'])) {
      $id = $koneksi->real_escape_string($_GET['edit']);
      $row = $koneksi->query("SELECT * FROM criteria_weight WHERE id_criteria='$id'")->fetch_assoc();
      if ($row) {
        ?>
        <form method="post" action="../process/update_kriteria.php" class="row g-2 mb-0 align-items-end">
          <input type="hidden" name="id_criteria" value="<?= htmlspecialchars($row['id_criteria']) ?>">
          <div class="col-12 col-md-6"><label class="form-label small muted">Label</label><input name="criteria_name" value="<?= htmlspecialchars($row['criteria_name']) ?>" class="form-control" placeholder="Label (e.g. Classification)" required></div>
          <div class="col-6 col-md-2"><label class="form-label small muted">Bobot</label><input name="weight" value="<?= htmlspecialchars($row['weight']) ?>" step="0.01" type="number" class="form-control" placeholder="0.25" required></div>
          <div class="col-6 col-md-2"><label class="form-label small muted">Tipe</label>
            <select name="type" class="form-select">
              <option value="benefit" <?= $row['type']=='benefit' ? 'selected' : '' ?>>Benefit</option>
              <option value="cost" <?= $row['type']=='cost' ? 'selected' : '' ?>>Cost</option>
            </select>
          </div>

          <div class="col-12 col-md-2 text-md-end">
            <button class="btn btn-primary">Update</button>
            <a href="data_kriteria.php" class="btn btn-outline-secondary ms-2">Batal</a>
          </div>
        </form>
        <?php
      } else {
        echo '<div class="alert alert-danger">Kriteria tidak ditemukan.</div>';
      }
    } 
    ?>
   

  </div>
</div>

  <!-- Tabel kriteria -->
  <div class="card simple-card">
    <div class="card-body p-0">
      <?php
      $q = $koneksi->query("SELECT * FROM criteria_weight ORDER BY id_criteria");
      if (!$q || $q->num_rows === 0) {
        echo '<div class="p-3"><div class="alert alert-info mb-0">Belum ada kriteria. Tambahkan kriteria melalui form di atas.</div></div>';
      } else {
        ?>
        <div class="table-responsive">
        <table class="table table-sm table-small mb-0">
          <thead><tr><th style="width:60px">No</th><th>Label</th><th style="width:120px">Bobot</th><th style="width:120px">Tipe</th><th style="width:120px">Aksi</th></tr></thead>
          <tbody>
        <?php
        $no = 1;
        while($r = $q->fetch_assoc()){
          $typeBadge = $r['type'] === 'benefit' ? '<span class="badge bg-success">Benefit</span>' : '<span class="badge bg-danger">Cost</span>';
          echo "<tr><td>{$no}</td><td><strong>".htmlspecialchars($r['criteria_name'])."</strong></td><td>".htmlspecialchars($r['weight'])."</td><td>{$typeBadge}</td>";
            echo "<td class='actions-cell text-end'>";
              echo "<a href='data_kriteria.php?edit={$r['id_criteria']}' class='btn btn-sm btn-outline-warning me-1' title='Edit'><span class=\"material-icons\">edit</span></a>";
            echo "</td>";
          $no++;
        }
        ?>
          </tbody>
        </table>
        </div>
        <?php
      }
      ?>
    </div>
  </div>

</div>

<?php include('../includes/footer.php'); ?>
