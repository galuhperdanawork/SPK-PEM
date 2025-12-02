<?php
include('../config/db.php');
$sql1 = "CREATE TABLE IF NOT EXISTS compute_history (
  id_history INT AUTO_INCREMENT PRIMARY KEY,
  computed_by VARCHAR(100),
  computed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  filters TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$sql2 = "CREATE TABLE IF NOT EXISTS compute_results (
  id_result INT AUTO_INCREMENT PRIMARY KEY,
  history_id INT NOT NULL,
  id_equipment INT NOT NULL,
  score FLOAT NOT NULL,
  details TEXT,
  FOREIGN KEY (history_id) REFERENCES compute_history(id_history) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if($koneksi->query($sql1) && $koneksi->query($sql2)){
  echo "Tables created or already exist.";
} else {
  echo "Error creating tables: " . $koneksi->error;
}
?>