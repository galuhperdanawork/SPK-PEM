<?php
include('../config/db.php');

$id = "mc-" . substr(md5(rand()), 0, 8);
$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

$stmt = $koneksi->prepare("INSERT INTO users (id_user, username, password, role) VALUES (?,?,?,?)");
$stmt->bind_param("ssss", $id, $username, $password, $role);
$stmt->execute();

header("Location: ../pages/users.php");
exit;
?>
