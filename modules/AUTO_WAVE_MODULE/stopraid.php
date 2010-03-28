<?php
$db->query("SELECT * FROM wave_counter_<myname>");
while($row = $db->fObject()){
	$wave = $row->wave;
	$db->query("DELETE FROM wave_counter_<myname> WHERE `wave` = '$wave'");
	bot::send("Raid stopped.", "guild");
}
?>