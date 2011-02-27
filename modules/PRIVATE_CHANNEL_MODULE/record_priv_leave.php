<?php

if ($type == "leavePriv") {
	$db->exec("DELETE FROM online WHERE `charid` = '$charid' AND `channel_type` = 'priv' AND added_by = '<myname>'");
}

?>