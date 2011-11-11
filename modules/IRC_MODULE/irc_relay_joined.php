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
		$whois->name = $sender;
	}
	$msg = Player::get_info($whois);
	
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

	$sql = "SELECT logon_msg FROM org_members_<myname> WHERE name = '{$sender}'";
	$db->query($sql);
	$row = $db->fObject();
	if ($row !== null && $row->logon_msg != '') {
		$msg .= " - " . $row->logon_msg;
	}
	
	$msg = Text::format_message($msg);
	
	if ($type == "joinPriv") {
		IRC::send($ircSocket, Setting::get('irc_channel'), encodeGuildMessage($chatBot->vars['my_guild'], $msg));
	} else if ($type == "logOn" && isset($chatBot->guildmembers[$sender])) {
		if (Setting::get('first_and_last_alt_only') == 1) {
			// if at least one alt/main is still online, don't show logoff message
			$altInfo = Alts::get_alt_info($sender);
			if (count($altInfo->get_online_alts()) > 1) {
				return;
			}
		}

		IRC::send($ircSocket, Setting::get('irc_channel'), encodeGuildMessage($chatBot->vars['my_guild'], $msg));
	}
	Logger::log_chat("Out. IRC Msg.", -1, $msg);
}

?>