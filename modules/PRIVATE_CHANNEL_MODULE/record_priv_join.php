<?php

if ($type == "joinPriv") {
	$data = $db->query("SELECT name FROM online WHERE `name` = '$sender' AND `channel_type` = 'priv' AND added_by = '<myname>'");
	if (count($data) == 0) {
	    $db->exec("INSERT INTO online (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$sender', '<myguild> Guests', 'priv', '<myname>', " . time() . ")");
	}
}

?>