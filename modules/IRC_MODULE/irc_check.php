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
if(($data = fgets($socket)) && ("1" == $this->settings['irc_status'])) {
	$ex = explode(' ', $data);
	$ex[3] = substr($ex[3],1,strlen($ex[3]));
	$rawcmd = rtrim(htmlspecialchars($ex[3]));
	
	$channel = rtrim(strtolower($ex[2]));
	$nicka = explode('@', $ex[0]);
	$nickb = explode('!', $nicka[0]);
	$nickc = explode(':', $nickb[0]);
	if($this->settings['irc_debug_all'] == 1)
	{
		newLine("IRC"," ",trim($data),0);
	}
	$host = $nicka[1];
	$nick = $nickc[1];
	if($ex[0] == "PING"){
		fputs($socket, "PONG ".$ex[1]."\n");
		if($this->settings['irc_debug_ping'] == 1) {
			newLine("IRC"," ","PING received. PONG sent",0);
		}
	}
	elseif($ex[1] == "QUIT") {
		if($this->vars['my guild'] != "") {
			$this->send("<yellow>[IRC]<end><green> $nick quit IRC.<end>","guild",true);
		}
		if($this->vars['my guild'] == "" ||$this->settings["guest_relay"] == 1) {
			$this->send("<yellow>[IRC]<end><white> $nick quit IRC.<end>","priv",true);
		}
	}
	elseif($channel == trim(strtolower($this->settings['irc_channel']))) {
		$args = NULL; for ($i = 4; $i < count($ex); $i++) { $args .= rtrim(htmlspecialchars($ex[$i])) . ' '; }
		for ($i = 3; $i < count($ex); $i++) {
			$ircmessage .= rtrim(htmlspecialchars($ex[$i]))." ";
		}
		if ($rawcmd == "!sayit") {
			fputs($socket, "PRIVMSG ".$channel." :".$args." \n");
		}
		elseif ($rawcmd == "!md5") {
			fputs($socket, "PRIVMSG ".$channel." :MD5 ".md5($args)."\n");
		}
		elseif ($rawcmd == "!online") {
			$numonline = 0;
			$numguest = 0;
			//guild listing
			if($this->vars['my guild'] != "") {
				$db->query("SELECT * FROM guild_chatlist_<myname>");
				$numonline = $db->numrows();
				if($numonline != 0) {
					$data = $db->fObject("all");
					foreach($data as $row) {
						if($row->afk == "kiting")
							$afk = " KITING";
						elseif($row->afk != "0")
							$afk = " AFK";
						else
							$afk = "";
						$db->query("SELECT * FROM alts WHERE `alt` = '$row->name'");
						if($db->numrows() == 0)
							$alt = "";
						else {
							$row1 = $db->fObject();
							$alt = " ($row1->main)";
						}
						$list .= "$row->name"."$alt"."$afk, ";
						$g++;
					}
				}
			}
			//priv listing
			$db->query("SELECT * FROM priv_chatlist_<myname>");
			$numguest = $db->numrows();
			if($db->numrows() != 0) {
				$data = $db->fObject("all");
				foreach($data as $row) {
					if($row->afk != "0")
						$afk = " AFK";
					else
						$afk = "";
					$db->query("SELECT * FROM alts WHERE `alt` = '$row->name'");
					if($db->numrows() == 0)
						$alt = "";
					else {
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
		}
		elseif($ex[1] == "JOIN") {
			if($this->vars['my guild'] != "") {
				$this->send("<yellow>[IRC]<end><green> $nick joined the channel.<end>","guild",true);
			}
			if($this->vars['my guild'] == "" ||$this->settings["guest_relay"] == 1) {
				$this->send("<yellow>[IRC]<end><white> $nick joined the channel.<end>","priv",true);
			}
		}
		elseif($ex[1] == "PART") {
			if($this->vars['my guild'] != "") {
				$this->send("<yellow>[IRC]<end><green> $nick left the channel.<end>","guild",true);
			}
			if($this->vars['my guild'] == "" ||$this->settings["guest_relay"] == 1) {
				$this->send("<yellow>[IRC]<end><white> $nick left the channel.<end>","priv",true);
			}
		}
		else {
			if($this->settings['irc_debug_messages'] == 1) {
				newLine("IRC"," ","[Inc. IRC Msg.] $nick: $ircmessage",0);
			}
			if($this->vars['my guild'] != "") {
				$this->send("<yellow>[IRC]<end><green> $nick: $ircmessage<end>","guild",true);
			}
			if($this->vars['my guild'] == "" ||$this->settings["guest_relay"] == 1) {
				$this->send("<yellow>[IRC]<end><white> $nick: $ircmessage<end>","priv",true);
			}
			flush();
		}
	}
	unset($sandbox);
}
?>