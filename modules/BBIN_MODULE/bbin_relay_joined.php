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
	if ($type == "joinPriv") {
		$leaveType = "1";
	} else if ($type == 'logOn' && isset($chatBot->guildmembers[$sender])) {
		$leaveType = "0";
	}
	
	$msg = "[BBIN:LOGON:".$sender.",".$chatBot->vars["dimension"].",{$leaveType}]";
	
	Logger::log('DEBUG', "BBIN Outgoing", $msg);
	IRC::send($bbinSocket, Setting::get('bbin_channel'), $msg);
}

?>
