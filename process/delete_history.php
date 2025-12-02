<?php
include('../config/db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['history_id'])) {
    header('Location: ../pages/data_hasil.php'); exit;
}

$hid = (int)$_POST['history_id'];

$stmt = $koneksi->prepare("DELETE FROM compute_history WHERE id_history = ?");
$stmt->bind_param('i', $hid);
if ($stmt->execute()) {
    header('Location: ../pages/data_hasil.php?msg=deleted');
    exit;
} else {
    header('Location: ../pages/data_hasil.php?msg=error');
    exit;
}

?>
