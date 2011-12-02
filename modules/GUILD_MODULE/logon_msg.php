<?php

if (preg_match("/^logon$/i", $message)) {
	$data = $db->query("SELECT name, logon_msg FROM org_members_<myname> WHERE `name` = '{$sender}'");
	$row = $data[0];

	if ($row !== null) {
		if ($row->logon_msg == '') {
			$msg = "Your logon message has not been set.";
		} else {
			$msg = "{$sender} logon: {$row->logon_msg}";
		}
    } else {
        $msg = "You are not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^logon (.+)$/i", $message, $arr)) {
	$data = $db->query("SELECT name, logon_msg FROM org_members_<myname> WHERE `name` = '{$sender}'");
	$row = $data[0];

    if ($row !== null) {
        $logon_msg = str_replace("'", "''", $arr[1]);
		if ($logon_msg == 'clear') {
			$db->exec("UPDATE org_members_<myname> SET `logon_msg` = '' WHERE `name` = '{$sender}'");
            $msg = "Your logon message has been cleared.";
		} else if (strlen($logon_msg) <= Setting::get('max_logon_msg_size')) {
            $db->exec("UPDATE org_members_<myname> SET `logon_msg` = '{$logon_msg}' WHERE `name` = '{$sender}'");
            $msg = "Your logon message has been set.";
        } else {
            $msg = "Your logon message is too large. Your logon message may contain a maximum of " . Setting::get('max_logon_msg_size') . " characters.";
		}
    } else {
        $msg = "You are not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
