<?php
$include_path = '../config/db.php';
include $include_path;
$query = "SELECT e.id_equipment, e.equipment_name, e.inspection_name,
                 g.grade_name, p.plant_name, ip.period_name,
                 li.year AS last_year
          FROM equipment e
          JOIN grade g ON e.id_grade = g.id_grade
          JOIN plant p ON e.id_plant = p.id_plant
          JOIN inspection_period ip ON e.id_inspection_period = ip.id_inspection_period
          LEFT JOIN last_inspection li ON e.id_last_inspection = li.id_last_inspection";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Alternatif</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h3>Data Alternatif (Equipment)</h3>
  <table class="table table-bordered mt-3">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>Nama Equipment</th>
        <th>Inspection Item</th>
        <th>Grade</th>
        <th>Plant</th>
        <th>Last Inspection</th>
        <th>Inspection Period</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; while($row=mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['equipment_name'] ?></td>
        <td><?= $row['inspection_name'] ?></td>
        <td><?= $row['grade_name'] ?></td>
        <td><?= $row['plant_name'] ?></td>
        <td><?= htmlspecialchars($row['last_year'] ?? '') ?></td>
        <td><?= $row['period_name'] ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</body>
</html>
