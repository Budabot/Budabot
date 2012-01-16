<?php

if (preg_match("/^remuser (.+)$/i", $message, $arr)) {
	$uid = $chatBot->get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
    if (!$uid) {
        $msg = "Character <highlight>{$name}<end> does not exist.";
    } else {
	  	$data = $db->query("SELECT * FROM members_<myname> WHERE `name` = ?", $name);
	  	if (count($data) == 0) {
	  		$msg = "<highlight>$name<end> is not a member of this bot.";
	  	} else {
		    $db->exec("DELETE FROM members_<myname> WHERE `name` = ?", $name);
		    $msg = "<highlight>$name<end> has been removed as a member of this bot.";
			Buddylist::remove($name, 'member');
		}
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>