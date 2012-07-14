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
	if ($type == "joinpriv") {
		$msg = "[BBIN:LOGON:".$sender.",".$chatBot->vars["dimension"].",1]";
		LegacyLogger::log('DEBUG', "BBIN Outgoing", $msg);
		IRC::send($bbinSocket, $setting->get('bbin_channel'), $msg);
	} else if ($type == "logon" && isset($chatBot->guildmembers[$sender])) {
		$msg = "[BBIN:LOGON:".$sender.",".$chatBot->vars["dimension"].",0]";
		LegacyLogger::log('DEBUG', "BBIN Outgoing", $msg);
		IRC::send($bbinSocket, $setting->get('bbin_channel'), $msg);
	}
}

?>
