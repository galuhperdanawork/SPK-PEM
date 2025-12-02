<?php
include('../config/db.php');
include('../includes/header.php');
include('../includes/sidebar.php');
?>

<?php
$edit = $_GET['edit'] ?? '';
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<span class="welcome-pill">Melihat dan mengelola data Subkriteria (konversi dari skala likert)</span>
<div class="row mt-4">
  <div class="col-md-6">
    <!-- Grade -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title">Grade</h5>
        <?php
        $g_name = '';
        $g_point = '';
        $g_id = 0;
        if($edit === 'grade' && $edit_id){
          $grr = $koneksi->query("SELECT * FROM grade WHERE id_grade = " . $edit_id);
          if($grr && $grr->num_rows){ $grr = $grr->fetch_assoc(); $g_name = $grr['grade_name']; $g_point = $grr['grade_point']; $g_id = $grr['id_grade']; }
        }
        ?>
        <form action="../process/insert_subkriteria.php" method="post" class="input-group mb-2">
          <input type="hidden" name="target" value="grade">
          <?php if($g_id): ?><input type="hidden" name="id" value="<?= $g_id ?>"><?php endif; ?>
          <input name="name" class="form-control" placeholder="Grade name (A)" value="<?= htmlspecialchars($g_name) ?>">
          <input name="point" type="number" class="form-control" placeholder="Point (5)" value="<?= htmlspecialchars($g_point) ?>">
          <button class="btn btn-<?= $g_id ? 'primary' : 'success' ?>"><?= $g_id ? 'Update' : 'Tambah' ?></button>
        </form>
        <?php if($g_id): ?><a href="data_subkriteria.php" class="btn btn-sm btn-outline-danger mb-2">Batal edit</a><?php endif; ?>
        <table class="table table-sm">
          <thead><tr><th>Grade</th><th>Point</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php
          $gr = $koneksi->query("SELECT * FROM grade ORDER BY id_grade");
          while($g = $gr->fetch_assoc()):
          ?>
            <tr>
              <td><?= htmlspecialchars($g['grade_name']) ?></td>
              <td><?= (int)$g['grade_point'] ?></td>
              <td>
                <a href="data_subkriteria.php?edit=grade&id=<?= $g['id_grade'] ?>" class="btn btn-icon btn-sm btn-warning" title="Edit">
                  <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" stroke="currentColor" stroke-width="0" fill="currentColor"/></svg>
                </a>
                
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Inspection Period -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Inspection Period</h5>
        <?php
        $p_name = '';
        $p_point = '';
        $p_id = 0;
        if($edit === 'inspection_period' && $edit_id){
          $prr = $koneksi->query("SELECT * FROM inspection_period WHERE id_inspection_period = " . $edit_id);
          if($prr && $prr->num_rows){ $prr = $prr->fetch_assoc(); $p_name = $prr['period_name']; $p_point = $prr['period_point']; $p_id = $prr['id_inspection_period']; }
        }
        ?>
        <form action="../process/insert_subkriteria.php" method="post" class="input-group mb-2">
          <input type="hidden" name="target" value="inspection_period">
          <?php if($p_id): ?><input type="hidden" name="id" value="<?= $p_id ?>"><?php endif; ?>
          <input name="name" class="form-control" placeholder="Period name (2-4)" value="<?= htmlspecialchars($p_name) ?>">
          <input name="point" type="number" class="form-control" placeholder="Point (5)" value="<?= htmlspecialchars($p_point) ?>">
          <button class="btn btn-<?= $p_id ? 'primary' : 'success' ?>"><?= $p_id ? 'Update' : 'Tambah' ?></button>
        </form>
        <?php if($p_id): ?><a href="data_subkriteria.php" class="btn btn-sm btn-outline-danger mb-2">Batal edit</a><?php endif; ?>
        <table class="table table-sm">
          <thead><tr><th>Period</th><th>Point</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php
          $pr = $koneksi->query("SELECT * FROM inspection_period ORDER BY id_inspection_period");
          while($p = $pr->fetch_assoc()):
          ?>
            <tr>
              <td><?= htmlspecialchars($p['period_name']) ?></td>
              <td><?= (int)$p['period_point'] ?></td>
              <td>
                <a href="data_subkriteria.php?edit=inspection_period&id=<?= $p['id_inspection_period'] ?>" class="btn btn-icon btn-sm btn-warning" title="Edit">
                  <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" stroke="currentColor" stroke-width="0" fill="currentColor"/></svg>
                </a>
                
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <!-- Classification -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Classification</h5>
        <?php
        $c_name = '';
        $c_point = '';
        $c_id = 0;
        if($edit === 'classification' && $edit_id){
          $crr = $koneksi->query("SELECT * FROM classification WHERE id_classification = " . $edit_id);
          if($crr && $crr->num_rows){ $crr = $crr->fetch_assoc(); $c_name = $crr['classification_name']; $c_point = $crr['classification_point']; $c_id = $crr['id_classification']; }
        }
        ?>
        <form action="../process/insert_subkriteria.php" method="post" class="input-group mb-2">
          <input type="hidden" name="target" value="classification">
          <?php if($c_id): ?><input type="hidden" name="id" value="<?= $c_id ?>"><?php endif; ?>
          <input name="name" class="form-control" placeholder="Classification name (Rotating work)" value="<?= htmlspecialchars($c_name) ?>">
          <input name="point" type="number" class="form-control" placeholder="Point (4)" value="<?= htmlspecialchars($c_point) ?>">
          <button class="btn btn-<?= $c_id ? 'primary' : 'success' ?>"><?= $c_id ? 'Update' : 'Tambah' ?></button>
        </form>
        <?php if($c_id): ?><a href="data_subkriteria.php" class="btn btn-sm btn-outline-danger mb-2">Batal edit</a><?php endif; ?>
        <table class="table table-sm">
          <thead><tr><th>Classification</th><th>Point</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php
          $cr = $koneksi->query("SELECT * FROM classification ORDER BY id_classification");
          while($c = $cr->fetch_assoc()):
          ?>
            <tr>
              <td><?= htmlspecialchars($c['classification_name']) ?></td>
              <td><?= (int)$c['classification_point'] ?></td>
              <td>
                <a href="data_subkriteria.php?edit=classification&id=<?= $c['id_classification'] ?>" class="btn btn-icon btn-sm btn-warning" title="Edit">
                  <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" stroke="currentColor" stroke-width="0" fill="currentColor"/></svg>
                </a>
                
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

