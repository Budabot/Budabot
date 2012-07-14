<?php

if (preg_match("/^inviteuser (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
	if ($chatBot->vars["name"] == $name) {
		$msg = "You cannot invite the bot to its own private channel.";
	} else if ($uid) {
	$msg = "Invited <highlight>$name<end> to this channel.";
		$chatBot->privategroup_kick($name);
		$chatBot->privategroup_invite($name);
		$msg2 = "You have been invited to the <highlight><myname><end> channel by <highlight>$sender<end>";
		$chatBot->sendTell($msg2, $name);
    } else {
		$msg = "Character <highlight>{$name}<end> does not exist.";
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}
?>
