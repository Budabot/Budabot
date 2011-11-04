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
	$whois = Player::get_by_name($sender);
	if ($whois === null) {
		$whois = new stdClass;
	}
	if ($whois->guild == "") {
		$whois->guild = "Not in a guild";
	}
	$msg = "$sender ({$whois->level}/{$whois->ai_level}, {$whois->profession}, {$whois->guild})";
	
	if ($type == "joinPriv") {
		$msg .= " has joined the private channel.";
	} else {
		$msg .= " has logged on.";
	}

	// Alternative Characters Part
	$altInfo = Alts::get_alt_info($sender);
	if ($altInfo->main != $sender) {
		$msg .= " Alt of {$altInfo->main}";
	}

	if (($row->logon_msg != '') && ($row->logon_msg != '0')) {
		$msg .= " - " . $row->logon_msg;
	}
	
	if ($type == "joinPriv") {
		IRC::send($ircSocket, Setting::get('irc_channel'), encodeGuildMessage($chatBot->vars['my_guild'], $msg));
		Logger::log_chat("Out. IRC Msg.", -1, "$sender has joined the private chat");
	} else if ($type == "logOn" && isset($chatBot->guildmembers[$sender])) {
		if (Setting::get('first_and_last_alt_only') == 1) {
			// if at least one alt/main is still online, don't show logoff message
			$altInfo = Alts::get_alt_info($sender);
			if (count($altInfo->get_online_alts()) > 1) {
				return;
			}
		}

		IRC::send($ircSocket, Setting::get('irc_channel'), encodeGuildMessage($chatBot->vars['my_guild'], $msg));
		Logger::log_chat("Out. IRC Msg.", -1, "$sender has logged on");
	}
}

?>