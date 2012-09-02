<?php
/*
** Author: Legendadv (RK2)
** IRC RELAY MODULE
**
** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
**
*/

global $ircSocket;
if (IRC::isConnectionActive($ircSocket)) {
	if ($type == "leavepriv") {
		IRC::send($ircSocket, $setting->get('irc_channel'), encodeGuildMessage(getGuildAbbreviation(), "$sender has left the private channel."));
		LegacyLogger::log_chat("Out. IRC Msg.", -1, "$sender has left the channel");
	} else if ($type == "logoff" && isset($chatBot->guildmembers[$sender])) {
		if ($setting->get('first_and_last_alt_only') == 1) {
			$alts = Registry::getInstance('alts');
			// if at least one alt/main is already online, don't show logon message
			$altInfo = $alts->get_alt_info($sender);
			if (count($altInfo->get_online_alts()) > 0) {
				return;
			}
		}

		IRC::send($ircSocket, $setting->get('irc_channel'), encodeGuildMessage(getGuildAbbreviation(), "$sender has logged off."));
		LegacyLogger::log_chat("Out. IRC Msg.", -1, "$sender has left the channel");
	}
}

?>
