<?php
$db->query("SELECT * FROM wave_counter_<myname>");
if($db->numrows() == 1){
	$row = $db->fObject();
	$stime = $row->time;
	$now = time();
	$wave = $row->wave;
	if($wave != 2){
		if($stime >= $now + 13 - $wave * 120 && $stime <= $now + 17 - $wave * 120){
			if($wave != 9){
				bot::send("Wave $wave Incoming.", "guild");
			}else{
				bot::send("General Incoming.", "guild");
			}
			$wave++;
			$db->query("UPDATE `wave_counter_<myname>` SET `wave` = '$wave'");
			if($wave == 10){
				$db->query("DELETE FROM wave_counter_<myname> WHERE `wave` = '$wave'");
			}
		}
	}elseif($stime >= $now + 13 - 270 && $stime <= $now + 17 - 270){
		bot::send("Wave $wave Incoming.", "guild");
		$wave++;
		$db->query("UPDATE `wave_counter_<myname>` SET `wave` = '$wave'");
	}
	if($stime < $now - 10 * 120){
		$db->query("DELETE FROM wave_counter_<myname> WHERE `wave` = '$wave'");
	}
}else{
	while($row = $db->fObject()){
		$wave = $row->wave;
		$db->query("DELETE FROM wave_counter_<myname> WHERE `wave` = '$wave'");
	}
}

?>