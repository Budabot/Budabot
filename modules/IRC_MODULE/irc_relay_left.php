<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
global $socket;
if ("1" == Setting::get('irc_status')) {
	if ($type == "leavePriv") {
		flush();
		fputs($socket, "PRIVMSG ".Setting::get('irc_channel') . " :" . encodeGuildMessage($chatBot->vars['my_guild'], "$sender has left the private channel.") . "\n");
		if (Setting::get('irc_debug_messages') == 1) {
			Logger::log_chat("Out. IRC Msg.", -1, "$sender has left the channel");
		}
	} else if ($type == "logOff" && isset($chatBot->guildmembers[$sender])) {
		if (Setting::get('first_and_last_alt_only') == 1) {
			// if at least one alt/main is already online, don't show logon message
			$altInfo = Alts::get_alt_info($sender);
			if (count($altInfo->get_online_alts()) > 0) {
				return;
			}
		}
		
		flush();
		fputs($socket, "PRIVMSG ".Setting::get('irc_channel') . " :" . encodeGuildMessage($chatBot->vars['my_guild'], "$sender has logged off.") . "\n");
		if (Setting::get('irc_debug_messages') == 1) {
			Logger::log_chat("Out. IRC Msg.", -1, "$sender has left the channel");
		}
	}
}

?>