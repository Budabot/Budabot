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
		$leaveType = "1";
	} else if ($type == "logOff" && isset($chatBot->guildmembers[$sender])) {
		$leaveType = "0";
	}
	
	$msg = "[BBIN:LOGOFF:".$sender.",".$chatBot->vars["dimension"].",{$leaveType}]";
	
	IRC::send($bbinSocket, Setting::get('bbin_channel'), $msg);
	Logger::log('debug', "BBIN Outgoing", $msg);
}

?>