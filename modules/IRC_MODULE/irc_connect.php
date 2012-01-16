<?php
/*
** Author: Legendadv (RK2)
** IRC RELAY MODULE
**
** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
**
*/

global $ircSocket;
if (preg_match("/^startirc$/i", $message)) {
	if ($setting->get('irc_server') == "") {
		$sendto->reply("The IRC <highlight>server address<end> seems to be missing. <highlight>/tell <myname> <symbol>help irc<end> for details on setting this.");
		return;
	}
	if ($setting->get('irc_port') == "") {
		$sendto->reply("The IRC <highlight>server port<end> seems to be missing. <highlight>/tell <myname> <symbol>help irc<end> for details on setting this.");
		return;
	}

	$sendto->reply("Intializing IRC connection. Please wait...");
	IRC::connect($ircSocket, $setting->get('irc_nickname'), $setting->get('irc_server'), $setting->get('irc_port'), $setting->get('irc_password'), $setting->get('irc_channel'));
	if (IRC::isConnectionActive($ircSocket)) {
		$setting->save("irc_status", "1");
		$sendto->reply("Finished connecting to IRC.");
	} else {
		$sendto->reply("Error connecting to IRC.");
	}
} else {
	$syntax_error = true;
}

?>