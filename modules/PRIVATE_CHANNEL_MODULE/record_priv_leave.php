<?php

if ($type == "leavePriv") {
	$db->exec("DELETE FROM online WHERE `name` = '$sender' AND `channel_type` = 'priv' AND added_by = '<myname>'");
}

?>