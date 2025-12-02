<?php
include('../config/db.php');

$kode = $_POST['kode_alat'];
$nama = $_POST['nama_alat'];
$tipe = $_POST['tipe'];
$grade = $_POST['grade'];

mysqli_query($koneksi, "INSERT INTO equipment (kode_alat, nama_alat, tipe, grade) 
VALUES ('$kode', '$nama', '$tipe', '$grade')");

header("Location: ../pages/equipment.php");
?>
