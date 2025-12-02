<?php
include('../config/db.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
		die('Access denied.');
}

// collect filters
$filters = [];
$where = [];
if(!empty($_POST['id_grade'])) { $filters['id_grade'] = (int)$_POST['id_grade']; $where[] = "e.id_grade=".$filters['id_grade']; }
if(!empty($_POST['id_plant'])) { $filters['id_plant'] = (int)$_POST['id_plant']; $where[] = "e.id_plant=".$filters['id_plant']; }
if(!empty($_POST['id_classification'])) { $filters['id_classification'] = (int)$_POST['id_classification']; $where[] = "e.id_classification=".$filters['id_classification']; }
if(!empty($_POST['id_inspection_period'])) { $filters['id_inspection_period'] = (int)$_POST['id_inspection_period']; $where[] = "e.id_inspection_period=".$filters['id_inspection_period']; }
if(!empty($_POST['id_last_inspection'])) { $filters['id_last_inspection'] = (int)$_POST['id_last_inspection']; $where[] = "e.id_last_inspection=".$filters['id_last_inspection']; }
$where_sql = '';
if(count($where)>0) $where_sql = 'WHERE '.implode(' AND ', $where);

//equipments matching filter
$sql = "SELECT e.id_equipment, e.equipment_name, e.id_grade, e.id_classification, e.id_inspection_period,
			   e.inspection_name, p.plant_name,
			   g.grade_point, c.classification_point, ip.period_point
		  FROM equipment e
		  LEFT JOIN grade g ON e.id_grade = g.id_grade
		  LEFT JOIN classification c ON e.id_classification = c.id_classification
		  LEFT JOIN inspection_period ip ON e.id_inspection_period = ip.id_inspection_period
		  LEFT JOIN plant p ON e.id_plant = p.id_plant
		  $where_sql";
$res = $koneksi->query($sql);
$items = [];
while($r=$res->fetch_assoc()) $items[] = $r;
if(count($items)===0){
		header('Location: ../pages/data_perhitungan.php?msg=no_items'); exit;
}

// get criteria
$kq = $koneksi->query("SELECT * FROM criteria_weight ORDER BY id_criteria");
$criteria = [];
while($k=$kq->fetch_assoc()) $criteria[$k['id_criteria']] = $k;

// build raw matrix 
$matrix = [];
foreach($items as $it){
	$id = $it['id_equipment'];
	$matrix[$id]['name'] = $it['equipment_name'];
	// store snapshot meta so we can save inspection and plant in details
	$matrix[$id]['inspection'] = $it['inspection_name'] ?? '';
	$matrix[$id]['plant'] = $it['plant_name'] ?? '';
	foreach($criteria as $cid=>$meta){
		$s = $koneksi->prepare("SELECT nilai FROM penilaian WHERE id_equipment=? AND id_criteria=?");
		$s->bind_param("ii", $id, $cid);
		$s->execute();
		$res2 = $s->get_result()->fetch_assoc();
		$val = $res2 ? (float)$res2['nilai'] : 0;
		if(empty($val)){
			$cname = strtolower(trim($criteria[$cid]['criteria_name']));
			if(strpos($cname,'grade') !== false){
				$val = isset($it['grade_point']) ? (float)$it['grade_point'] : 0;
			} elseif(strpos($cname,'classification') !== false){
				$val = isset($it['classification_point']) ? (float)$it['classification_point'] : 0;
			} elseif(strpos($cname,'inspection') !== false || strpos($cname,'period') !== false){
				$val = isset($it['period_point']) ? (float)$it['period_point'] : 0;
			}
		}
		$matrix[$id]['raw'][$cid] = $val;
	}
}

// normalization
$normalized = [];
foreach($criteria as $cid=>$meta){
	$values = array_map(fn($r) => $r['raw'][$cid] ?? 0, $matrix);
	$max = count($values)>0?max($values):0; $min = count($values)>0?min($values):0;
	foreach($matrix as $id=>$row){
		$x = $row['raw'][$cid] ?? 0;
		if($meta['type']=='benefit'){
			$rval = ($max>0)?($x/$max):0;
		} else {
			$rval = ($x>0)?($min/$x):0;
		}
		$normalized[$id][$cid]=$rval;
	}
}

// compute totals
$results = [];
foreach($matrix as $id=>$row){
	$total=0;
	foreach($criteria as $cid=>$meta){
		$total += ($normalized[$id][$cid] ?? 0) * (float)$meta['weight'];
	}
	$results[$id]=$total;
}

// save history
$filters_json = $koneksi->real_escape_string(json_encode($filters));
$user = $koneksi->real_escape_string($_SESSION['nama_pengguna']);
$ins = $koneksi->query("INSERT INTO compute_history (computed_by, filters) VALUES ('{$user}', '{$filters_json}')");
if(!$ins){
	die('Failed saving history: '.$koneksi->error);
}
$history_id = $koneksi->insert_id;

$purge = !empty($_POST['purge_previous']);

// save results
foreach($results as $id=>$score){
	$detailsArr = ['raw'=>$matrix[$id]['raw'], 'name'=>$matrix[$id]['name']];
	if (isset($matrix[$id]['inspection'])) $detailsArr['inspection'] = $matrix[$id]['inspection'];
	if (isset($matrix[$id]['plant'])) $detailsArr['plant'] = $matrix[$id]['plant'];
	$details = $koneksi->real_escape_string(json_encode($detailsArr));
	$sql = "INSERT INTO compute_results (history_id, id_equipment, score, details) VALUES ($history_id, $id, $score, '$details')";
	$ok = $koneksi->query($sql);
	if(!$ok){
		//tulis debug info to log
		$msg = date('Y-m-d H:i:s') . " | FAIL INSERT: history={$history_id} id={$id} score={$score} sql={$sql} error=" . $koneksi->error . "\n";
		file_put_contents(__DIR__ . '/compute_debug.log', $msg, FILE_APPEND);
	} else {
		$msg = date('Y-m-d H:i:s') . " | OK INSERT: history={$history_id} id={$id} score={$score}\n";
		file_put_contents(__DIR__ . '/compute_debug.log', $msg, FILE_APPEND);
	}
}

if($purge){
	$ids = array_keys($results);
	if(count($ids)>0){
		$ids_sql = implode(',', array_map('intval', $ids));
		// delete penilaian first
		$del_pen = "DELETE FROM penilaian WHERE id_equipment IN ($ids_sql)";
		$koneksi->query($del_pen);
		$del_eq = "DELETE FROM equipment WHERE id_equipment IN ($ids_sql)";
		$koneksi->query($del_eq);
		$log = date('Y-m-d H:i:s') . " | PURGE: deleted equipment and penilaian for equipment_ids=($ids_sql)\n";
		file_put_contents(__DIR__ . '/compute_debug.log', $log, FILE_APPEND);
	}
}

// redirect to hasil page (detail)
header('Location: ../pages/data_hasil.php?history_id='.$history_id);
exit;
