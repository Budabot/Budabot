<?php

if ($type == "leavePriv") {
	$db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'priv' AND added_by = '<myname>'", $sender);
}

?>