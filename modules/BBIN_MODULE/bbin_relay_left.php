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
	if ($type == "leavePriv") {
		$msg = "[BBIN:LOGOFF:".$sender.",".$chatBot->vars["dimension"].",1]";
		Logger::log('DEBUG', "BBIN Outgoing", $msg);
		IRC::send($bbinSocket, Setting::get('bbin_channel'), $msg);
	} else if ($type == "logOff" && isset($chatBot->guildmembers[$sender])) {
		$msg = "[BBIN:LOGOFF:".$sender.",".$chatBot->vars["dimension"].",0]";
		Logger::log('DEBUG', "BBIN Outgoing", $msg);
		IRC::send($bbinSocket, Setting::get('bbin_channel'), $msg);
	}
}

?>