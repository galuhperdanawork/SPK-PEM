<?php
include('../config/db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $koneksi->real_escape_string($_POST['id_criteria'] ?? '');
    $name = $koneksi->real_escape_string($_POST['criteria_name'] ?? '');
    $weight = (float)($_POST['weight'] ?? 0);
    $type = $koneksi->real_escape_string($_POST['type'] ?? 'benefit');

    if ($id) {
        $stmt = $koneksi->prepare("UPDATE criteria_weight SET criteria_name = ?, weight = ?, type = ? WHERE id_criteria = ?");
        $stmt->bind_param('sdss', $name, $weight, $type, $id);
        $stmt->execute();
    }
}
header('Location: ../pages/data_kriteria.php');
exit;
