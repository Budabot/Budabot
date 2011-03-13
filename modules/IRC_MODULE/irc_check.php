<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */

global $socket;

stream_set_blocking($socket, 0);
if (($data = fgets($socket)) && ("1" == $chatBot->settings['irc_status'])) {
	$ex = explode(' ', $data);
	$ex[3] = substr($ex[3],1,strlen($ex[3]));
	$rawcmd = rtrim(htmlspecialchars($ex[3]));
	
	$channel = rtrim(strtolower($ex[2]));
	$nicka = explode('@', $ex[0]);
	$nickb = explode('!', $nicka[0]);
	$nickc = explode(':', $nickb[0]);
	if ($chatBot->settings['irc_debug_all'] == 1) {
		Logger::log('info', "IRC", trim($data));
	}
	$host = $nicka[1];
	$nick = $nickc[1];
	if ($ex[0] == "PING"){
		fputs($socket, "PONG ".$ex[1]."\n");
		if ($chatBot->settings['irc_debug_ping'] == 1) {
			Logger::log('info', "IRC", "PING received. PONG sent");
		}
	} else if($ex[1] == "QUIT") {
		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->send("<yellow>[IRC]<end><green> $nick quit IRC.<end>","guild",true);
		}
		if ($chatBot->vars['my_guild'] == "" ||$chatBot->settings["guest_relay"] == 1) {
			$chatBot->send("<yellow>[IRC]<end><white> $nick quit IRC.<end>","priv",true);
		}
	} else if ($channel == trim(strtolower($chatBot->settings['irc_channel']))) {
		$args = NULL; for ($i = 4; $i < count($ex); $i++) { $args .= rtrim(htmlspecialchars($ex[$i])) . ' '; }
		for ($i = 3; $i < count($ex); $i++) {
			$ircmessage .= rtrim(htmlspecialchars($ex[$i]))." ";
		}
		// vhabot compatibility; vhabot sends ascii 02 and 03 chars in it's irc messages, this filters them out
		$ircmessage = str_replace(chr(2), "", $ircmessage);
		$ircmessage = str_replace(chr(3), "", $ircmessage);

		if ($rawcmd == "!sayit") {
			fputs($socket, "PRIVMSG ".$channel." :".$args." \n");
		} else if ($rawcmd == "!md5") {
			fputs($socket, "PRIVMSG ".$channel." :MD5 ".md5($args)."\n");
		} else if ($rawcmd == "!online") {
			$numonline = 0;
			$numguest = 0;
			//guild listing
			if ($chatBot->vars['my_guild'] != "") {
				$db->query("SELECT * FROM online WHERE channel_type = 'guild'");
				$numonline = $db->numrows();
				if ($numonline != 0) {
					$data = $db->fObject("all");
					forEach ($data as $row) {
						if ($row->afk == "kiting") {
							$afk = " KITING";
						} else if ($row->afk != "") {
							$afk = " AFK";
						} else {
							$afk = "";
						}
						$db->query("SELECT * FROM alts WHERE `alt` = '$row->name'");
						if ($db->numrows() == 0) {
							$alt = "";
						} else {
							$row1 = $db->fObject();
							$alt = " ($row1->main)";
						}
						$list .= "$row->name"."$alt"."$afk, ";
						$g++;
					}
				}
			}
			//priv listing
			$db->query("SELECT * FROM online WHERE channel_type = 'priv'");
			$numguest = $db->numrows();
			if ($db->numrows() != 0) {
				$data = $db->fObject("all");
				forEach ($data as $row) {
					if ($row->afk != "") {
						$afk = " AFK";
					} else {
						$afk = "";
					}
					$db->query("SELECT * FROM alts WHERE `alt` = '$row->name'");
					if ($db->numrows() == 0) {
						$alt = "";
					} else {
						$row1 = $db->fObject();
						$alt = " ($row1->main)";
					}
					$list .= "$row->name"."$alt"."$afk, ";
					$p++;
				}
			}
			$membercount = "$numonline guildmembers and $numguest private chat members are online";
			$list = substr($list,0,-2);
			
			fputs($socket, "PRIVMSG ".$channel." :$membercount\n");
			fputs($socket, "PRIVMSG ".$channel." :$list\n");
			flush();
		} else if ($ex[1] == "JOIN") {
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[IRC]<end><green> $nick joined the channel.<end>", "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || $chatBot->settings["guest_relay"] == 1) {
				$chatBot->send("<yellow>[IRC]<end><white> $nick joined the channel.<end>", "priv", true);
			}
		} else if ($ex[1] == "PART") {
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[IRC]<end><green> $nick left the channel.<end>", "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" ||$chatBot->settings["guest_relay"] == 1) {
				$chatBot->send("<yellow>[IRC]<end><white> $nick left the channel.<end>", "priv", true);
			}
		} else {
			if ($chatBot->settings['irc_debug_messages'] == 1) {
				Logger::log_chat("Inc. IRC Msg.", $nick, $ircmessage);
			}
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[IRC]<end><white> $nick: $ircmessage<end>", "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || $chatBot->settings["guest_relay"] == 1) {
				$chatBot->send("<yellow>[IRC]<end><green> $nick: $ircmessage<end>", "priv", true);
			}
			flush();
		}
	}
	unset($sandbox);
}
?>