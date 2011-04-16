<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
global $socket;
if ("1" == Setting::get('irc_status')) {
	if ($args[2][0] != Setting::get("symbol")) {
		
		$patterns = array(
		  '/<a href="itemref:\/\/(\d+)\/\1\/(\d+)">([^<]+)<\/a>/',
		  '/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/',
		);

		$replaces = array(
		  '\3 (http://auno.org/ao/db.php?id=\1)',
		  '\4 (http://auno.org/ao/db.php?id=\1&ql=\3)',
		);

		$message = htmlspecialchars_decode(preg_replace($patterns, $replaces, $message));

		fputs($socket, "PRIVMSG ".Setting::get('irc_channel')." :[{$chatBot->vars['my_guild']}] $sender: $message\n");
		if (Setting::get('irc_debug_messages') == 1) {
			Logger::log_chat("Out. IRC Msg.", $sender, $message);
		}
	}
}
?>