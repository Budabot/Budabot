<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
if(preg_match("/^setirc server (.+)$/i", $message, $arr)) {
	Setting::save("irc_server", trim($arr[1]));
	bot::send("Setting saved.  Bot will connect to IRC server: {$arr[1]}.", $sender);
}
elseif(preg_match("/^setirc port (.+)$/i", $message, $arr)) {
	if(is_numeric($arr[1])) {
		Setting::save("irc_port", trim($arr[1]));
		bot::send("Setting saved.  Bot will use port {$arr[1]} to connect to the IRC server.", $sender);
	}
	else {
		bot::send("Please check again.  The port should be a number.", $sender);
	}
}
elseif(preg_match("/^setirc nickname (.+)$/i", $message, $arr)) {
	Setting::save("irc_nickname", trim($arr[1]));
	bot::send("Setting saved.  Bot will use {$arr[1]} as its nickname while in IRC.", $sender);
}
elseif(preg_match("/^setirc channel (.+)$/i", $message, $arr)) {
	if(strpos($arr[1]," ")) {
		bot::send("IRC channels cannot have spaces in them",$sender);
		$sandbox = explode(" ",$arr[1]);
		for ($i = 0; $i < count($sandbox); $i++) {
			$channel .= ucfirst(strtolower($sandbox[$i]));
		}
	}
	else {
		$channel = $arr[1];
	}
	if(strpos($channel,"#") !== 0) {
		$channel = "#".$channel;
	}
	Setting::save("irc_channel", trim($channel));
	bot::send("Setting saved.  Bot will join $channel when it connects to IRC.", $sender);
}
else {
	bot::send("<symbol>tell <myname> <symbol>help irc",$sender);
}
?>