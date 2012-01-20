<?php
/*
** Author: Mindrila (RK1)
** Credits: Legendadv (RK2)
** BUDABOT IRC NETWORK MODULE
** Version = 0.1
** Developed for: Budabot(http://budabot.com)
**
*/

if (preg_match("/^setbbin server (.+)$/i", $message, $arr)) {
	$setting->save("bbin_server", trim($arr[1]));
	$sendto->reply("Setting saved.  Bot will connect to IRC server: {$arr[1]}.");
} else if (preg_match("/^setbbin port (.+)$/i", $message, $arr)) {
	if (is_numeric($arr[1])) {
		$setting->save("bbin_port", trim($arr[1]));
		$sendto->reply("Setting saved.  Bot will use port {$arr[1]} to connect to the IRC server.");
	} else {
		$sendto->reply("Please check again.  The port should be a number.");
	}
} else if (preg_match("/^setbbin nickname (.+)$/i", $message, $arr)) {
	$setting->save("bbin_nickname", trim($arr[1]));
	$sendto->reply("Setting saved.  Bot will use {$arr[1]} as its nickname while in IRC.");
} else if (preg_match("/^setbbin channel (.+)$/i", $message, $arr)) {
	if (strpos($arr[1]," ")) {
		$sendto->reply("IRC channels cannot have spaces in them");
		$sandbox = explode(" ",$arr[1]);
		for ($i = 0; $i < count($sandbox); $i++) {
			$channel .= ucfirst(strtolower($sandbox[$i]));
		}
	} else {
		$channel = $arr[1];
	}
	if (strpos($channel,"#") !== 0) {
		$channel = "#".$channel;
	}
	$setting->save("bbin_channel", trim($channel));
	$sendto->reply("Setting saved.  Bot will join $channel when it connects to IRC.");
} else {
	$syntax_error = true;
}

?>