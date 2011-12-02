<?php

if (preg_match("/^logoff$/i", $message)) {
	$data = $db->query("SELECT name, logoff_msg FROM org_members_<myname> WHERE `name` = '{$sender}'");
	$row = $data[0];

	if ($row !== null) {
		if ($row->logoff_msg == '') {
			$msg = "Your logoff message has not been set.";
		} else {
			$msg = "{$sender} logoff: {$row->logon_msg}";
		}
    } else {
        $msg = "You are not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^logoff (.+)$/i", $message, $arr)) {
	$data = $db->query("SELECT name, logoff_msg FROM org_members_<myname> WHERE `name` = '{$sender}'");
	$row = $data[0];

    if ($row !== null) {
        $logoff_msg = str_replace("'", "''", $arr[1]);
		if ($logoff_msg == 'clear') {
			$db->exec("UPDATE org_members_<myname> SET `logoff_msg` = '' WHERE `name` = '{$sender}'");
            $msg = "Your logoff message has been cleared.";
		} else if (strlen($logoff_msg) <= Setting::get('max_logoff_msg_size')) {
            $db->exec("UPDATE org_members_<myname> SET `logoff_msg` = '{$logoff_msg}' WHERE `name` = '{$sender}'");
            $msg = "Your logoff message has been set.";
        } else {
            $msg = "Your logoff message is too large. Your logoff message may contain a maximum of " . Setting::get('max_logoff_msg_size') . " characters.";
		}
    } else {
        $msg = "You are not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
