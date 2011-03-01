<?php
	 /*
   ** Author: Mindrila (RK1)
   ** Credits: Legendadv (RK2)
   ** BUDABOT IRC NETWORK MODULE
   ** Version = 0.1
   ** Developed for: Budabot(http://budabot.com)
   **
   */
   
global $bbin_socket;
if ("1" == $chatBot->settings['bbin_status']) {
	// do not relay commands and ignored chars
	if ($args[2][0] != $chatBot->settings["symbol"] && !$chatBot->settings["Ignore"][$sender]) {
		
		$outmsg = htmlspecialchars($message);
		
		fputs($bbin_socket, "PRIVMSG ".$chatBot->settings['bbin_channel']." :$sender: $message\n");
		if ($chatBot->settings['bbin_debug_messages'] == 1) {
			Logger::log_chat("Out. BBIN Msg.", $sender, $message);
		}
	}
}
?>