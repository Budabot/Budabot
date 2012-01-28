<?php

if (preg_match("/^notify (on|add) (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[2]);
	$name = ucfirst(strtolower($arr[2]));

	if (!$uid) {
		$msg = "<highlight>{$name}<end> does not exist.";
		$sendto->reply($msg);
		return;
	}
	
	$row = $db->queryRow("SELECT mode FROM org_members_<myname> WHERE `name` = ?", $name);

	if ($row !== null && $row->mode != "del") {
        $msg = "<highlight>{$name}<end> is already on the Notify list.";
    } else {
		if ($row === null) {
			$db->exec("INSERT INTO org_members_<myname> (`name`, `mode`) VALUES (?, 'add')", $name);
		} else {
			$db->exec("UPDATE org_members_<myname> SET `mode` = 'add' WHERE `name` = ?", $name);
		}
		$db->exec("INSERT INTO online (`name`, `channel`, `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild>', 'guild', '<myname>', ?)", $name, time());
        $buddylistManager->add($name, 'org');
    	$chatBot->guildmembers[$name] = 6;
    	$msg = "<highlight>{$name}<end> has been added to the Notify list.";
    }

	$sendto->reply($msg);
} else if (preg_match("/^notify (off|rem) (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[2]);
	$name = ucfirst(strtolower($arr[2]));

	if (!$uid) {
		$msg = "<highlight>{$name}<end> does not exist.";
		$sendto->reply($msg);
		return;
	}

    $row = $db->queryRow("SELECT mode FROM org_members_<myname> WHERE `name` = ?", $name);
	
	if ($row === null) {
		$msg = "<highlight>{$name}<end> is not on the guild roster.";
	} else if ($row->mode == "del") {
		$msg = "<highlight>{$name}<end> has already been removed from the Notify list.";
	} else {
        $db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = ?", $name);
        $db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $name);
		$buddylistManager->remove($name, 'org');
		unset($chatBot->guildmembers[$name]);
        $msg = "Removed <highlight>{$name}<end> from the Notify list.";
    }

    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
