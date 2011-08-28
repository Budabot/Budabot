<?php

if (preg_match("/^logonadmin ([a-zA-Z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	
	$db->query("SELECT name, logon_msg FROM org_members_<myname> WHERE `name` = '{$name}'");
	$row = $db->fObject();

	if ($row !== null) {
		if ($row->logon_msg == '') {
			$msg = "The logon message for $name has not been set.";
		} else {
			$msg = "{$name} logon: {$row->logon_msg}";
		}
    } else {
        $msg = "$name is not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^logonadmin ([a-zA-Z0-9-]+) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	
	$db->query("SELECT name, logon_msg FROM org_members_<myname> WHERE `name` = '{$name}'");
	$row = $db->fObject();

    if ($row !== null) {
        $logon_msg = str_replace("'", "''", $arr[2]);
		if ($logon_msg == 'clear') {
			$db->exec("UPDATE org_members_<myname> SET `logon_msg` = '' WHERE `name` = '{$name}'");
            $msg = "The logon message for $name has been cleared.";
		} else if (strlen($logon_msg) <= Setting::get('max_logon_msg_size')) {
            $db->exec("UPDATE org_members_<myname> SET `logon_msg` = '{$logon_msg}' WHERE `name` = '{$name}'");
            $msg = "The logon message for $name has been set.";
        } else {
            $msg = "The logon message is too large. The logon message may contain a maximum of " . Setting::get('max_logon_msg_size') . " characters.";
		}
    } else {
        $msg = "$name is not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
