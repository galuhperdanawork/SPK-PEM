<?php
include('../config/db.php');
$target = $_GET['target'] ?? '';
$id = intval($_GET['id'] ?? 0);
if ($id && in_array($target, ['grade','classification','inspection_period'])) {
    if ($target === 'grade') {
        $koneksi->query("DELETE FROM grade WHERE id_grade = $id");
    } elseif ($target === 'classification') {
        $koneksi->query("DELETE FROM classification WHERE id_classification = $id");
    } elseif ($target === 'inspection_period') {
        $koneksi->query("DELETE FROM inspection_period WHERE id_inspection_period = $id");
    }
}
header('Location: ../pages/data_subkriteria.php');
exit;
