<?php

if ($type == "joinPriv") {
	$db->query("SELECT name FROM online WHERE `name` = '$sender' AND `channel_type` = 'priv' AND added_by = '<myname>'");
	if ($db->numrows() == 0) {
	    $db->exec("INSERT INTO online (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$sender', '{$chatBot->vars['my guild']}', 'priv', '<myname>', " . time() . ")");
	}
}

?>