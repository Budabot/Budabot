<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
global $socket;
if ("1" == $chatBot->settings['irc_status']) {
	if ($type == "leavePriv") {
		flush();
		fputs($socket, "PRIVMSG ".$chatBot->settings['irc_channel']." :$sender has left the private chat.\n");
		if ($chatBot->settings['irc_debug_messages'] == 1) {
			Logger::log_chat("Out. IRC Msg.", -1, "$sender has left the channel");
		}
	} else if ($type == "logOff" && isset($chatBot->guildmembers[$sender])) {
		flush();
		fputs($socket, "PRIVMSG ".$chatBot->settings['irc_channel']." :$sender has logged off.\n");
		if ($chatBot->settings['irc_debug_messages'] == 1) {
			Logger::log_chat("Out. IRC Msg.", -1, "$sender has left the channel");
		}
	}
}

?>