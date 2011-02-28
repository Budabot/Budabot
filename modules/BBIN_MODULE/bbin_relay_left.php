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
if ("1" == Setting::get('bbin_status')) {
	$msg = "[BBIN:LOGOFF:".$sender.",".$chatBot->vars["dimension"].",";
	
	if ($type == "joinPriv") {
		$msg .= "1]";
	} else {
		$msg .= "0]";
	}
	
	if ($type == "leavePriv") {
		flush();
		fputs($bbin_socket, "PRIVMSG ".Setting::get('bbin_channel')." :$msg\n");
		if (Setting::get('bbin_debug_messages') == 1) {
			Logger::log('debug', "BBIN Outgoing", $msg);
		}
	} else if ($type == "logOff" && isset($chatBot->guildmembers[$charid])) {
		flush();
		fputs($bbin_socket, "PRIVMSG ".Setting::get('bbin_channel')." :$msg\n");
		if (Setting::get('bbin_debug_messages') == 1) {
			Logger::log('debug', "BBIN Outgoing", $msg);
		}
	}
}

?>