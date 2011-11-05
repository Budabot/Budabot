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
	if (Setting::get('irc_server') == "") {
		$chatBot->send("The IRC <highlight>server address<end> seems to be missing. <highlight>/tell <myname> <symbol>help irc<end> for details on setting this.", $sendto);
		return;
	}
	if (Setting::get('irc_port') == "") {
		$chatBot->send("The IRC <highlight>server port<end> seems to be missing. <highlight>/tell <myname> <symbol>help irc<end> for details on setting this.", $sendto);
		return;
	}

	$chatBot->send("Intializing IRC connection. Please wait...", $sendto);
	IRC::connect($ircSocket, Setting::get('irc_nickname'), Setting::get('irc_server'), Setting::get('irc_port'), Setting::get('irc_password'), Setting::get('irc_channel'));
	if (IRC::isConnectionActive($ircSocket)) {
		Setting::save("irc_status", "1");
		$chatBot->send("Finished connecting to IRC.", $sendto);
	} else {
		$chatBot->send("Error connecting to IRC.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>