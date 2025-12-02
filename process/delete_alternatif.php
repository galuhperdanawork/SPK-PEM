<?php
include('../config/db.php');
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // delete penilaian 
    $koneksi->query("DELETE FROM penilaian WHERE id_equipment = $id");
    // delete equipment
    $koneksi->query("DELETE FROM equipment WHERE id_equipment = $id");
}
header('Location: ../pages/data_alternatif.php');
exit;
