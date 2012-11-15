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
 *		accessLevel = 'mod', 
 *		description = "Connect to IRC", 
 *		help        = 'irc.txt',
 *		alias       = 'startirc'
 *	)
 *	@DefineCommand(
 *		command     = 'disconnectirc', 
 *		accessLevel = 'mod', 
 *		description = "Disconnect from IRC", 
 *		help        = 'irc.txt',
 *		alias       = 'stopirc'
 *	)
 *	@DefineCommand(
 *		command     = 'setirc',
 *		accessLevel = 'mod',
 *		description = 'Manually set IRC settings',
 *		help        = 'irc_help.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'onlineirc', 
 *		accessLevel = 'all', 
 *		description = 'Show users in IRC channel', 
 *		help        = 'irc.txt'
 *	)
 */
class IRCController {

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
	public $util;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $alts;

	/** @Inject */
	public $commandManager;
	
	/** @Inject */
	public $onlineController;
	
	/** @Inject */
	public $relayController;
	
	/** @Inject */
	public $playerManager;
	
	/** @Inject */
	public $preferences;
	
	/** @Inject */
	public $settingManager;
	
	/** @Logger */
	public $logger;

	private $setting;

	private $irc;
	
	/** @Setup */
	public function setup() {
		$this->setting = new Set();
		Registry::injectDependencies($this->setting);
		$this->onlineController->register($this);
		
		if ($this->chatBot->vars['my_guild'] == "") {
			$channel = "#".$this->chatBot->vars['name'];
		} else {
			$sandbox = explode(" ", $this->chatBot->vars['my_guild']);
			for ($i = 0; $i < count($sandbox); $i++) {
				$channel .= ucfirst(strtolower($sandbox[$i]));
			}
			$channel = "#".$channel;
		}
		
		$this->settingManager->add($this->moduleName, "irc_status", "Status of IRC uplink", "noedit", "options", "0", "Offline;Online", "0;1");
		$this->settingManager->add($this->moduleName, "irc_server", "IRC server to connect to", "noedit", "text", "irc.funcom.com", "irc.funcom.com");
		$this->settingManager->add($this->moduleName, "irc_port", "IRC server port to use", "noedit", "number", "6667", "6667");
		$this->settingManager->add($this->moduleName, "irc_nickname", "Nickname to use while in IRC", "noedit", "text", $this->chatBot->vars['name'], $this->chatBot->vars['name']);
		$this->settingManager->add($this->moduleName, "irc_channel", "Channel to join", "noedit", "text", $channel, $channel);
		$this->settingManager->add($this->moduleName, "irc_password", "IRC password to join channel", "edit", "text", "none", "none");
		$this->settingManager->add($this->moduleName, 'irc_guild_message_color', "Color of messages from other bots in the IRC channel", 'edit', "color", "<font color='#FFFFFF'>");
		$this->settingManager->add($this->moduleName, 'irc_guild_name_color', "Color of guild names from other bots in the IRC channel", 'edit', "color", "<font color='#FFFFFF'>");
		$this->settingManager->add($this->moduleName, 'irc_message_color', "Color of messages from users in the IRC channel", 'edit', "color", "<font color='#FFFFFF'>");
		$this->settingManager->add($this->moduleName, 'irc_ignore', "Defines which characters to ignore", 'edit', "text", 'none', 'none', '', '', 'irc_ignore.txt');
	}
	
	/**
	 * @HandlesCommand("setirc")
	 * @Matches("/^setirc server (.+)$/i")
	 */
	public function setIRCServerCommand($message, $channel, $sender, $sendto, $args) {
		$server = trim($args[1]);
		$this->setting->irc_server = $server;
		$sendto->reply("Setting saved.  Bot will connect to IRC server: {$server}.");
	}
	
	/**
	 * @HandlesCommand("setirc")
	 * @Matches("/^setirc port (.+)$/i")
	 */
	public function setIRCPortCommand($message, $channel, $sender, $sendto, $args) {
		$port = trim($args[1]);
		if (is_numeric($port)) {
			$this->setting->irc_port = trim($port);
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
		$this->setting->irc_nickname = $nickname;
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
			$this->setting->irc_channel = $channel;
			$sendto->reply("Setting saved.  Bot will join $channel when it connects to IRC.");
		}
	}
	
