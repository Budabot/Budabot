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
		$chatBot->send("The IRC <highlight>server address<end> seems to be missing. <highlight>/tell <myname> <symbol>help irc<end> for details on setting this.", $sendto);
		return;
	}
	if ($setting->get('irc_port') == "") {
		$chatBot->send("The IRC <highlight>server port<end> seems to be missing. <highlight>/tell <myname> <symbol>help irc<end> for details on setting this.", $sendto);
		return;
	}

	$chatBot->send("Intializing IRC connection. Please wait...", $sendto);
	IRC::connect($ircSocket, $setting->get('irc_nickname'), $setting->get('irc_server'), $setting->get('irc_port'), $setting->get('irc_password'), $setting->get('irc_channel'));
	if (IRC::isConnectionActive($ircSocket)) {
		$setting->save("irc_status", "1");
		$chatBot->send("Finished connecting to IRC.", $sendto);
	} else {
		$chatBot->send("Error connecting to IRC.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>