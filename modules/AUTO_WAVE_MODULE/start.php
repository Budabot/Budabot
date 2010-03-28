<?php
if(eregi("^Your city in (.+) has been targeted by hostile forces.$", $message, $arr)){
	$db->query("SELECT * FROM wave_counter_<myname>");
	if($db->numrows() != 0){
		while($row = $db->fObject()){
			$wave = $row->wave;
			$db->query("DELETE FROM wave_counter_<myname> WHERE `wave` = '$wave'");
		}
	}
	$wtf = 1;
	bot::send("Wave counter started.", "guild");
	$db->query("INSERT INTO `wave_counter_<myname>` (`time`, `wave`) VALUES ('".time()."', '".$wtf."')");
}elseif(eregi("^startraid", $message)){
	$db->query("SELECT * FROM wave_counter_<myname>");
	if($db->numrows() != 0){
		bot::send("A raid is already in progress.", "guild");
	}else{
		$wtf = 1;
		bot::send("Wave counter started.", "guild");
		$db->query("INSERT INTO `wave_counter_<myname>` (`time`, `wave`) VALUES ('".time()."', '".$wtf."')");
	}
}
?>