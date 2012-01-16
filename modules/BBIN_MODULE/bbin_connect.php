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
	if ($setting->get('bbin_server') == "") {
		$sendto->reply("The BBIN <highlight>server address<end> seems to be missing. <highlight>/tell <myname> <symbol>help bbin<end> for details on setting this.");
		return;
	}
	if ($setting->get('bbin_port') == "") {
		$sendto->reply("The BBIN <highlight>server port<end> seems to be missing. <highlight>/tell <myname> <symbol>help bbin<end> for details on setting this.");
		return;
	}

	$sendto->reply("Intializing BBIN connection. Please wait...");
	if (bbinConnect()) {
		$sendto->reply("Finished connecting to BBIN.");
	} else {
		$sendto->reply("Error connectiong to BBIN.");
	}
} else {
	$syntax_error = true;
}

?>
