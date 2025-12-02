<?php
include('../config/db.php');
$target = $_POST['target'];
$name = $koneksi->real_escape_string($_POST['name']);
$point = (int)$_POST['point'];
 $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if($target === 'grade'){
  if($id > 0){
    $stmt = $koneksi->prepare("UPDATE grade SET grade_name = ?, grade_point = ? WHERE id_grade = ?");
    $stmt->bind_param("sii", $name, $point, $id);
  } else {
    $stmt = $koneksi->prepare("INSERT INTO grade (grade_name, grade_point) VALUES (?,?)");
    $stmt->bind_param("si", $name, $point);
  }
} elseif($target === 'classification'){
  if($id > 0){
    $stmt = $koneksi->prepare("UPDATE classification SET classification_name = ?, classification_point = ? WHERE id_classification = ?");
    $stmt->bind_param("sii", $name, $point, $id);
  } else {
    $stmt = $koneksi->prepare("INSERT INTO classification (classification_name, classification_point) VALUES (?,?)");
    $stmt->bind_param("si", $name, $point);
  }
} elseif($target === 'inspection_period'){
  if($id > 0){
    $stmt = $koneksi->prepare("UPDATE inspection_period SET period_name = ?, period_point = ? WHERE id_inspection_period = ?");
    $stmt->bind_param("sii", $name, $point, $id);
  } else {
    $stmt = $koneksi->prepare("INSERT INTO inspection_period (period_name, period_point) VALUES (?,?)");
    $stmt->bind_param("si", $name, $point);
  }
} else {
  // unknown target -> redirect
  header("Location: ../pages/data_subkriteria.php");
  exit;
}
$stmt->execute();
header("Location: ../pages/data_subkriteria.php");
