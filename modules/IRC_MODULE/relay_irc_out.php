<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
global $socket;
if ("1" == $chatBot->settings['irc_status']) {
	if ($args[2][0] != $chatBot->settings["symbol"] && !$chatBot->settings["Ignore"][$sender]) {
		
		$patterns = array(
		  '/<a href="itemref:\/\/(\d+)\/\1\/(\d+)">([^<]+)<\/a>/',
		  '/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/',
		);

		$replaces = array(
		  '\3 (http://auno.org/ao/db.php?id=\1)',
		  '\4 (http://auno.org/ao/db.php?id=\1&ql=\3)',
		);

		$msg = htmlspecialchars_decode(preg_replace($patterns, $replaces, $message));

		fputs($socket, "PRIVMSG ".$chatBot->settings['irc_channel']." :$sender: $msg\n");
		if ($chatBot->settings['irc_debug_messages'] == 1) {
			Logger::log_chat("Out. IRC Msg.", $sender, $msg);
		}
	}
}
?>