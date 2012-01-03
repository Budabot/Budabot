<?php

$ban = Registry::getInstance('ban');
if (preg_match("/^unban (.+)$/i", $message, $arr)){
	$who = ucfirst(strtolower($arr[1]));
	
	if (!$ban->is_banned($who)) {
		$chatBot->send("<highlight>$who<end> is not banned on this bot.", $sendto);
		return;
	}
		
	$ban->remove($who);

	$chatBot->send("You have unbanned <highlight>$who<end> from this bot.", $sendto);
	if ($setting->get('notify_banned_player') == 1) {
		$chatBot->send("You have been unbanned from this bot by $sender.", $who);
	}
} else if (preg_match("/^unbanorg (.+)$/i", $message, $arr)) {
	$who = ucwords(strtolower($arr[1]));
	
	if (!$ban->is_banned($who)) {
		$chatBot->send("The org <highlight>$who<end> is not banned on this bot.", $sender);
		return;		  
	}
		
	$ban->remove($who);

	$chatBot->send("You have unbanned the org <highlight>$who<end> from this bot.", $sendto);
} else {
	$syntax_error = true;
}

?>