<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
if (!IRC::isConnectionActive()) {
	return;
}

global $socket;
if ($data = fgets($socket)) {
	$ex = explode(' ', $data);
	$ex[3] = substr($ex[3],1,strlen($ex[3]));
	$rawcmd = rtrim(htmlspecialchars($ex[3]));
	
	$channel = rtrim(strtolower($ex[2]));
	$nicka = explode('@', $ex[0]);
	$nickb = explode('!', $nicka[0]);
	$nickc = explode(':', $nickb[0]);
	if (Setting::get('irc_debug_all') == 1) {
		Logger::log('info', "IRC", trim($data));
	}
	$host = $nicka[1];
	$nick = $nickc[1];
	if ($ex[0] == "PING") {
		fputs($socket, "PONG ".$ex[1]."\n");
		if (Setting::get('irc_debug_ping') == 1) {
			Logger::log('info', "IRC", "PING received. PONG sent");
		}
	} else if($ex[1] == "QUIT") {
		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->send("<yellow>[IRC]<end><green> $nick quit IRC.<end>","guild",true);
		}
		if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
			$chatBot->send("<yellow>[IRC]<end><white> $nick quit IRC.<end>","priv",true);
		}
	} else if ($channel == trim(strtolower(Setting::get('irc_channel')))) {
		$args = NULL; for ($i = 4; $i < count($ex); $i++) { $args .= rtrim(htmlspecialchars($ex[$i])) . ' '; }
		for ($i = 3; $i < count($ex); $i++) {
			$ircmessage .= rtrim(htmlspecialchars($ex[$i]))." ";
		}

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
					$list .= $row->name . $alt . "$afk, ";
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
			if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
				$chatBot->send("<yellow>[IRC]<end><white> $nick joined the channel.<end>", "priv", true);
			}
		} else if ($ex[1] == "PART") {
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[IRC]<end><green> $nick left the channel.<end>", "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
				$chatBot->send("<yellow>[IRC]<end><white> $nick left the channel.<end>", "priv", true);
			}
		} else {
			if (Setting::get('irc_debug_messages') == 1) {
				Logger::log_chat("Inc. IRC Msg.", $nick, $ircmessage);
			}
			
			// handle relay messages from other bots
			if (preg_match("/" . chr(2) . chr(2) . chr(2) . "(.+)" . chr(2) . " (.+)/i", $ircmessage, $arr)) {
				$ircmessage = "<white>{$arr[1]} {$arr[2]}<end>";
			} else {
				$ircmessage = "<yellow>[IRC]<end><white> {$nick}: {$ircmessage}<end>";
			}
			
			// handle item links from other bots
			$pattern = "/" . chr(3) . chr(3) . "(.+?)" . chr(3) . ' ' . chr(3) . "[(](.+?)id=([0-9]+)&amp;id2=([0-9]+)&amp;ql=([0-9]+)[)]" . chr(3) . chr(3) . "/";
			$replace = '<a href="itemref://\3/\4/\5">\1</a>';
			$ircmessage = preg_replace($pattern, $replace, $ircmessage);
			
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send($ircmessage, "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
				$chatBot->send($ircmessage, "priv", true);
			}
			flush();
		}
	}
	unset($sandbox);
}
?>