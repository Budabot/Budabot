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
if (preg_match("/^startbbin$/i", $message)) {
	if (Setting::get('bbin_server') == "") {
		$chatBot->send("The BBIN <highlight>server address<end> seems to be missing. <highlight>/tell <myname> <symbol>help bbin<end> for details on setting this.", $sendto);
		return;
	}
	if (Setting::get('bbin_port') == "") {
		$chatBot->send("The BBIN <highlight>server port<end> seems to be missing. <highlight>/tell <myname> <symbol>help bbin<end> for details on setting this.", $sendto);
		return;
	}

	$chatBot->send("Intializing BBIN connection. Please wait...", $sendto);
	if (bbinConnect()) {
		$chatBot->send("Finished connecting to BBIN.", $sendto);
	} else {
		$chatBot->send("Error connectiong to BBIN.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>
