<?php

if ($type == "joinpriv") {
	$data = $db->query("SELECT name FROM online WHERE `name` = ? AND `channel_type` = 'priv' AND added_by = '<myname>'", $sender);
	if (count($data) == 0) {
	    $db->exec("INSERT INTO online (`name`, `channel`, `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild> Guests', 'priv', '<myname>', ?)", $sender, time());
	}
}

?>