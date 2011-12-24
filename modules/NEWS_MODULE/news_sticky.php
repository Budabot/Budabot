<?php
 
if (preg_match("/^news sticky ([0-9]+)$/i", $message, $arr)) {
	$id = $arr[1];
	
	$row = $db->queryRow("SELECT * FROM `#__news` WHERE `id` = ?", $id);
       
	if ($row->sticky == 1) {
		$msg = "News ID $id is already stickied.";
	} else {
		$db->exec("UPDATE `#__news` SET `sticky` = 1 WHERE `id` = ?", $id);
		$msg = "News ID $id successfully stickied.";
	}
	$chatBot->send($msg, $sendto);
 
} else if (preg_match("/^news unsticky ([0-9]+)$/i", $message, $arr)) {
	$id = $arr[1];
       
	$row = $db->queryRow("SELECT * FROM `#__news` WHERE `id` = ?", $id);

	if ($row->sticky == 0) {
		$msg = "News ID $id is not stickied.";
	} else if ($row->sticky == 1) {
		$db->exec("UPDATE `#__news` SET `sticky` = 0 WHERE `id` = ?", $id);
		$msg = "News ID $id successfully unstickied.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
 
?>