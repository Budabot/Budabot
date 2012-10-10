<?php
/**
 * Authors: 
 *	- Legendadv (RK2)
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'startirc',
 *		accessLevel = 'mod',
 *		description = 'Connect to IRC',
 *		help        = 'irc_help.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'stopirc',
 *		accessLevel = 'mod',
 *		description = 'Disconnect from IRC',
 *		help        = 'irc_help.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'setirc',
 *		accessLevel = 'mod',
 *		description = 'Manually set IRC settings',
 *		help        = 'irc_help.txt'
 *	)
 */
class IRCRelayController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $alts;
	
	/** @Inject */
	public $commandManager;
	
	/** @Logger */
	public $logger;
	
	private $apisocket = null;

	/** @Setup */
	public function setup() {
		if ($this->setting->exists('irc_channel')) {
			$channel = $this->setting->get('irc_channel');
		} else {
			$channel = false;
		}
		if ($channel === false) {
			if ($this->chatBot->vars['my_guild'] == "") {
				$channel = "#".$this->chatBot->vars['name'];
			} else {
				if (strpos($this->chatBot->vars['my_guild']," ")) {
					$sandbox = explode(" ", $this->chatBot->vars['my_guild']);
					for ($i = 0; $i < count($sandbox); $i++) {
						$channel .= ucfirst(strtolower($sandbox[$i]));
					}
					$channel = "#".$channel;
				} else {
					$channel = "#".$this->chatBot->vars['my_guild'];
				}
			}
		}
		
		$this->setting->add($this->moduleName, "irc_status", "Status of IRC uplink", "noedit", "options", "0", "Offline;Online", "0;1");
		$this->setting->add($this->moduleName, "irc_server", "IRC server to connect to", "noedit", "text", "irc.funcom.com", "irc.funcom.com");
		$this->setting->add($this->moduleName, "irc_port", "IRC server port to use", "noedit", "number", "6667", "6667");
		$this->setting->add($this->moduleName, "irc_nickname", "Nickname to use while in IRC", "noedit", "text", $this->chatBot->vars['name'], $this->chatBot->vars['name']);
		$this->setting->add($this->moduleName, "irc_channel", "Channel to join", "noedit", "text", $channel, $channel);
		$this->setting->add($this->moduleName, "irc_password", "IRC password to join channel", "edit", "text", "none", "none");
		$this->setting->add($this->moduleName, 'irc_guild_message_color', "Color of messages from other bots in the IRC channel", 'edit', "color", "<font color='#FFFFFF'>");
		$this->setting->add($this->moduleName, 'irc_guild_name_color', "Color of guild names from other bots in the IRC channel", 'edit', "color", "<font color='#FFFFFF'>");
		$this->setting->add($this->moduleName, 'irc_message_color', "Color of messages from users in the IRC channel", 'edit', "color", "<font color='#FFFFFF'>");
		$this->setting->add($this->moduleName, 'irc_ignore', "Defines which characters to ignore", 'edit', "text", 'none', 'none', '', '', 'irc_ignore.txt');
	}
	
	/**
	 * @HandlesCommand("startirc")
	 * @Matches("/^startirc$/i")
	 */
	public function startIRCCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->setting->get('irc_server') == "") {
			$sendto->reply("The IRC <highlight>server address<end> seems to be missing. <highlight>/tell <myname> help irc<end> for details on setting this.");
			return;
		}
		if ($this->setting->get('irc_port') == "") {
			$sendto->reply("The IRC <highlight>server port<end> seems to be missing. <highlight>/tell <myname> help irc<end> for details on setting this.");
			return;
		}

		$sendto->reply("Intializing IRC connection. Please wait...");
		IRC::connect($this->ircSocket, $this->setting->get('irc_nickname'), $this->setting->get('irc_server'), $this->setting->get('irc_port'), $this->setting->get('irc_password'), $this->setting->get('irc_channel'));
		if (IRC::isConnectionActive($this->ircSocket)) {
			$this->setting->save("irc_status", "1");
			$sendto->reply("Finished connecting to IRC.");
		} else {
			$sendto->reply("Error connecting to IRC.");
		}
	}
	
	/**
	 * @HandlesCommand("stopirc")
	 * @Matches("/^stopirc$/i")
	 */
	public function stopIRCCommand($message, $channel, $sender, $sendto, $args) {
		$this->setting->save("irc_status", "0");

		if (!IRC::isConnectionActive($this->ircSocket)) {
			$sendto->reply("There is no active IRC connection.");
		} else {
			IRC::disconnect($this->ircSocket);
			$this->logger->log('INFO', "IRC", "Disconnected from IRC");
			$sendto->reply("The IRC connection has been disconnected.");
		}
	}
	
	/**
	 * @HandlesCommand("setirc")
	 * @Matches("/^setirc server (.+)$/i")
	 */
	public function setIRCServerCommand($message, $channel, $sender, $sendto, $args) {
		$server = trim($args[1]);
		$this->setting->save("irc_server", $server);
		$sendto->reply("Setting saved.  Bot will connect to IRC server: {$server}.");
	}
	
	/**
	 * @HandlesCommand("setirc")
	 * @Matches("/^setirc port (.+)$/i")
	 */
	public function setIRCPortCommand($message, $channel, $sender, $sendto, $args) {
		$port = trim($args[1]);
		if (is_numeric($port)) {
			$this->setting->save("irc_port", trim($port));
			$sendto->reply("Setting saved.  Bot will use port {$port} to connect to the IRC server.");
		} else {
			$sendto->reply("Please check again.  The port should be a number.");
		}
	}
	
	/**
	 * @HandlesCommand("setirc")
	 * @Matches("/^setirc nickname (.+)$/i")
	 */
	public function setIRCNicknameCommand($message, $channel, $sender, $sendto, $args) {
		$nickname = trim($args[1]);
		$this->setting->save("irc_nickname", $nickname);
		$sendto->reply("Setting saved.  Bot will use {$nickname} as its nickname while in IRC.");
	}
	
	/**
	 * @HandlesCommand("setirc")
	 * @Matches("/^setirc channel (.+)$/i")
	 */
	public function setIRCChannelCommand($message, $channel, $sender, $sendto, $args) {
		$channel = trim($args[1]);
		if (strpos($channel, " ") !== false) {
			$sendto->reply("IRC channels cannot have spaces in them");
		} else {
			if (strpos($channel, "#") === false) {
				$channel = "#" . $channel;
			}
			$this->setting->save("irc_channel", $channel);
			$sendto->reply("Setting saved.  Bot will join $channel when it connects to IRC.");
		}
	}
	
	/**
	 * @HandlesCommand("setirc")
	 * @Matches("/^setirc password (.+)$/i")
	 */
	public function setIRCPasswordCommand($message, $channel, $sender, $sendto, $args) {
		$password = trim($args[1]);
		$this->setting->save("irc_password", $password);
		$sendto->reply("Setting saved.  Bot will use {$password} as the password when connecting to IRC.");
	}

	/**
	 * @Event("1min")
	 * @Description("Automatically reconnect to IRC server")
	 * @DefaultStatus("0")
	 */
	public function autoReconnectEvent() {
		// make sure eof flag is set
		fputs($this->ircSocket, "PONG ping\n");
		if ($this->setting->get('irc_status') == '1' && !IRC::isConnectionActive($this->ircSocket)) {
			IRC::connect($this->ircSocket, $this->setting->get('irc_nickname'), $this->setting->get('irc_server'), $this->setting->get('irc_port'), $this->setting->get('irc_password'), $this->setting->get('irc_channel'));
		}
	}
	
	/**
	 * @Event("2sec")
	 * @Description("Check for messages from IRC")
	 */
	public function checkForIRCMessagesEvent($eventObj) {
		if (!IRC::isConnectionActive($this->ircSocket)) {
			return;
		}

		if ($data = trim(fgets($this->ircSocket))) {
			$ex = explode(' ', $data);
			$this->logger->log('DEBUG', $data);
			$ex[3] = substr($ex[3], 1, strlen($ex[3]));

			$channel = rtrim($ex[2]);
			$nicka = explode('@', $ex[0]);
			$nickb = explode('!', $nicka[0]);
			$nickc = explode(':', $nickb[0]);

			$host = $nicka[1];
			$nick = $nickc[1];

			$msgColor = $this->setting->get('irc_message_color');
			$guildMsgColor = $this->setting->get('irc_guild_message_color');
			$guildNameColor = $this->setting->get('irc_guild_name_color');

			if ("PING" == $ex[0]) {
				fputs($this->ircSocket, "PONG ".$ex[1]."\n");
				$this->logger->log('DEBUG', "PING received. PONG sent");
			} else if ("NOTICE" == $ex[1]) {
				if (false != stripos($data, "exiting")) {
					// the irc server shut down (i guess)
					// send notification to channel
					$extendedinfo = $this->text->make_blob("Extended information", $data);
					if ($this->chatBot->vars['my_guild'] != "") {
						$this->chatBot->sendGuild("<yellow>[IRC]<end> Lost connection with server:".$extendedinfo, true);
					}
					if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
						$this->chatBot->sendPrivate("<yellow>[IRC]<end> Lost connection with server:".$extendedinfo, true);
					}
				}
			} else if ("KICK" == $ex[1]) {
				$extendedinfo = $this->text->make_blob("Extended information", $data);
				if ($ex[3] == $this->setting->get('irc_nickname')) {
					if ($this->chatBot->vars['my_guild'] != "") {
						$this->chatBot->sendGuild("<yellow>[IRC]<end> Bot was kicked from the server:".$extendedinfo, true);
					}
					if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
						$this->chatBot->sendPrivate("<yellow>[IRC]<end> Bot was kicked from the server:".$extendedinfo, true);
					}
				} else {
					if ($this->chatBot->vars['my_guild'] != "") {
						$this->chatBot->sendGuild("<yellow>[IRC]<end> ".$ex[3]." was kicked from the server:".$extendedinfo, true);
					}
					if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
						$this->chatBot->sendPrivate("<yellow>[IRC]<end> ".$ex[3]." was kicked from the server:".$extendedinfo, true);
					}
				}
			} else if("QUIT" == $ex[1] || "PART" == $ex[1]) {
				$this->db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'irc' AND added_by = '<myname>'", $nick);

				if ($this->chatBot->vars['my_guild'] != "") {
					$this->chatBot->sendGuild("<yellow>[IRC]<end> {$msgColor}$nick left the channel.<end>", true);
				}
				if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
					$this->chatBot->sendPrivate("<yellow>[IRC]<end> {$msgColor}$nick left the channel.<end>", true);
				}
			} else if ("JOIN" == $ex[1]) {
				$data = $this->db->query("SELECT name FROM online WHERE `name` = ? AND `channel_type` = 'irc' AND added_by = '<myname>'", $nick);
				if (count($data) == 0)
					$this->db->exec("INSERT INTO online (`name`, `channel`, `channel_type`, `added_by`, `dt`) VALUES (?, ?, 'irc', '<myname>', ?)", $nick, $channel, time());

				if ($this->chatBot->vars['my_guild'] != "") {
					$this->chatBot->sendGuild("<yellow>[IRC]<end> {$msgColor}$nick joined the channel.<end>", true);
				}
				if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
					$this->chatBot->sendPrivate("<yellow>[IRC]<end> {$msgColor}$nick joined the channel.<end>", true);
				}
			} else if ("PRIVMSG" == $ex[1] && strtolower($channel) == trim(strtolower($this->setting->get('irc_channel')))) {
				$args = NULL;
				for ($i = 4; $i < count($ex); $i++) {
					$args .= rtrim(htmlspecialchars($ex[$i])) . ' ';
				}
				for ($i = 3; $i < count($ex); $i++) {
					$ircmessage .= rtrim(htmlspecialchars($ex[$i]))." ";
				}

				$rawcmd = rtrim(htmlspecialchars($ex[3]));

				$this->logger->log_chat("Inc. IRC Msg.", $nick, $ircmessage);

				if ($rawcmd == "!online") {
					$numguild = 0;

					$numguest = 0;
					//guild listing
					if ($this->chatBot->vars['my_guild'] != "") {
						$data = $this->db->query("SELECT * FROM online WHERE channel_type = 'guild'");
						$numguild = count($data);
						if ($numguild != 0) {
							forEach ($data as $row) {
								switch ($row->afk) {
									case "kiting": $afk = " KITING"; break;
									case       "": $afk = ""; break;
									default      : $afk = " AFK"; break;
								}

								$row1 = $this->db->queryRow("SELECT * FROM alts WHERE `alt` = ?", $row->name);
								$list .= "$row->name".($row1 === null ? "":" ($row1->main)")."$afk, ";
								$g++;
							}
						}
					}
					//priv listing
					$data = $this->db->query("SELECT * FROM online WHERE channel_type = 'priv'");
					$numguest = count($data);
					if ($numguest != 0) {
						forEach ($data as $row) {
							switch ($row->afk) {
								case "kiting": $afk = " KITING"; break;
								case       "": $afk = ""; break;
								default      : $afk = " AFK"; break;
							}

							$row1 = $this->db->queryRow("SELECT * FROM alts WHERE `alt` = ?", $row->name);
							$list .= "$row->name".($row1 === null ? "":" ($row1->main)")."$afk, ";
							$p++;
						}
					}

					$membercount = "$numguild guildmembers and $numguest private chat members are online";
					$list = substr($list,0,-2);

					fputs($this->ircSocket, "PRIVMSG ".$channel." :$membercount\n");
					fputs($this->ircSocket, "PRIVMSG ".$channel." :$list\n");
				} else if ($ircmessage[0] == $this->setting->get('symbol')) {
					$sendto = new IRCCommandReply($this->chatBot, $this->ircSocket, $this->setting->get('irc_channel'));
					$this->commandManager->process('msg', substr(rtrim($ircmessage), 1), '', $sendto);
				} else {
					$ircarray = explode(",", strtolower($this->setting->get('irc_ignore')));
					if (in_array(strtolower($nick), $ircarray)) return;

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

					if ($this->chatBot->vars['my_guild'] != "") {
						$this->chatBot->sendGuild($ircmessage, true);
					}
					if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
						$this->chatBot->sendPrivate($ircmessage, true);
					}
					flush();
				}
			}
		}
	}
	
	/**
	 * @Event("priv")
	 * @Description("Relay private messages to IRC")
	 */
	public function relayPrivMessagesEvent($eventObj) {
		if (IRC::isConnectionActive($this->ircSocket)) {
			$pattern = '/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/';
			$replace = chr(3) . chr(3) . '\4' . chr(3) . ' ' . chr(3) . '(http://auno.org/ao/db.php?id=\1&id2=\2&ql=\3)' . chr(3) . chr(3);

			$msg = strip_tags(htmlspecialchars_decode(preg_replace($pattern, $replace, $eventObj->message)));

			if ($this->util->isValidSender($eventObj->sender)) {
				$msg = "$eventObj->sender: $msg";
			}
			IRC::send($this->ircSocket, $this->setting->get('irc_channel'), $this->encodeGuildMessage(getGuildAbbreviation(), $msg));
			$this->logger->log_chat("Out. IRC Msg.", $eventObj->sender, $msg);
		}
	}
	
	/**
	 * @Event("guild")
	 * @Description("Relay guild messages to IRC")
	 */
	public function relayGuildMessagesEvent($eventObj) {
		if (IRC::isConnectionActive($this->ircSocket)) {
			$pattern = '/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/';
			$replace = chr(3) . chr(3) . '\4' . chr(3) . ' ' . chr(3) . '(http://auno.org/ao/db.php?id=\1&id2=\2&ql=\3)' . chr(3) . chr(3);

			$msg = strip_tags(htmlspecialchars_decode(preg_replace($pattern, $replace, $eventObj->message)));

			if ($this->util->isValidSender($eventObj->sender)) {
				$msg = "$eventObj->sender: $msg";
			}
			IRC::send($this->ircSocket, $this->setting->get('irc_channel'), $this->encodeGuildMessage(getGuildAbbreviation(), $msg));
			$this->logger->log_chat("Out. IRC Msg.", $eventObj->sender, $msg);
		}
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Sends joined channel messages")
	 */
	public function joinPrivEvent($eventObj) {
		if (IRC::isConnectionActive($this->ircSocket)) {
			$msg = $this->getIRCPlayerInfo($eventObj->sender);
			$this->logger->log_chat("Out. IRC Msg.", -1, $msg);
			IRC::send($this->ircSocket, $this->setting->get('irc_channel'), $this->encodeGuildMessage(getGuildAbbreviation(), $msg));
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Shows a logon from a member")
	 */
	public function logonEvent($eventObj) {
		if (IRC::isConnectionActive($this->ircSocket)) {
			if (isset($this->chatBot->guildmembers[$eventObj->sender])) {
				if ($this->setting->get('first_and_last_alt_only') == 1) {
					// if at least one alt/main is still online, don't show logoff message
					$altInfo = $this->alts->get_alt_info($eventObj->sender);
					if (count($altInfo->get_online_alts()) > 1) {
						return;
					}
				}

				$msg = $this->getIRCPlayerInfo($eventObj->sender);
				$this->logger->log_chat("Out. IRC Msg.", -1, $msg);
				IRC::send($this->ircSocket, $this->setting->get('irc_channel'), $this->encodeGuildMessage(getGuildAbbreviation(), $msg));
			}
		}
	}
	
	/**
	 * @Event("leavePriv")
	 * @Description("Sends left channel messages")
	 */
	public function leavePrivEvent($eventObj) {
		if (IRC::isConnectionActive($this->ircSocket)) {
			$msg = "$eventObj->sender has left the private channel.";
			IRC::send($this->ircSocket, $this->setting->get('irc_channel'), $this->encodeGuildMessage(getGuildAbbreviation(), $msg));
			$this->logger->log_chat("Out. IRC Msg.", -1, $msg);
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Shows a logoff from a member")
	 */
	public function logoffEvent($eventObj) {
		if (IRC::isConnectionActive($this->ircSocket)) {
			if (isset($this->chatBot->guildmembers[$eventObj->sender])) {
				if ($this->setting->get('first_and_last_alt_only') == 1) {
					// if at least one alt/main is already online, don't show logon message
					$altInfo = $this->alts->get_alt_info($eventObj->sender);
					if (count($altInfo->get_online_alts()) > 0) {
						return;
					}
				}

				$msg = "$eventObj->sender has logged off.";
				IRC::send($this->ircSocket, $this->setting->get('irc_channel'), $this->encodeGuildMessage(getGuildAbbreviation(), $msg));
				$this->logger->log_chat("Out. IRC Msg.", -1, $msg);
			}
		}
	}
	
	public function encodeGuildMessage($guild, $msg) {
		return chr(2) . chr(2) . chr(2) . "[{$guild}]" .  chr(2) . ' ' . $msg;
	}
	
	public function getIRCPlayerInfo($sender) {
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
		$altInfo = $this->alts->get_alt_info($sender);
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

