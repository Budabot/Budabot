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
	$msg = "[BBIN:LOGON:".$sender.",".$chatBot->vars["dimension"].",";
	
	if ($type == "joinPriv") {
		$msg .= "1]";
	} else {
		$msg .= "0]";
	}
	
	if ($type == "joinPriv") {
		fputs($bbin_socket, "PRIVMSG ".$chatBot->settings['bbin_channel']." :$msg\n");
		if ($chatBot->settings['bbin_debug_messages'] == 1) {
			Logger::log('debug', "BBIN Outgoing", $msg);
		}
	} else if (isset($chatBot->guildmembers[$charid])) {
		fputs($bbin_socket, "PRIVMSG ".$chatBot->settings['bbin_channel']." :$msg\n");
		if ($chatBot->settings['bbin_debug_messages'] == 1) {
			Logger::log('debug', "BBIN Outgoing", $msg);
		}
	}
}

?>
