<?php

/**
 * Authors: 
 *	- Mindrila (RK1)
 *	- Legendadv (RK2)
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'startbbin', 
 *		accessLevel = 'mod', 
 *		description = 'Connect to BBIN', 
 *		help        = 'bbin_help.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'stopbbin', 
 *		accessLevel = 'mod', 
 *		description = 'Disconnect from BBIN', 
 *		help        = 'bbin_help.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'setbbin', 
 *		accessLevel = 'mod', 
 *		description = 'Configure BBIN settings', 
 *		help        = 'bbin_help.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'onlinebbin', 
 *		accessLevel = 'all', 
 *		description = 'Show users in BBIN channel', 
 *		help        = 'bbin_help.txt'
 *	)
 */
class BBINController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Logger */
	public $logger;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $playerManager;

	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $whitelist;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $onlineController;
	
	private $setting;
	
	private $irc;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->setting = new Set();
		Registry::injectDependencies($this->setting);
		
		if ($this->chatBot->vars['my_guild'] == "") {
			$channel = "#".strtolower($this->chatBot->vars['name']);
		} else {
			$sandbox = explode(" ", $this->chatBot->vars['my_guild']);
			for ($i = 0; $i < count($sandbox); $i++) {
				$channel .= ucfirst(strtolower($sandbox[$i]));
			}
			$channel = "#".$channel;
		}

		// Setup
		$this->db->loadSQLFile($this->moduleName, "bbin_chatlist");
		
		$this->settingManager->add($this->moduleName, "bbin_status", "Status of BBIN uplink", "noedit", "options", "0", "Offline;Online", "0;1", "mod", "bbin_help.txt");
		$this->settingManager->add($this->moduleName, "bbin_server", "IRC server to connect to", "noedit", "text", "irc.funcom.com", "", "", "mod", "bbin_help.txt");
		$this->settingManager->add($this->moduleName, "bbin_port", "IRC server port to use", "noedit", "number", "6667", "", "", "mod", "bbin_help.txt");
		$this->settingManager->add($this->moduleName, "bbin_nickname", "Nickname to use while in IRC", "noedit", "text", $this->chatBot->vars['name'], "", "", "mod", "bbin_help.txt");
		$this->settingManager->add($this->moduleName, "bbin_channel", "Channel to join", "noedit", "text", $channel, "", "", "mod", "bbin_help.txt");
		$this->settingManager->add($this->moduleName, "bbin_password", "IRC password to join channel", "edit", "text", "none", "none");
		
		$this->onlineController->register($this);
	}
	
	public function getOnlineList() {
		$blob = '';
		$numonline = 0;
		if ($this->setting->bbin_status == 1) {
			// members
			$data = $this->db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 0) {$prof_query} ORDER BY `profession`, `level` DESC");
			$numbbinmembers = count($data);

			if ($numbbinmembers >= 1) {
				$blob .= "\n\n<header2> ::: BBIN ($numbbinmembers) ::: <end>\n";

				$blob .= $this->onlineController->createListByProfession($data, false, true);
			}

			// guests
			$data = $this->db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 1) {$prof_query} ORDER BY `profession`, `level` DESC");
			$numbbinguests = count($data);

			if ($numbbinguests >= 1) {
				$blob .= "\n\n<header2> ::: BBIN Guests ($numbbinguests) ::: <end>\n";

				$blob .= $this->onlineController->createListByProfession($data, false, true);
			}

			$numonline = $numbbinguests + $numbbinmembers;
		}
		return array($numonline, $blob);
	}
	
	/**
	 * @HandlesCommand("startbbin")
	 * @Matches("/^startbbin$/i")
	 */
	public function startBBINCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->setting->bbin_server == "") {
			$sendto->reply("The BBIN <highlight>server address<end> seems to be missing. <highlight><symbol>help bbin<end> for details on setting this.");
			return;
		}
		if ($this->setting->bbin_port == "") {
			$sendto->reply("The BBIN <highlight>server port<end> seems to be missing. <highlight><symbol>help bbin<end> for details on setting this.");
			return;
		}
		
		if ($this->irc != null) {
			$this->irc->disconnect();
			$this->irc = null;
		}

		$sendto->reply("Intializing BBIN connection. Please wait...");

		$this->connect();
		if ($this->ircActive()) {
			$this->setting->irc_status = "1";
			$sendto->reply("Finished connecting to BBIN.");
		} else {
			$sendto->reply("Error connecting to BBIN.");
		}
	}
	
	public function connect() {
		$this->db->exec("DELETE FROM bbin_chatlist_<myname>");
	
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
		$this->irc->connect($this->setting-bbin_server, $this->setting->bbin_port);
		$this->irc->login($this->setting->bbin_nickname, $realname, 0, $this->setting-bbin_password);
		$this->irc->join(array($this->setting->bbin_channel));
		$this->irc->listenOnce();
		$this->sendMessageToBBIN("PRIVMSG ".$this->setting->bbin_channel." :[BBIN:SYNCHRONIZE]");
		$this->parse_incoming_bbin("[BBIN:SYNCHRONIZE]", '');
	}
	
	/**
	 * @HandlesCommand("stopbbin")
	 * @Matches("/^stopbbin$/i")
	 */
	public function stopBBINCommand($message, $channel, $sender, $sendto, $args) {
		$this->setting->bbin_status = "0";
		
		$this->setting->irc_status = "0";

		if ($this->ircActive()) {
			$this->irc->disconnect();
			$this->irc = null;
			$this->logger->log('INFO', "Disconnected from BBIN");
			$sendto->reply("The BBIN connection has been disconnected.");
		} else {
			$sendto->reply("There is no active BBIN connection.");
		}
	}
	
	/**
	 * @HandlesCommand("setbbin")
	 * @Matches("/^setbbin server (.+)$/i")
	 */
	public function setBBINServerCommand($message, $channel, $sender, $sendto, $args) {
		$server = trim($args[1]);
		$this->setting->bbin_server = $server;
		$sendto->reply("Setting saved.  Bot will connect to IRC server: $server.");
	}
	
	/**
	 * @HandlesCommand("setbbin")
	 * @Matches("/^setbbin port (.+)$/i")
	 */
	public function setBBINPortCommand($message, $channel, $sender, $sendto, $args) {
		$port = trim($args[1]);
		if (is_numeric($port)) {
			$this->setting->bbin_port = $port;
			$sendto->reply("Setting saved.  Bot will use port {$port} to connect to the IRC server.");
		} else {
			$sendto->reply("The port should be a number.");
		}
	}
	
	/**
	 * @HandlesCommand("setbbin")
	 * @Matches("/^setbbin nickname (.+)$/i")
	 */
	public function setBBINNicknameCommand($message, $channel, $sender, $sendto, $args) {
		$nickname = trim($args[1]);
		$this->setting->bbin_nickname = $nickname;
		$sendto->reply("Setting saved.  Bot will use $nickname as its nickname while in IRC.");
	}
	
	/**
	 * @HandlesCommand("setbbin")
	 * @Matches("/^setbbin channel (.+)$/i")
	 */
	public function setBBINChannelCommand($message, $channel, $sender, $sendto, $args) {
		$channel = trim($args[1]);
		if (strpos($channel, " ") !== false) {
			$msg = "IRC channels cannot have spaces in them";
		} else {
			if (strpos($channel, "#") === false) {
				$channel = "#" . $channel;
			}
			$this->setting->bbin_channel = $channel;
			$msg = "Setting saved.  Bot will join $channel when it connects to IRC.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("onlinebbin")
	 * @Matches("/^onlinebbin$/i")
	 */
	public function onlineBBINCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->ircActive()) {
			list($num, $blob) = $this->getOnlineList();
			$msg = $this->text->make_blob("BBIN Online ($num)", $blob);
		} else {
			$msg = "There is no active BBIN connection.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @Event("1min")
	 * @Description("Automatically reconnect to IRC server")
	 * @DefaultStatus("0")
	 */
	public function autoReconnectEvent($eventObj) {
		// make sure eof flag is set
		//fputs($this->ircSocket, "PING ping\n");
		if ($this->setting->bbin_status == '1' && !$this->ircActive()) {
			$this->connect();
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
	
	public function noticeMessage(&$irc, &$obj) {
		if (false != stripos($obj->message, "exiting")) {
			// the irc server shut down (i guess)
			// send notification to channel
			$extendedinfo = $this->text->make_blob("Extended information", $obj->message);
			$msg = "<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
	}
	
	public function kickMessage(&$irc, &$obj) {
		$extendedinfo = $this->text->make_blob("Extended information", $obj->message);
		if ($obj->nick == $this->setting->bbin_nickname) {
			$msg = "<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		} else {
			$this->db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = ?", $obj->nick);
			$msg = "<yellow>[BBIN]<end> Uplink to ".$obj->nick." was kicked from the server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
	}
	
	public function leaveMessage(&$irc, &$obj) {
		$this->db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = ?", $nick);
		$msg = "<yellow>[BBIN]<end> Lost uplink with $obj->nick";
		
		if ($this->chatBot->vars['my_guild'] != "") {
			$this->chatBot->sendGuild($msg, true);
		}
		if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
			$this->chatBot->sendPrivate($msg, true);
		}
	}
	
	public function joinMessage(&$irc, &$obj) {
		$msg = "<yellow>[BBIN]<end> Uplink established with $obj->nick.";

		if ($this->chatBot->vars['my_guild'] != "") {
			$this->chatBot->sendGuild($msg, true);
		}
		if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
			$this->chatBot->sendPrivate($msg, true);
		}
	}
	
	public function channelMessage(&$irc, &$obj) {
		$this->logger->log_chat("Inc. BBIN Msg.", $obj->nick, $obj->message);
		$this->parse_incoming_bbin($obj->message, $obj->nick);
	}
	
	/**
	 * @Event("priv")
	 * @Description("Relay (priv) messages to BBIN")
	 */
	public function relayPrivMessagesEvent($eventObj) {
		$message = $eventObj->message;
		$sender = $eventObj->sender;
		if ($this->ircActive()) {
			// do not relay commands and ignored chars
			if ($message[0] != $this->setting->symbol) {
				$outmsg = htmlspecialchars($message);

				$msg = "$sender: $message";
				$this->logger->log_chat("Out. BBIN Msg.", $sender, $msg);
				$this->sendMessageToBBIN($msg);
			}
		}
	}
	
	/**
	 * @Event("guild")
	 * @Description("Relay (guild) messages to BBIN")
	 */
	public function relayGuildMessagesEvent($eventObj) {
		$message = $eventObj->message;
		$sender = $eventObj->sender;
		if ($this->ircActive()) {
			// do not relay commands and ignored chars
			if ($message[0] != $this->setting->symbol) {
				$outmsg = htmlspecialchars($message);

				$msg = "$sender: $message";
				$this->logger->log_chat("Out. BBIN Msg.", $sender, $msg);
				$this->sendMessageToBBIN($msg);
			}
		}
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Sends joined channel messages")
	 */
	public function joinPrivEvent($eventObj) {
		if ($this->ircActive()) {
			$msg = "[BBIN:LOGON:".$eventObj->sender.",".$this->chatBot->vars["dimension"].",1]";
			$this->logger->log('DEBUG', $msg);
			$this->sendMessageToBBIN($msg);
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Shows a logon from a member")
	 */
	public function logonEvent($eventObj) {
		if ($this->ircActive() && isset($this->chatBot->guildmembers[$eventObj->sender])) {
			$msg = "[BBIN:LOGON:".$eventObj->sender.",".$this->chatBot->vars["dimension"].",0]";
			$this->logger->log('DEBUG', $msg);
			$this->sendMessageToBBIN($msg);
		}
	}
	
	/**
	 * @Event("leavePriv")
	 * @Description("Sends left channel messages")
	 */
	public function leavePrivEvent($eventObj) {
		if ($this->ircActive()) {
			$msg = "[BBIN:LOGOFF:".$eventObj->sender.",".$this->chatBot->vars["dimension"].",1]";
			$this->logger->log('DEBUG', $msg);
			$this->sendMessageToBBIN($msg);
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Shows a logoff from a member")
	 */
	public function logoffEvent($eventObj) {
		if ($this->ircActive() && isset($this->chatBot->guildmembers[$eventObj->sender])) {
			$msg = "[BBIN:LOGOFF:".$eventObj->sender.",".$this->chatBot->vars["dimension"].",0]";
			$this->logger->log('DEBUG', $msg);
			$this->sendMessageToBBIN($msg);
		}
	}

	/*
	 * This is the main parse function for incoming messages other than
	 * IRC related stuff
	 */
	public function parse_incoming_bbin($bbinmsg, $nick) {
		if (preg_match("/^\[BBIN:LOGON:(.*?),(.),(.)\]/", $bbinmsg, $arr)) {
			// a user logged on somewhere in the network
			// first argument is name, second is dimension, third indicates a guest
			$name = $arr[1];
			$dimension = $arr[2];
			$isguest = $arr[3];

			// get character information
			$character = $this->playerManager->get_by_name($name, $dimension);

			// add user to bbin_chatlist_<myname>
			$sql = "INSERT INTO bbin_chatlist_<myname> (`name`, `guest`, `ircrelay`, `faction`, `profession`, `guild`, `breed`, `level`, `ai_level`, `dimension`, `afk`) " .
				"VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$this->db->exec($sql, $name, $isguest, $nick, $character->faction, $character->profession, $character->guild, $character->breed, $character->level, $character->ai_level, $dimension, '');

			// send notification to channels
			$msg = "<highlight>$name<end> (<highlight>{$character->level}<end>/<green>{$character->ai_level}<end>, <highlight>{$character->profession}<end>, {$character->faction})";
			if ($character->guild != "") {
				$msg .=	" {$character->guild_rank} of {$character->guild}";
			}
			$msg .= " has joined the network";
			if ($isguest == 1) {
				$msg .= " as a guest";
			}
			$msg .= ".";

			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild("<yellow>[BBIN]<end> $msg", true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
				$this->chatBot->sendPrivate("<yellow>[BBIN]<end> $msg", true);
			}

		} else if (preg_match("/^\[BBIN:LOGOFF:(.*?),(.),(.)\]/", $bbinmsg, $arr)) {
			// a user logged off somewhere in the network
			$name = $arr[1];
			$dimension = $arr[2];
			$isguest = $arr[3];

			// delete user from bbin_chatlist table
			$this->db->exec("DELETE FROM bbin_chatlist_<myname> WHERE (`name` = ?) AND (`dimension` = ?) AND (`ircrelay` = ?)", $name, $dimension, $nick);

			// send notification to channels
			$msg = "";
			if ($isguest == 1) {
				$msg = "Our guest ";
			}
			$msg .= "<highlight>$name<end> has left the network.";


			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild("<yellow>[BBIN]<end> $msg", true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
				$this->chatBot->sendPrivate("<yellow>[BBIN]<end> $msg", true);
			}

		} else if (preg_match("/^\[BBIN:SYNCHRONIZE\]/",$bbinmsg)) {
			// a new bot joined and requested a full online synchronization

			// send actual online members
			$msg = "[BBIN:ONLINELIST:".$this->chatBot->vars["dimension"].":";
			$data = $this->db->query("SELECT name FROM online WHERE channel_type = 'guild' AND added_by = '<myname>'");
			$numrows = count($data);
			forEach ($data as $row) {
				$msg .= $row->name . ",0,";
			}

			$data = $this->db->query("SELECT * FROM online WHERE channel_type = 'priv' AND added_by = '<myname>'");
			$numrows += count($data);
			forEach ($data as $row) {
				$msg .= $row->name . ",1,";
			}
			if ($numrows != 0) {
				// remove trailing , if there is one
				$msg = substr($msg,0,strlen($msg)-1);
			}

			$msg .= "]";

			// send complete list back to bbin channel
			fputs($this->bbinSocket, "PRIVMSG ".$this->setting->bbin_channel." :$msg\n");

		} else if (preg_match("/^\[BBIN:ONLINELIST:(.):(.*?)\]/", $bbinmsg, $arr)) {
			// received a synchronization list

			$this->db->begin_transaction();

			// delete all buddies from that nick
			$this->db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = ?", $nick);

			// Format: [BBIN:ONLINELIST:dimension:name,isguest,name,isguest....]
			$dimension = $arr[1];
			$listplode = explode(',', $arr[2]);

			// listplode should be: {name,isguest,name,isguest ...}
			while (true) {
				// as using array_pop will lead to null some time,
				// this loop will exit when all chars are parsed

				// pop last value off array (isguest of last member)
				$isguest = array_pop($listplode);

				// pop next value off array (name of last member)
				$name = array_pop($listplode);

				if ($isguest == null || $name == null) {
					// we popped all items of the array, break
					break;
				}

				// get character information
				$character = $this->playerManager->get_by_name($name, $dimension);

				// add user to bbin_chatlist_<myname>
				$sql = "INSERT INTO bbin_chatlist_<myname> (`name`, `guest`, `ircrelay`, `faction`, `profession`, `guild`, `breed`, `level`, `ai_level`, `dimension`, `afk`) " .
					"VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$this->db->exec($sql, $name, $isguest, $nick, $character->faction, $character->profession, $character->guild, $character->breed, $character->level, $character->ai_level, $dimension, '');
			}

			$this->db->commit();
		} else {
			// normal message
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild("<yellow>[BBIN]<end> $bbinmsg", true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->guest_relay == 1) {
				$this->chatBot->sendPrivate("<yellow>[BBIN]<end> $bbinmsg", true);
			}
		}
	}
	
	public function sendMessageToBBIN($msg) {
		$this->irc->message(SMARTIRC_TYPE_CHANNEL, $this->setting->bbin_channel, $msg);
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
}
