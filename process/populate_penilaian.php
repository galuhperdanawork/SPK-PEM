<?php
include('../config/db.php');
if (php_sapi_name() !== 'cli') die('Run from CLI only');

echo "Populating missing penilaian for equipments...\n";

$kq = $koneksi->query("SELECT id_criteria, criteria_name FROM criteria_weight ORDER BY id_criteria");
$criteria = [];
while($r = $kq->fetch_assoc()) $criteria[$r['id_criteria']] = $r['criteria_name'];

$eq = $koneksi->query("SELECT * FROM equipment");
$countInserted = 0;
while($e = $eq->fetch_assoc()){
    $id_eq = $e['id_equipment'];
    // check setiap criteria
    foreach($criteria as $cid => $cname){
        $s = $koneksi->prepare("SELECT id_penilaian, nilai FROM penilaian WHERE id_equipment=? AND id_criteria=?");
        $s->bind_param('ii', $id_eq, $cid);
        $s->execute();
        $res = $s->get_result()->fetch_assoc();
        if($res) continue; // sudah ada

        $nilai = 0;
        $cn = strtolower(trim($cname));
        if(strpos($cn,'grade') !== false){
            $q = $koneksi->prepare("SELECT grade_point FROM grade WHERE id_grade=?");
            $q->bind_param('i', $e['id_grade']); $q->execute(); $d = $q->get_result()->fetch_assoc();
            $nilai = $d ? $d['grade_point'] : 0;
        } elseif(strpos($cn,'classification') !== false){
            $q = $koneksi->prepare("SELECT classification_point FROM classification WHERE id_classification=?");
            $q->bind_param('i', $e['id_classification']); $q->execute(); $d = $q->get_result()->fetch_assoc();
            $nilai = $d ? $d['classification_point'] : 0;
        } elseif(strpos($cn,'inspection') !== false || strpos($cn,'period') !== false){
            $q = $koneksi->prepare("SELECT period_point FROM inspection_period WHERE id_inspection_period=?");
            $q->bind_param('i', $e['id_inspection_period']); $q->execute(); $d = $q->get_result()->fetch_assoc();
            $nilai = $d ? $d['period_point'] : 0;
        }

        $ins = $koneksi->prepare("INSERT INTO penilaian (id_equipment, id_criteria, nilai) VALUES (?,?,?)");
        $ins->bind_param('iid', $id_eq, $cid, $nilai);
        if($ins->execute()) {
            $countInserted++;
        } else {
            echo "Failed insert for equipment $id_eq criteria $cid: " . $koneksi->error . "\n";
        }
    }
}

echo "Done. Inserted $countInserted missing penilaian rows.\n";

?>
