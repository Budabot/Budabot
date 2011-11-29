<?php
/*
** Author: Legendadv (RK2)
** IRC RELAY MODULE
**
** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
**
*/

global $ircSocket;
if (!IRC::isConnectionActive($ircSocket)) {
	return;
}

if ($data = fgets($ircSocket)) {
	$ex = explode(' ', $data);
	Logger::log('DEBUG', "IRC", trim($data));
	$ex[3] = substr($ex[3],1,strlen($ex[3]));
	
	$channel = rtrim(strtolower($ex[2]));
	$nicka = explode('@', $ex[0]);
	$nickb = explode('!', $nicka[0]);
	$nickc = explode(':', $nickb[0]);
	
	$host = $nicka[1];
	$nick = $nickc[1];
	
	$msgColor = Setting::get('irc_message_color');
	$guildMsgColor = Setting::get('irc_guild_message_color');
	$guildNameColor = Setting::get('irc_guild_name_color');

	if ("PING" == $ex[0]) {
		fputs($ircSocket, "PONG ".$ex[1]."\n");
		Logger::log('DEBUG', "IRC", "PING received. PONG sent");
	} else if ("NOTICE" == $ex[1]) {
		if (false != stripos($data, "exiting")) {
			// the irc server shut down (i guess)
			// send notification to channel
			$extendedinfo = Text::make_blob("Extended information", $data);
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[IRC]<end> Lost connection with server:".$extendedinfo, "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
				$chatBot->send("<yellow>[IRC]<end> Lost connection with server:".$extendedinfo, "priv", true);
			}
		}
	} else if ("KICK" == $ex[1]) {
		$extendedinfo = Text::make_blob("Extended information", $data);
		if ($ex[3] == Setting::get('irc_nickname')) {
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[IRC]<end> Bot was kicked from the server:".$extendedinfo, "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
				$chatBot->send("<yellow>[IRC]<end> Bot was kicked from the server:".$extendedinfo, "priv", true);
			}
		} else {
			if ($chatBot->vars['my_guild'] != "") {
				$chatBot->send("<yellow>[IRC]<end> ".$ex[3]." was kicked from the server:".$extendedinfo, "guild", true);
			}
			if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
				$chatBot->send("<yellow>[IRC]<end> ".$ex[3]." was kicked from the server:".$extendedinfo, "priv", true);
			}
		}
	} else if("QUIT" == $ex[1] || "PART" == $ex[1]) {
		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->send("<yellow>[IRC]<end> {$msgColor}$nick left the channel.<end>", "guild", true);
		}
		if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
			$chatBot->send("<yellow>[IRC]<end> {$msgColor}$nick left the channel.<end>", "priv", true);
		}
	} else if ("JOIN" == $ex[1]) {
		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->send("<yellow>[IRC]<end> {$msgColor}$nick joined the channel.<end>", "guild", true);
		}
		if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
			$chatBot->send("<yellow>[IRC]<end> {$msgColor}$nick joined the channel.<end>", "priv", true);
		}
	} else if ("PRIVMSG" == $ex[1] && $channel == trim(strtolower(Setting::get('irc_channel')))) {
		$args = NULL;
		for ($i = 4; $i < count($ex); $i++) {
			$args .= rtrim(htmlspecialchars($ex[$i])) . ' ';
		}
		for ($i = 3; $i < count($ex); $i++) {
			$ircmessage .= rtrim(htmlspecialchars($ex[$i]))." ";
		}
		
		$rawcmd = rtrim(htmlspecialchars($ex[3]));
		
		Logger::log_chat("Inc. IRC Msg.", $nick, $ircmessage);

		if ($rawcmd == "!sayit") {
			fputs($ircSocket, "PRIVMSG ".$channel." :".$args." \n");
		} else if ($rawcmd == "!md5") {
			fputs($ircSocket, "PRIVMSG ".$channel." :MD5 ".md5($args)."\n");
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
			
			fputs($ircSocket, "PRIVMSG ".$channel." :$membercount\n");
			fputs($ircSocket, "PRIVMSG ".$channel." :$list\n");
		} else {
			$ircarray = explode(",", strtolower(Setting::get('irc_ignore')));
			if (in_array($nick, $ircarray)) {
				return;
			}
		
			// handle relay messages from other bots
			if (preg_match("/" . chr(2) . chr(2) . chr(2) . "(.+)" . chr(2) . " (.+)/i", $ircmessage, $arr)) {
				$ircmessage = "{$guildNameColor}{$arr[1]}<end> {$guildMsgColor}{$arr[2]}<end>";
			} else {
				$ircmessage = "<yellow>[IRC]<end> {$msgColor}{$nick}: {$ircmessage}<end>";
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