<?php
/*
** Author: Legendadv (RK2)
** IRC RELAY MODULE
**
** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
**
*/

if (!function_exists('getIRCPlayerInfo')) {
	function getIRCPlayerInfo($sender) {
		$chatBot = Registry::getInstance('chatBot');
		$db = Registry::getInstance('db');
	
		$whois = Player::get_by_name($sender);
		if ($whois === null) {
			$whois = new stdClass;
			$whois->name = $sender;
		}
		
		$msg = '';
			
		if ($whois->firstname) {
			$msg = $whois->firstname." ";
		}

		$msg .= "\"{$whois->name}\" ";

		if ($whois->lastname) {
			$msg .= $whois->lastname." ";
		}

		$msg .= "({$whois->level}/{$whois->ai_level}";
		$msg .= ", {$whois->gender} {$whois->breed} {$whois->profession}";
		$msg .= ", $whois->faction";

		if ($whois->guild) {
			$msg .= ", {$whois->guild_rank} of {$whois->guild})";
		} else {
			$msg .= ", Not in a guild)";
		}
		
		if ($type == "joinpriv") {
			$msg .= " has joined the private channel.";
		} else {
			$msg .= " has logged on.";
		}

		// Alternative Characters Part
		$altInfo = Alts::get_alt_info($sender);
		if ($altInfo->main != $sender) {
			$msg .= " Alt of {$altInfo->main}";
		}

		$logon_msg = Preferences::get($sender, 'logon_msg');
		if ($logon_msg !== false && $logon_msg != '') {
			$msg .= " - " . $logon_msg;
		}
		
		return $msg;
	}
}

global $ircSocket;
if (IRC::isConnectionActive($ircSocket)) {
	if ($type == "joinpriv") {
		$msg = getIRCPlayerInfo($sender);
		Logger::log_chat("Out. IRC Msg.", -1, $msg);
		IRC::send($ircSocket, $setting->get('irc_channel'), encodeGuildMessage(getGuildAbbreviation(), $msg));
	} else if ($type == "logon" && isset($chatBot->guildmembers[$sender])) {
		if ($setting->get('first_and_last_alt_only') == 1) {
			// if at least one alt/main is still online, don't show logoff message
			$altInfo = Alts::get_alt_info($sender);
			if (count($altInfo->get_online_alts()) > 1) {
				return;
			}
		}
		
		$msg = getIRCPlayerInfo($sender);
		Logger::log_chat("Out. IRC Msg.", -1, $msg);
		IRC::send($ircSocket, $setting->get('irc_channel'), encodeGuildMessage(getGuildAbbreviation(), $msg));
	}
}

?>