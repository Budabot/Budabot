<?php

if (preg_match("/^autoinvite (on|off)$/i", $message, $arr)) {
	$onOrOff = 0;
	if ($arr[1] == 'on') {
		$onOrOff = 1;
		$buddylistManager->add($sender, 'member');
	} else {
		$buddylistManager->remove($sender, 'member');
	}

	$data = $db->query("SELECT * FROM members_<myname> WHERE `name` = ?", $sender);
	if (count($data) == 0) {
		$msg = "You are not a member of this bot.";
	} else {
		$db->exec("UPDATE members_<myname> SET autoinv = ? WHERE name = ?", $onOrOff, $sender);
		$msg = "Your auto invite preference has been updated.";
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
