<?php
include('../config/db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only allow admin
    session_start();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../pages/data_alternatif.php'); exit;
    }

    $id_equipment = intval($_POST['id_equipment'] ?? 0);
    $equipment_name = $koneksi->real_escape_string($_POST['equipment_name'] ?? '');
    $inspection_name = $koneksi->real_escape_string($_POST['inspection_name'] ?? '');
    $id_last_inspection = intval($_POST['id_last_inspection'] ?? 0) ?: 'NULL';
    $id_grade = intval($_POST['id_grade'] ?? 0);
    $id_plant = intval($_POST['id_plant'] ?? 0);
    $id_classification = intval($_POST['id_classification'] ?? 0);
    $id_inspection_period = intval($_POST['id_inspection_period'] ?? 0);

    // Update equipment
    $sql = "UPDATE equipment SET equipment_name = '{$equipment_name}', inspection_name = '{$inspection_name}', id_last_inspection = ".($id_last_inspection==='NULL'?'NULL':$id_last_inspection).", id_grade = $id_grade, id_plant = $id_plant, id_classification = $id_classification, id_inspection_period = $id_inspection_period WHERE id_equipment = $id_equipment";
    $koneksi->query($sql);

    // Rebuild penilaian
    $koneksi->query("DELETE FROM penilaian WHERE id_equipment = $id_equipment");
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
        $koneksi->query("INSERT INTO penilaian (id_equipment, id_criteria, nilai) VALUES ('$id_equipment', '$id_criteria', '$nilai')");
    }
}
header('Location: ../pages/data_alternatif.php');
exit;
