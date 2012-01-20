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
if (!IRC::isConnectionActive($bbinSocket)) {
	return;
}

if ($data = fgets($bbinSocket)) {
	$ex = explode(' ', $data);
	LegacyLogger::log('DEBUG', "BBIN", trim($data));
	$channel = rtrim(strtolower($ex[2]));
	$nicka = explode('@', $ex[0]);
	$nickb = explode('!', $nicka[0]);
	$nickc = explode(':', $nickb[0]);

	$host = $nicka[1];
	$nick = $nickc[1];
	if ($ex[0] == "PING") {
		fputs($bbinSocket, "PONG ".$ex[1]."\n");
		LegacyLogger::log('DEBUG', "BBIN", "PING received. PONG sent.");
	} else if ($ex[1] == "NOTICE") {
		if (false != stripos($data, "exiting")) {
			// the irc server shut down (i guess)
			// send notification to channel
			$extendedinfo = Text::make_blob("Extended information", $data);
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->sendGuild("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo, true);
			}
			if ($chatBot->vars['my_guild'] == "" || $setting->get("guest_relay") == 1) {
				$chatBot->sendPrivate("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo, true);
			}
		}
	} else if ("KICK" == $ex[1]) {
		$extendedinfo = Text::make_blob("Extended information", $data);
		if ($ex[3] == $setting->get('bbin_nickname')) {
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->sendGuild("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo, true);
			}
			if ($chatBot->vars['my_guild'] == "" || $setting->get("guest_relay") == 1) {
				$chatBot->sendPrivate("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo, true);
			}
		} else {
			// yay someone else was kicked
			$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$ex[3]'");
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->sendGuild("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo, true);
			}
			if ($chatBot->vars['my_guild'] == "" || $setting->get("guest_relay") == 1) {
				$chatBot->sendPrivate("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo, true);
			}
		}
	} else if (($ex[1] == "QUIT") || ($ex[1] == "PART")) {
		$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$nick'");
		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->sendGuild("<yellow>[BBIN]<end> Lost uplink with $nick", true);
		}
		if ($chatBot->vars['my_guild'] == "" || $setting->get("guest_relay") == 1) {
			$chatBot->sendPrivate("<yellow>[BBIN]<end> Lost uplink with $nick", true);
		}
	} else if ($ex[1] == "JOIN") {
		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->sendGuild("<yellow>[BBIN]<end> Uplink established with $nick.", true);
		}
		if ($chatBot->vars['my_guild'] == "" || $setting->get("guest_relay") == 1) {
			$chatBot->sendPrivate("<yellow>[BBIN]<end> Uplink established with $nick.", true);
		}
	} else if ("PRIVMSG" == $ex[1] && $channel == trim(strtolower($setting->get('bbin_channel')))) {
		// tweak the third message a bit to remove beginning ":"
		$ex[3] = substr($ex[3],1,strlen($ex[3]));
		for ($i = 3; $i < count($ex); $i++) {
			$bbinmessage .= rtrim(htmlspecialchars_decode($ex[$i]))." ";
		}

		LegacyLogger::log_chat("Inc. BBIN Msg.", $nick, $bbinmessage);
		parse_incoming_bbin($bbinmessage, $nick);
	}
	unset($sandbox);
}
?>
