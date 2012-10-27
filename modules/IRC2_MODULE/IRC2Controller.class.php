<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'connectirc', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'disconnectirc', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 */
class IRC2Controller {

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
	public $eventManager;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $accessLevel;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $commandManager;
	
	/** @Inject */
	public $onlineController;
	
	/** @Logger */
	public $logger;
	
	private $irc;
	
	/**
	 * @HandlesCommand("connectirc")
	 * @Matches("/^connectirc$/i")
	 */
	public function connectircCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->irc != null) {
			$this->irc->disconnect();
			$this->irc = null;
		}
		
		$this->connect();
	}
	
	public function connect() {
		$realname = 'Budabot - SmartIRC Client ' . SMARTIRC_VERSION;

		$this->irc = new Net_SmartIRC();
		$this->irc->setUseSockets(TRUE);
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '', $this, 'channelMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '', $this, 'queryMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_JOIN, '', $this, 'joinMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_PART, '', $this, 'leaveMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUIT, '', $this, 'leaveMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_KICK, '', $this, 'kickMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_NAME, '', $this, 'nameMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_NOTICE, '', $this, 'noticeMessage');
		$this->irc->connect($this->setting->get('irc_server'), $this->setting->get('irc_port'));
		$this->irc->login($this->setting->get('irc_nickname'), $realname, 0, $this->setting->get('irc_password'));
		$this->irc->join(array($this->setting->get('irc_channel')));
		$this->irc->listenOnce();
	}
	
	/**
	 * @HandlesCommand("disconnectirc")
	 * @Matches("/^disconnectirc$/i")
	 */
	public function disconnectircCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->irc != null) {
			$this->irc->disconnect();
			$this->irc = null;
		}
	}
	
	/**
	 * @Event("2s")
	 * @Description("Listen to IRC")
	 */
	public function checkForIRCEvent($eventObj) {
		if ($this->irc != null) {
			$this->irc->listenOnce();
		}
	}
	
	public function channelMessage(&$irc, &$obj) {
		$ircIgnore = explode(",", strtolower($this->setting->get('irc_ignore')));
		if (in_array(strtolower($obj->nick), $ircIgnore)) {
			return;
		}
		
		if ($obj->message == "!online") {
			$this->handleOnlineCmd($obj);
		} else if ($obj->message[0] == $this->setting->get('symbol')) {
			$sendto = new IRCCommandReply2($irc, $obj->channel, $obj->type);
			$this->commandManager->process('msg', substr(rtrim($obj->message), 1), '', $sendto);
		} else {
			$this->handleIncomingIRCMessage($obj);
		}
	}
	
	public function handleOnlineCmd($obj) {
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
		$list = substr($list, 0, -2);

		$this->irc->message($obj->type, $obj->channel, $membercount);
		$this->irc->message($obj->type, $obj->channel, $list);
	}
	
	public function handleIncomingIRCMessage($obj) {
		$msgColor = $this->setting->get('irc_message_color');
		$guildMsgColor = $this->setting->get('irc_guild_message_color');
		$guildNameColor = $this->setting->get('irc_guild_name_color');

		// handle relay messages from other bots
		if (preg_match("/" . chr(2) . chr(2) . chr(2) . "(.+)" . chr(2) . " (.+)/i", $obj->message, $arr)) {
			$ircmessage = "{$guildNameColor}{$arr[1]}<end> {$guildMsgColor}{$arr[2]}<end>";
		} else {
			$ircmessage = "<yellow>[IRC]<end> {$msgColor}{$obj->nick}: {$obj->message}<end>";
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
	}
	
	public function queryMessage(&$irc, &$obj) {
		
	}
	
	public function joinMessage(&$irc, &$obj) {
		$this->onlineController->addPlayerToOnlineList($obj->nick, $obj->channel, 'irc');
		
		$msgColor = $this->setting->get('irc_message_color');
		$msg = "<yellow>[IRC]<end> {$msgColor}$obj->nick joined the channel.<end>";

		if ($this->chatBot->vars['my_guild'] != "") {
			$this->chatBot->sendGuild($msg, true);
		}
		if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
			$this->chatBot->sendPrivate($msg, true);
		}
	}
	
	public function leaveMessage(&$irc, &$obj) {
		$this->onlineController->removePlayerFromOnlineList($obj->nick, 'irc');
		
		$msgColor = $this->setting->get('irc_message_color');
		$msg = "<yellow>[IRC]<end> {$msgColor}$obj->nick left the channel.<end>";
		
		if ($this->chatBot->vars['my_guild'] != "") {
			$this->chatBot->sendGuild($msg, true);
		}
		if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
			$this->chatBot->sendPrivate($msg, true);
		}
	}
	
	public function nameMessage(&$irc, &$obj) {
		print_r($obj);
		echo "nameMessage\n";
	}
	
	public function kickMessage(&$irc, &$obj) {
		$extendedinfo = $this->text->make_blob("Extended information", $obj->message);
		if ($ex[3] == $this->setting->get('irc_nickname')) {
			$msg = "<yellow>[IRC]<end> Bot was kicked from the server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		} else {
			$msg = "<yellow>[IRC]<end> ".$ex[3]." was kicked from the server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
	}
	
	public function noticeMessage(&$irc, &$obj) {
		if (false != stripos($obj->message, "exiting")) {
			// the irc server shut down (i guess)
			// send notification to channel
			$extendedinfo = $this->text->make_blob("Extended information", $obj->message);
			$msg = "<yellow>[IRC]<end> Lost connection with server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
	
		print_r($obj);
		echo "nameMessage\n";
	}
	
	public function ircActive() {
		if ($this->irc === null) {
			return false;
		}
		
		if ($this->irc->_state() !== SMARTIRC_STATE_CONNECTED) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @Event("1min")
	 * @Description("Automatically reconnect to IRC server")
	 * @DefaultStatus("0")
	 */
	public function autoReconnectEvent() {
		// make sure eof flag is set
		//fputs($this->ircSocket, "PING ping\n");
		if ($this->setting->get('irc_status') == '1' && !$this->ircActive()) {
			$this->connect();
		}
	}
	
	/**
	 * @Event("priv")
	 * @Description("Relay private messages to IRC")
	 */
	public function relayPrivMessagesEvent($eventObj) {
		$this->relayMessageToIRC($eventObj->sender, $eventObj->message);
	}
	
	/**
	 * @Event("guild")
	 * @Description("Relay guild messages to IRC")
	 */
	public function relayGuildMessagesEvent($eventObj) {
		$this->relayMessageToIRC($eventObj->sender, $eventObj->message);
	}
	
	public function relayMessageToIRC($sender, $message) {
		if ($this->ircActive() && $message[0] != $this->setting->get('symbol')) {
			$pattern = '/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/';
			$replace = chr(3) . chr(3) . '\4' . chr(3) . ' ' . chr(3) . '(http://auno.org/ao/db.php?id=\1&id2=\2&ql=\3)' . chr(3) . chr(3);

			$msg = strip_tags(htmlspecialchars_decode(preg_replace($pattern, $replace, $message)));

			if ($this->util->isValidSender($sender)) {
				$msg = "$sender: $msg";
			}
			$this->sendMessageToIRC($msg);
		}
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Sends joined channel messages")
	 */
	public function joinPrivEvent($eventObj) {
		if ($this->ircActive()) {
			$msg = $this->getIRCPlayerInfo($eventObj->sender, $eventObj->type);
			$this->sendMessageToIRC($msg);
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Shows a logon from a member")
	 */
	public function logonEvent($eventObj) {
		if ($this->ircActive()) {
			if (isset($this->chatBot->guildmembers[$eventObj->sender])) {
				if ($this->setting->get('first_and_last_alt_only') == 1) {
					// if at least one alt/main is still online, don't show logoff message
					$altInfo = $this->alts->get_alt_info($eventObj->sender);
					if (count($altInfo->get_online_alts()) > 1) {
						return;
					}
				}

				$msg = $this->getIRCPlayerInfo($eventObj->sender, $eventObj->type);
				$this->sendMessageToIRC($msg);
			}
		}
	}
	
	/**
	 * @Event("leavePriv")
	 * @Description("Sends left channel messages")
	 */
	public function leavePrivEvent($eventObj) {
		if ($this->ircActive()) {
			$msg = "$eventObj->sender has left the private channel.";
			$this->sendMessageToIRC($msg);
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Shows a logoff from a member")
	 */
	public function logoffEvent($eventObj) {
		if ($this->ircActive()) {
			if (isset($this->chatBot->guildmembers[$eventObj->sender])) {
				if ($this->setting->get('first_and_last_alt_only') == 1) {
					// if at least one alt/main is already online, don't show logon message
					$altInfo = $this->alts->get_alt_info($eventObj->sender);
					if (count($altInfo->get_online_alts()) > 0) {
						return;
					}
				}

				$msg = "$eventObj->sender has logged off.";
				$this->sendMessageToIRC($msg);
			}
		}
	}
	
	public function encodeGuildMessage($guild, $msg) {
		return chr(2) . chr(2) . chr(2) . "[{$guild}]" .  chr(2) . ' ' . $msg;
	}
	
	public function getIRCPlayerInfo($sender, $type) {
		$whois = $this->playerManager->get_by_name($sender);
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

		$logon_msg = $this->preferences->get($sender, 'logon_msg');
		if ($logon_msg !== false && $logon_msg != '') {
			$msg .= " - " . $logon_msg;
		}

		return $msg;
	}
	
	public function sendMessageToIRC($message) {
		$this->logger->log_chat("Out. IRC Msg.", -1, $message);
		$guild = $this->relayController->getGuildAbbreviation();
		if (empty($guild)) {
			$ircmsg = $message;
		} else {
			$ircmsg = $this->encodeGuildMessage($guild, $message);
		}
		$irc->message(SMARTIRC_TYPE_CHANNEL, $this->setting->get('irc_channel'), $ircmsg);
	}
}

class IRCCommandReply2 implements CommandReply {
	private $irc;
	private $channel;
	private $type;

	public function __construct(&$irc, $channel, $type) {
		$this->irc = $irc;
		$this->channel = $channel;
		$this->type = $type;
	}

	public function reply($msg) {
		$this->irc->message($this->type, $this->channel, strip_tags($msg));
	}
}
