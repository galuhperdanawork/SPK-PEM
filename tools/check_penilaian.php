<?php
include __DIR__ . '/../config/db.php';
$kq=$koneksi->query('SELECT id_criteria,criteria_name,weight,type FROM criteria_weight');
$criteria=[]; while($r=$kq->fetch_assoc()) $criteria[$r['id_criteria']]=$r;
$eq=$koneksi->query('SELECT id_equipment,equipment_name FROM equipment LIMIT 50');
while($e=$eq->fetch_assoc()){
    echo 'EQ '. $e['id_equipment'] . ' - ' . $e['equipment_name'] . PHP_EOL;
    foreach($criteria as $cid=>$c){
        $s=$koneksi->prepare('SELECT nilai FROM penilaian WHERE id_equipment=? AND id_criteria=?');
        $s->bind_param('ii',$e['id_equipment'],$cid);
        $s->execute();
        $res=$s->get_result()->fetch_assoc();
        $val = $res? $res['nilai'] : '(no)';
        echo '  C#'.$cid.' '. $c['criteria_name'] . ' = ' . $val . PHP_EOL;
    }
}
?>