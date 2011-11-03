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
	Logger::log('DEBUG', "BBIN", trim($data));
	$channel = rtrim(strtolower($ex[2]));
	$nicka = explode('@', $ex[0]);
	$nickb = explode('!', $nicka[0]);
	$nickc = explode(':', $nickb[0]);

	$host = $nicka[1];
	$nick = $nickc[1];
	if ($ex[0] == "PING") {
		fputs($bbinSocket, "PONG ".$ex[1]."\n");
		Logger::log('DEBUG', "BBIN", "PING received. PONG sent.");
	} else if ($ex[1] == "NOTICE") {
		if (false != stripos($data, "exiting")) {
			// the irc server shut down (i guess)
			// send notification to channel
			$extendedinfo = Text::make_blob("Extended information", $data);
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo, "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
				$chatBot->send("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo, "priv", true);
			}
		}
	} else if ("KICK" == $ex[1]) {
		$extendedinfo = Text::make_blob("Extended information", $data);
		if ($ex[3] == Setting::get('bbin_nickname')) {
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo, "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
				$chatBot->send("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo, "priv", true);
			}
		} else {
			// yay someone else was kicked
			$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$ex[3]'");
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo, "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
				$chatBot->send("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo, "priv", true);
			}
		}
	} else if (($ex[1] == "QUIT") || ($ex[1] == "PART")) {
		$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$nick'");
		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->send("<yellow>[BBIN]<end> Lost uplink with $nick", "guild", true);
		}
		if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
			$chatBot->send("<yellow>[BBIN]<end> Lost uplink with $nick", "priv", true);
		}
	} else if ($ex[1] == "JOIN") {
		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->send("<yellow>[BBIN]<end> Uplink established with $nick.", "guild", true);
		}
		if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
			$chatBot->send("<yellow>[BBIN]<end> Uplink established with $nick.", "priv", true);
		}
	} else if ("PRIVMSG" == $ex[1] && $channel == trim(strtolower(Setting::get('bbin_channel')))) {
		// tweak the third message a bit to remove beginning ":"
		$ex[3] = substr($ex[3],1,strlen($ex[3]));
		for ($i = 3; $i < count($ex); $i++) {
			$bbinmessage .= rtrim(htmlspecialchars_decode($ex[$i]))." ";
		}
		// vhabot compatibility; vhabot sends ascii 02 and 03 chars in it's irc messages, this filters them out
		$bbinmessage = str_replace(chr(2), "", $bbinmessage);
		$bbinmessage = str_replace(chr(3), "", $bbinmessage);

		Logger::log_chat("Inc. BBIN Msg.", $nick, $bbinmessage);
		parse_incoming_bbin($bbinmessage, $nick);

		flush();
	}
	unset($sandbox);
}
?>
