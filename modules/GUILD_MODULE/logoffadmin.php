<?php

if (preg_match("/^logoffadmin ([a-zA-Z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	
	$data = $db->query("SELECT name, logoff_msg FROM org_members_<myname> WHERE `name` = '{$name}'");
	$row = $data[0];

	if ($row !== null) {
		if ($row->logoff_msg == '') {
			$msg = "The logoff message for $name has not been set.";
		} else {
			$msg = "{$name} logoff: {$row->logoff_msg}";
		}
    } else {
        $msg = "$name is not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^logoffadmin ([a-zA-Z0-9-]+) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	
	$data = $db->query("SELECT name, logoff_msg FROM org_members_<myname> WHERE `name` = '{$name}'");
	$row = $data[0];

    if ($row !== null) {
        $logoff_msg = str_replace("'", "''", $arr[2]);
		if ($logoff_msg == 'clear') {
			$db->exec("UPDATE org_members_<myname> SET `logoff_msg` = '' WHERE `name` = '{$name}'");
            $msg = "The logoff message for $name has been cleared.";
		} else if (strlen($logoff_msg) <= Setting::get('max_logoff_msg_size')) {
            $db->exec("UPDATE org_members_<myname> SET `logoff_msg` = '{$logoff_msg}' WHERE `name` = '{$name}'");
            $msg = "The logoff message for $name has been set.";
        } else {
            $msg = "The logoff message is too large. The logoff message may contain a maximum of " . Setting::get('max_logoff_msg_size') . " characters.";
		}
    } else {
        $msg = "$name is not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
