<?php

$ban = Registry::getInstance('ban');
if (preg_match("/^unban (.+)$/i", $message, $arr)){
	$who = ucfirst(strtolower($arr[1]));

	if (!$ban->is_banned($who)) {
		$sendto->reply("<highlight>$who<end> is not banned on this bot.");
		return;
	}

	$ban->remove($who);

	$sendto->reply("You have unbanned <highlight>$who<end> from this bot.");
	if ($setting->get('notify_banned_player') == 1) {
		$chatBot->sendTell("You have been unbanned from this bot by $sender.", $who);
	}
} else if (preg_match("/^unbanorg (.+)$/i", $message, $arr)) {
	$who = ucwords(strtolower($arr[1]));

	if (!$ban->is_banned($who)) {
		$sendto->reply("The org <highlight>$who<end> is not banned on this bot.");
		return;
	}

	$ban->remove($who);

	$sendto->reply("You have unbanned the org <highlight>$who<end> from this bot.");
} else {
	$syntax_error = true;
}

?>
