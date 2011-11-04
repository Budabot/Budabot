<?php

if (preg_match("/^adduser (.+)$/i", $message, $arr)) {
	$uid = $chatBot->get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	if (!$uid) {
		$msg = "Player <highlight>$name<end> does not exist.";
	} else {
		$db->query("SELECT * FROM members_<myname> WHERE `name` = '$name'");
		if ($db->numrows() != 0) {
			$msg = "<highlight>$name<end> is already a member of this bot.";
		} else {
			$db->exec("INSERT INTO members_<myname> (`name`, `autoinv`) VALUES ('$name', 1)");
			$msg = "<highlight>$name<end> has been added as a member of this bot.";
		}

		// always add in case 
		Buddylist::add($name, 'member');
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>