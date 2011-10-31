<?php
/*
** Author: Mindrila (RK1)
** Credits: Legendadv (RK2)
** BUDABOT IRC NETWORK MODULE
** Version = 0.1
** Developed for: Budabot(http://budabot.com)
**
*/

global $bbinSocket;
if (IRC::isConnectionActive($bbinSocket)) {
	// do not relay commands and ignored chars
	if ($args[2][0] != Setting::get("symbol")) {
		$outmsg = htmlspecialchars($message);
		
		$msg = "$sender: $message";
		Logger::log_chat("Out. BBIN Msg.", $sender, $msg);
		IRC::send($bbinSocket, Setting::get('bbin_channel'), $msg);
	}
}
?>