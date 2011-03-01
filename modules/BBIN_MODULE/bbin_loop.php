<?php
/*
 ** Author: Mindrila (RK1)
 ** Credits: Legendadv (RK2)
 ** BUDABOT IRC NETWORK MODULE
 ** Version = 0.1
 ** Developed for: Budabot(http://budabot.com)
 **
 */

global $bbin_socket;
$db = DB::get_instance();
require_once("bbin_func.php");

stream_set_blocking($bbin_socket, 0);
if (($data = fgets($bbin_socket)) && ("1" == $chatBot->settings['bbin_status'])) {
	$ex = explode(' ', $data);
	if ($chatBot->settings['bbin_debug_all'] == 1) {
		Logger::log('debug', "BBIN", trim($data));
	}
	$channel = rtrim(strtolower($ex[2]));
	$nicka = explode('@', $ex[0]);
	$nickb = explode('!', $nicka[0]);
	$nickc = explode(':', $nickb[0]);

	$host = $nicka[1];
	$nick = $nickc[1];
	if ($ex[0] == "PING") {
		fputs($bbin_socket, "PONG ".$ex[1]."\n");
		if ($chatBot->settings['bbin_debug_ping'] == 1) {
			Logger::log('debug', "BBIN", "PING received. PONG sent.");
		}
	} else if ($ex[1] == "NOTICE") {
		if (false != stripos($data, "exiting")) {
			// the irc server shut down (i guess)
			// set bot to disconnected
			Setting::save("bbin_status","0");

			// send notification to channel
			$extendedinfo = Text::make_link("Extended informations",$data);
			if ($chatBot->vars['my guild'] != "") {
				$chatBot->send("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo,"guild",true);
			}
			if ($chatBot->vars['my guild'] == "" ||$chatBot->settings["guest_relay"] == 1) {
				$chatBot->send("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo,"priv",true);
			}
		}
	} else if ("KICK" == $ex[1]) {
		$extendedinfo = Text::make_link("Extended informations",$data);
		if ($ex[3] == $chatBot->settings['bbin_nickname']) {
			// oh noez, I was kicked !
			Setting::save("bbin_status", "0");
			if ($chatBot->vars['my guild'] != "") {
				$chatBot->send("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo,"guild",true);
			}
			if ($chatBot->vars['my guild'] == "" ||$chatBot->settings["guest_relay"] == 1) {
				$chatBot->send("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo,"priv",true);
			}
		} else {
			// yay someone else was kicked
			$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$ex[3]'");
			if ($chatBot->vars['my guild'] != "") {
				$chatBot->send("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo,"guild",true);
			}
			if ($chatBot->vars['my guild'] == "" ||$chatBot->settings["guest_relay"] == 1) {
				$chatBot->send("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo,"priv",true);
			}
		}
	} else if (($ex[1] == "QUIT") || ($ex[1] == "PART")) {
		$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$nick'");
		if ($chatBot->vars['my guild'] != "") {
			$chatBot->send("<yellow>[BBIN]<end> Lost uplink with $nick","guild",true);
		}
		if ($chatBot->vars['my guild'] == "" ||$chatBot->settings["guest_relay"] == 1) {
			$chatBot->send("<yellow>[BBIN]<end> Lost uplink with $nick","priv",true);
		}
	} else if ($ex[1] == "JOIN") {
		if ($chatBot->vars['my guild'] != "") {
			$chatBot->send("<yellow>[BBIN]<end> Uplink established with $nick.","guild",true);
		}
		if ($chatBot->vars['my guild'] == "" || $chatBot->settings["guest_relay"] == 1) {
			$chatBot->send("<yellow>[BBIN]<end> Uplink established with $nick.","priv",true);
		}
	} else if ($channel == trim(strtolower($chatBot->settings['bbin_channel']))) {
		// tweak the third message a bit to remove beginning ":"
		$ex[3] = substr($ex[3],1,strlen($ex[3]));
		for ($i = 3; $i < count($ex); $i++) {
			$bbinmessage .= rtrim(htmlspecialchars_decode($ex[$i]))." ";
		}
		// vhabot compatibility; vhabot sends ascii 02 and 03 chars in it's irc messages, this filters them out
		$bbinmessage = str_replace(chr(2), "", $bbinmessage);
		$bbinmessage = str_replace(chr(3), "", $bbinmessage);

		if ($chatBot->settings['bbin_debug_messages'] == 1) {
			Logger::log_chat("Inc. IRC Msg.", $nick, $bbinmessage);
		}
		parse_incoming_bbin($bbinmessage, $nick, $this);

		flush();
	}
	unset($sandbox);
}
?>