	/**
	 * @HandlesCommand("setirc")
	 * @Matches("/^setirc password (.+)$/i")
	 */
	public function setIRCPasswordCommand($message, $channel, $sender, $sendto, $args) {
		$password = trim($args[1]);
		$this->setting->irc_password = $password;
		$sendto->reply("Setting saved.  Bot will use {$password} as the password when connecting to IRC.");
	}
	
	public function getOnlineList() {
		$numirc = 0;
		$blob = '';
		if ($this->ircActive()) {
			forEach ($this->irc->getChannels() as $channel) {
				$numirc += count($channel->users);

				$blob .= "\n<tab><highlight>{$channel->name}<end>\n";
				forEach ($channel->users as $user) {
					if ($user->nick == $this->setting->irc_nickname) {
						$numirc--;
					} else {
						$blob .= "<tab><tab>{$user->nick}\n";
					}
				}
			}
			$blob = "\n\n<header2> ::: IRC ($numirc) ::: <end>\n" . $blob;
		}
		return array($numirc, $blob);
	}
	
	/**
	 * @HandlesCommand("onlineirc")
	 * @Matches("/^onlineirc$/i")
	 */
	public function onlineircCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->ircActive()) {
			list($num, $blob) = $this->getOnlineList();
			if ($num == 0) {
				$msg = "There are no users in the IRC channel.";
			} else {
				$msg = $this->text->make_blob("IRC Online ($num)", $blob);
			}
		} else {
			$msg = "There is no active IRC connection.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("connectirc")
	 * @Matches("/^connectirc$/i")
	 */
	public function connectircCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->setting->irc_server == "") {
			$sendto->reply("The IRC <highlight>server address<end> seems to be missing. <highlight>/tell <myname> help irc<end> for details on setting this.");
			return;
		}
		if ($this->setting->irc_port == "") {
			$sendto->reply("The IRC <highlight>server port<end> seems to be missing. <highlight>/tell <myname> help irc<end> for details on setting this.");
			return;
		}
		
		if ($this->irc != null) {
			$this->irc->disconnect();
			$this->irc = null;
		}

		$sendto->reply("Intializing IRC connection. Please wait...");
		
		$this->connect();
		if ($this->ircActive()) {
			$this->setting->irc_status = "1";
		} else {
			$sendto->reply("Error connecting to IRC.");
		}
	}
	
	public function connect() {
		$realname = 'Budabot - SmartIRC Client ' . SMARTIRC_VERSION;

		$this->irc = new Net_SmartIRC();
		$this->irc->setUseSockets(true);
		$this->irc->setChannelSyncing(true);
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '', $this, 'channelMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '', $this, 'queryMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_JOIN, '', $this, 'joinMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_PART, '', $this, 'leaveMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUIT, '', $this, 'leaveMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_KICK, '', $this, 'kickMessage');
		//$this->irc->registerActionhandler(SMARTIRC_TYPE_NAME, '', $this, 'nameMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_NOTICE, '', $this, 'noticeMessage');
		$this->irc->connect($this->setting->irc_server, $this->setting->irc_port);
		$this->irc->login($this->setting->irc_nickname, $realname, 0, $this->setting->irc_password);
		$this->irc->join(array($this->setting->irc_channel));
		$this->irc->listenOnce();
	}
	
	/**
	 * @HandlesCommand("disconnectirc")
	 * @Matches("/^disconnectirc$/i")
	 */
	public function disconnectircCommand($message, $channel, $sender, $sendto, $args) {
		$this->setting->irc_status = "0";

		if ($this->ircActive()) {
			$this->irc->disconnect();
			$this->irc = null;
			$this->logger->log('INFO', "Disconnected from IRC");
			$sendto->reply("The IRC connection has been disconnected.");
		} else {
			$sendto->reply("There is no active IRC connection.");
		}
	}
	
	/**
	 * @Event("1min")
	 * @Description("Automatically reconnect to IRC server")
	 * @DefaultStatus("0")
	 */
	public function autoReconnectEvent() {
		if ($this->setting->irc_status == '1') {
			// get the topic to test the connection
			if ($this->irc !== null) {
				$this->irc->getTopic($this->setting->irc_channel);
			}
			if (!$this->ircActive()) {
				$this->connect();
			}
		}
	}
	
	/**
	 * @Event("2s")
	 * @Description("Listen for IRC messages")
	 */
	public function checkForIRCEvent($eventObj) {
		if ($this->ircActive()) {
			$this->irc->listenOnce();
		}
	}
	
	public function channelMessage(&$irc, &$obj) {
		$ircIgnore = explode(",", strtolower($this->setting->irc_ignore));
		if (in_array(strtolower($obj->nick), $ircIgnore)) {
			return;
		}
		
		$this->logger->log_chat("Inc. IRC Msg.", -1, $obj->message);
		
		if ($obj->message == "!online") {
			$this->handleOnlineCmd($obj);
		} else if ($obj->message[0] == $this->setting->symbol) {
			$sendto = new IRCCommandReply($irc, $obj->channel, $obj->type);
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
					$list .= $row->name . " ";
				}
			}
		}
		//priv listing
		$data = $this->db->query("SELECT * FROM online WHERE channel_type = 'priv'");
		$numguest = count($data);
		if ($numguest != 0) {
			forEach ($data as $row) {
				$list .= $row->name . " ";
			}
		}

		$msg = "Guild ($numguild), Private Channel($numguest): " . $list;
		$this->logger->log_chat("Out. IRC Msg.", -1, $msg);
		$this->irc->message($obj->type, $obj->channel, $msg);
	}
	
	public function handleIncomingIRCMessage($obj) {
		$msgColor = $this->setting->irc_message_color;
		$guildMsgColor = $this->setting->irc_guild_message_color;
		$guildNameColor = $this->setting->irc_guild_name_color;

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
		if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
			$this->chatBot->sendPrivate($ircmessage, true);
		}
	}
	
	public function queryMessage(&$irc, &$obj) {
		
	}
	
	public function joinMessage(&$irc, &$obj) {
		$msgColor = $this->setting->irc_message_color;
		if ($obj->nick == $this->setting->irc_nickname) {
			$msg = "<yellow>[IRC]<end> {$msgColor}Connected to IRC {$this->setting->irc_server}: $obj->channel.<end>";
		} else {
			$msg = "<yellow>[IRC]<end> {$msgColor}$obj->nick joined the channel.<end>";
		}
		if ($this->chatBot->vars['my_guild'] != "") {
			$this->chatBot->sendGuild($msg, true);
		}
		if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
			$this->chatBot->sendPrivate($msg, true);
		}
	}
	
	public function leaveMessage(&$irc, &$obj) {
		$msgColor = $this->setting->irc_message_color;
		$msg = "<yellow>[IRC]<end> {$msgColor}$obj->nick left the channel.<end>";
		
		if ($this->chatBot->vars['my_guild'] != "") {
			$this->chatBot->sendGuild($msg, true);
		}
		if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
			$this->chatBot->sendPrivate($msg, true);
		}
	}
	
	public function kickMessage(&$irc, &$obj) {
		$extendedinfo = $this->text->make_blob("Extended information", $obj->message);
		if ($obj->nick == $this->setting->irc_nickname) {
			$msg = "<yellow>[IRC]<end> Bot was kicked from the server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		} else {
			$msg = "<yellow>[IRC]<end> ".$obj->nick." was kicked from the server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
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
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
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
		if ($this->ircActive() && $message[0] != $this->setting->symbol) {
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
				if ($this->setting->first_and_last_alt_only == 1) {
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
				if ($this->setting->first_and_last_alt_only == 1) {
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
		if ($this->ircActive()) {
			$this->logger->log_chat("Out. IRC Msg.", -1, $message);
			$guild = $this->relayController->getGuildAbbreviation();
			if (empty($guild)) {
				$ircmsg = $message;
			} else {
				$ircmsg = $this->encodeGuildMessage($guild, $message);
			}
			$this->irc->message(SMARTIRC_TYPE_CHANNEL, $this->setting->irc_channel, $ircmsg);
		} else {
			$this->logger->log("WARN", "Could not send message to IRC, not connected: $message");
		}
	}
}
