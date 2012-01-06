<?php

class Budabot extends AOChat {

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $command;
	
	/** @Inject */
	public $subcommand;
	
	/** @Inject */
	public $commandAlias;
	
	/** @Inject */
	public $event;
	
	/** @Inject */
	public $help;
	
	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $ban;

	var $buddyList = array();
	var $chatlist = array();
	var $guildmembers = array();
	
	var $events = array();
	var $helpfiles = array();
	var $subcommands = array();
	
	// array where modules can store stateful session data
	var $data = array();
	
	//Ignore Messages from Vicinity/IRRK New Wire/OT OOC/OT Newbie OOC...
	var $channelsToIgnore = array("", 'IRRK News Wire', 'OT OOC', 'OT Newbie OOC', 'OT Jpn OOC', 'OT shopping 11-50',
		'Tour Announcements', 'Neu. Newbie OOC', 'Neu. Jpn OOC', 'Neu. shopping 11-50', 'Neu. OOC', 'Clan OOC',
		'Clan Newbie OOC', 'Clan Jpn OOC', 'Clan shopping 11-50', 'OT German OOC', 'Clan German OOC', 'Neu. German OOC');

	function __construct(&$vars){
		parent::__construct();

		$this->vars = $vars;
		
		// don't fire logon events when the bot starts up
		$this->vars["logondelay"] = time() + 100000;

		// Set startup time
		$this->vars["startup"] = time();
	}

	/**
	 * @name: connect
	 * @description: connect to AO chat servers
	 */
	function connectAO($login, $password, $server, $port){
		// Begin the login process
		Logger::log('INFO', 'StartUp', "Connecting to AO Server...({$server}:{$port})");
		$this->connect($server, $port);
		if ($this->state != "auth") {
			Logger::log('ERROR', 'StartUp', "Connection failed! Please check your Internet connection and firewall.");
			sleep(10);
			die();
		}

		Logger::log('INFO', 'StartUp', "Authenticate login data...");
		$this->authenticate($login, $password);
		if ($this->state != "login") {
			Logger::log('ERROR', 'StartUp', "Authentication failed! Invalid username or password.");
			sleep(10);
			die();
		}

		Logger::log('INFO', 'StartUp', "Logging in {$this->vars["name"]}...");
		$this->login($this->vars["name"]);
		if ($this->state != "ok") {
			Logger::log('ERROR', 'StartUp', "Character selection failed! Could not login on as character '{$this->vars["name"]}'.");
			sleep(10);
			die();
		}

		Logger::log('INFO', 'StartUp', "All Systems ready!");
		
		Logger::log('DEBUG', 'Core', "Setting logondelay to '" . $this->setting->get("logon_delay") . "'");
		$this->vars["logondelay"] = time() + $this->setting->get("logon_delay");
	}
	
	public function run() {
		$start = time();
		$exec_connected_events = false;
		$time = 0;
		while (true) {
			$this->wait_for_packet();
			if ($this->is_ready()) {
				if ($exec_connected_events == false)	{
					$this->event->executeConnectEvents();
					$exec_connected_events = true;
				}
				
				// execute crons at most once every second
				if ($time < time()) {
					$this->event->crons();
					$time = time();
				}
			}
		}
	}
	
	function init() {
		Logger::log('DEBUG', 'Core', 'Initializing bot');
		
		// Create core tables if not exists
		$this->db->exec("CREATE TABLE IF NOT EXISTS cmdcfg_<myname> (`module` VARCHAR(50), `cmdevent` VARCHAR(6), `type` VARCHAR(18), `file` VARCHAR(255), `cmd` VARCHAR(25), `admin` VARCHAR(10), `description` VARCHAR(50) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `dependson` VARCHAR(25) DEFAULT 'none', `help` VARCHAR(25))");
		$this->db->exec("CREATE TABLE IF NOT EXISTS eventcfg_<myname> (`module` VARCHAR(50), `type` VARCHAR(18), `file` VARCHAR(255), `description` VARCHAR(50) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `help` VARCHAR(25))");
		$this->db->exec("CREATE TABLE IF NOT EXISTS settings_<myname> (`name` VARCHAR(30) NOT NULL, `module` VARCHAR(50), `type` VARCHAR(30), `mode` VARCHAR(10), `value` VARCHAR(255) DEFAULT '0', `options` VARCHAR(255) DEFAULT '0', `intoptions` VARCHAR(50) DEFAULT '0', `description` VARCHAR(50), `source` VARCHAR(5), `admin` VARCHAR(25), `verify` INT DEFAULT '0', `help` VARCHAR(25))");
		$this->db->exec("CREATE TABLE IF NOT EXISTS hlpcfg_<myname> (`name` VARCHAR(25) NOT NULL, `module` VARCHAR(50), `file` VARCHAR(255), `description` VARCHAR(50), `admin` VARCHAR(10), `verify` INT DEFAULT '0')");
		$this->db->exec("CREATE TABLE IF NOT EXISTS cmd_alias_<myname> (`cmd` VARCHAR(25) NOT NULL, `module` VARCHAR(50), `alias` VARCHAR(25) NOT NULL, `status` INT DEFAULT '0')");
		
		// Delete old vars in case they exist
		$this->helpfiles = array();
		$this->subcommands = array();
		$this->cmd_aliases = array();
		
		// Prepare command/event settings table
		$this->db->exec("UPDATE cmdcfg_<myname> SET `verify` = 0");
		$this->db->exec("UPDATE eventcfg_<myname> SET `verify` = 0");
		$this->db->exec("UPDATE settings_<myname> SET `verify` = 0");
		$this->db->exec("UPDATE hlpcfg_<myname> SET `verify` = 0");
		$this->db->exec("UPDATE eventcfg_<myname> SET `status` = 1 WHERE `type` = 'setup'");

		// To reduce queries load core items into memory
		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd'");
		forEach ($data as $row) {
		  	$this->existing_commands[$row->type][$row->cmd] = true;
		}

		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'subcmd'");
		forEach ($data as $row) {
		  	$this->existing_subcmds[$row->type][$row->cmd] = true;
		}

		$data = $this->db->query("SELECT * FROM eventcfg_<myname>");
		forEach ($data as $row) {
			$this->existing_events[$row->type][$row->file] = true;
		}

		$data = $this->db->query("SELECT * FROM hlpcfg_<myname>");
		forEach ($data as $row) {
		  	$this->existing_helps[$row->name] = true;
		}

		$data = $this->db->query("SELECT * FROM settings_<myname>");
		forEach ($data as $row) {
		  	$this->existing_settings[$row->name] = true;
		}
		
		$data = $this->db->query("SELECT * FROM cmd_alias_<myname>");
		forEach ($data as $row) {
		  	$this->existing_cmd_aliases[$row->alias] = true;
		}
		
		$this->loadCoreModules();

		Logger::log('INFO', 'Core', "Loading USER modules...");

		//Load user modules
		$this->loadModules();
		
		//remove arrays
		unset($this->existing_commands);
		unset($this->existing_events);
		unset($this->existing_subcmds);
		unset($this->existing_settings);
		unset($this->existing_helps);
		unset($this->existing_cmd_aliases);
		
		//Delete old entrys in the DB
		$this->db->exec("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0");
		$this->db->exec("DELETE FROM eventcfg_<myname> WHERE `verify` = 0");
		$this->db->exec("DELETE FROM settings_<myname> WHERE `verify` = 0");
		$this->db->exec("DELETE FROM hlpcfg_<myname> WHERE `verify` = 0");

		$this->command->loadCommands();
		$this->subcommand->loadSubcommands();
		$this->commandAlias->load();
		$this->event->loadEvents();
	}

	function sendPrivate($message, $group, $disable_relay = false) {
		// for when Text::make_blob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->sendPrivate($page, $group, $disable_relay);
			}
			return;
		}
	
		$message = Text::format_message($message);
		$this->send_privgroup($group, $this->setting->get("default_priv_color").$message);
	}

	/**
	 * @name: send
	 * @description: format a message and send it to private channel, guild channel, or a player
	 */
	function send($message, $target, $disable_relay = false, $priority = null) {
		if ($target == null) {
			Logger::log('ERROR', 'Core', "Could not send message as no target was specified. message: '{$message}'");
			return;
		}

		// for when Text::make_blob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->send($page, $target, $disable_relay, $priority);
			}
			return;
		}
		
		if ($target instanceof ClientHandler) {
			if ($message instanceof APIResponse) {
				$target->writePacket($message);
			} else {
				$target->writePacket(new APIResponse(API_SUCCESS, $message));
			}
			return;
		}

		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}

		if ($target == 'guild') {
			$target = 'org';
		}
		if ($target == 'priv') {
			$target = 'prv';
		}

		$message = Text::format_message($message);
		$sender_link = Text::make_userlink($this->vars['name']);

		if ($target == 'prv') {
			$this->send_privgroup($this->vars["name"], $this->setting->get("default_priv_color").$message);
			
			// relay to guild channel
			if (!$disable_relay && $this->setting->get('guild_channel_status') == 1 && $this->setting->get("guest_relay") == 1 && $this->setting->get("guest_relay_commands") == 1) {
				$this->send_guild("</font>{$this->setting->get('guest_color_channel')}[Guest]</font> {$sender_link}: {$this->setting->get('default_priv_color')}$message</font>", "\0", $priority);
			}

			// relay to bot relay
			if (!$disable_relay && $this->setting->get("relaybot") != "Off" && $this->setting->get("bot_relay_commands") == 1) {
				send_message_to_relay("grc <grey>[{$this->vars["my_guild"]}] [Guest] {$sender_link}: $message");
			}
		} else if (($target == $this->vars["my_guild"] || $target == 'org') && $this->setting->get('guild_channel_status') == 1) {
    		$this->send_guild($this->setting->get("default_guild_color").$message, "\0", $priority);
			
			// relay to private channel
			if (!$disable_relay && $this->setting->get("guest_relay") == 1 && $this->setting->get("guest_relay_commands") == 1) {
				$this->send_privgroup($this->vars["name"], "</font>{$this->setting->get('guest_color_channel')}[{$this->vars["my_guild"]}]</font> {$sender_link}: {$this->setting->get('default_guild_color')}$message</font>");
			}
			
			// relay to bot relay
			if (!$disable_relay && $this->setting->get("relaybot") != "Off" && $this->setting->get("bot_relay_commands") == 1) {
				send_message_to_relay("grc <grey>[{$this->vars["my_guild"]}] {$sender_link}: $message");
			}
		} else if ($this->get_uid($target) != NULL) {// Target is a player.
			Logger::log_chat("Out. Msg.", $target, $message);
    		$this->send_tell($target, $this->setting->get("default_tell_color").$message, "\0", $priority);
		} else { // Public channels that are not guild
	    	$this->send_group($target, $this->setting->get("default_guild_color").$message, "\0", $priority);
		}
	}
	
	function loadCoreModules() {
		$chatBot = Registry::getInstance('chatBot');
		$db = $this->db;
		$command = $this->command;
		$subcommand = $this->subcommand;
		$event = $this->event;
		$help = $this->help;
		$setting = $this->setting;
		$commandAlias = $this->commandAlias;

		// Load the Core Modules -- SETINGS must be first in case the other modules have settings
		Logger::log('INFO', 'Core', "Loading CORE modules...");
		$core_modules = array('SETTINGS', 'SYSTEM', 'ADMIN', 'BAN', 'HELP', 'CONFIG', 'LIMITS', 'PLAYER_LOOKUP', 'FRIENDLIST', 'ALTS', 'USAGE', 'PREFERENCES', 'API_MODULE');
		$this->db->begin_transaction();
		forEach ($core_modules as $MODULE_NAME) {
			Logger::log('DEBUG', 'Core', "MODULE_NAME:({$MODULE_NAME}.php)");
			require "./core/{$MODULE_NAME}/{$MODULE_NAME}.php";
		}
		$this->db->commit();
	}

	/**
	 * @name: loadModules
	 * @description: load all user modules
	 */
	function loadModules(){
		$chatBot = Registry::getInstance('chatBot');
		$db = $this->db;
		$command = $this->command;
		$subcommand = $this->subcommand;
		$event = $this->event;
		$help = $this->help;
		$setting = $this->setting;
		$commandAlias = $this->commandAlias;

		if ($d = dir("./modules")) {
			$this->db->begin_transaction();
			while (false !== ($MODULE_NAME = $d->read())) {
				// filters out ., .., .svn
				if (!is_dir($MODULE_NAME)) {
					// Look for the plugin's declaration file
					if (file_exists("./modules/{$MODULE_NAME}/{$MODULE_NAME}.php")) {
						Logger::log('DEBUG', 'Core', "MODULE_NAME:({$MODULE_NAME}.php)");
						require "./modules/{$MODULE_NAME}/{$MODULE_NAME}.php";
					} else {
						Logger::log('ERROR', 'Core', "Could not load module {$MODULE_NAME}. {$MODULE_NAME}.php does not exist!");
					}
				}
			}
			$d->close();
			$this->db->commit();
		}
	}

	/**
	 * @name: processCommandType
	 * @description: returns a command type in the proper format
	 */
	function processCommandArgs(&$type, &$admin) {
		if ($type == "") {
			$type = array("msg", "priv", "guild");
		} else {
			$type = explode(' ', $type);
		}

		$admin = explode(' ', $admin);
		if (count($admin) == 1) {
			$admin = array_fill(0, count($type), $admin[0]);
		} else if (count($admin) != count($type)) {
			Logger::log('ERROR', 'Core', "ERROR! the number of type arguments does not equal the number of admin arguments for command/subcommand registration!");
			return false;
		}
		return true;
	}

	/**
	 * @name: process_packet
	 * @description: Proccess all incoming messages that bot recives
	 */	
	function process_packet($packet) {
		$this->process_all_packets($packet->type, $packet->args);
		
		// event handlers
		switch ($packet->type){
			case AOCP_GROUP_ANNOUNCE: // 60
				$this->process_group_announce($packet->args);
				break;
			case AOCP_PRIVGRP_CLIJOIN: // 55, Incoming player joined private chat
				$this->process_private_channel_join($packet->args);
				break;
			case AOCP_PRIVGRP_CLIPART: // 56, Incoming player left private chat
				$this->process_private_channel_leave($packet->args);
				break;
			case AOCP_BUDDY_ADD: // 40, Incoming buddy logon or off
				$this->process_buddy_update($packet->args);
				break;
			case AOCP_MSG_PRIVATE: // 30, Incoming Msg
				$this->process_private_message($packet->args);
				break;
			case AOCP_PRIVGRP_MESSAGE: // 57, Incoming priv message
				$this->process_private_channel_message($packet->args);
				break;
			case AOCP_GROUP_MESSAGE: // 65, Public and guild channels
				$this->process_public_channel_message($packet->args);
				break;
			case AOCP_PRIVGRP_INVITE: // 50, private channel invite
				$this->process_private_channel_invite($packet->args);
				break;
		}
	}
	
	function process_all_packets($packet_type, $args) {
		$eventObj = new stdClass;
		$eventObj->type = 'allpackets';
		$this->event->fireEvent($eventObj);
	}
	
	function process_group_announce($args) {
		$b = unpack("C*", $args[0]);
		Logger::log('DEBUG', 'Packets', "AOCP_GROUP_ANNOUNCE => name: '$args[1]'");
		if ($b[1] == 3) {
			$this->vars["my_guild_id"] = ($b[2] << 24) + ($b[3] << 16) + ($b[4] << 8) + ($b[5]);
			//$this->vars["my_guild"] = $args[1];
		}
	}
	
	function process_private_channel_join($args) {
		$eventObj = new stdClass;
		$channel = $this->lookup_user($args[0]);
		$sender = $this->lookup_user($args[1]);
		$eventObj->channel = $channel;
		$eventObj->sender = $sender;

		Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_CLIJOIN => channel: '$channel' sender: '$sender'");
		
		if ($channel == $this->vars['name']) {
			$eventObj->type = "joinpriv";

			Logger::log_chat("Priv Group", -1, "$sender joined the channel.");

			// Remove sender if they are banned or if spam filter is blocking them
			if ($this->ban->is_banned($sender) || $this->spam[$sender] > 100){
				$this->privategroup_kick($sender);
				return;
			}

			// Add sender to the chatlist.
			$this->chatlist[$sender] = true;

			$this->event->fireEvent($eventObj);
		} else {
			$eventObj->type = "extjoinpriv";
			$this->event->fireEvent($eventObj);
		}
	}
	
	function process_private_channel_leave($args) {
		$eventObj = new stdClass;
		$channel = $this->lookup_user($args[0]);
		$sender = $this->lookup_user($args[1]);
		$eventObj->channel = $channel;
		$eventObj->sender = $sender;
		
		Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_CLIPART => channel: '$channel' sender: '$sender'");
		
		if ($channel == $this->vars['name']) {
			$eventObj->type = "leavepriv";
		
			Logger::log_chat("Priv Group", -1, "$sender left the channel.");

			// Remove from Chatlist array.
			unset($this->chatlist[$sender]);
			
			$this->event->fireEvent($eventObj);
		} else {
			$eventObj->type = "extleavepriv";
			
			$this->event->fireEvent($eventObj);
		}
	}
	
	function process_buddy_update($args) {
		$sender	= $this->lookup_user($args[0]);
		$status	= 0 + $args[1];

		$eventObj = new stdClass;
		$eventObj->sender = $sender;
		
		Logger::log('DEBUG', 'Packets', "AOCP_BUDDY_ADD => sender: '$sender' status: '$status'");
		
		// store buddy info
		list($bid, $bonline, $btype) = $args;
		$this->buddyList[$bid]['uid'] = $bid;
		$this->buddyList[$bid]['name'] = $sender;
		$this->buddyList[$bid]['online'] = ($bonline ? 1 : 0);
		$this->buddyList[$bid]['known'] = (ord($btype) ? 1 : 0);

		// Ignore Logon/Logoff from other bots or phantom logon/offs
		if ($sender == "") {
			return;
		}

		// Status => 0: logoff  1: logon
		if ($status == 0) {
			$eventObj->type = "logoff";
			
			Logger::log('DEBUG', "Buddy", "$sender logged off");

			$this->event->fireEvent($eventObj);
		} else if ($status == 1) {
			$eventObj->type = "logon";
			
			Logger::log('INFO', "Buddy", "$sender logged on");

			$this->event->fireEvent($eventObj);
		}
	}
	
	function process_private_message($args) {
		$type = "msg";
		$sender	= $this->lookup_user($args[0]);
		$sendto = $sender;
		
		Logger::log('DEBUG', 'Packets', "AOCP_MSG_PRIVATE => sender: '$sender' message: '$args[1]'");
		
		// Removing tell color
		if (preg_match("/^<font color='#([0-9a-f]+)'>(.+)$/si", $args[1], $arr)) {
			$message = $arr[2];
		} else {
			$message = $args[1];
		}
		
		$eventObj = new stdClass;
		$eventObj->sender = $sender;
		$eventObj->type = $type;
		$eventObj->message = $message;

		Logger::log_chat("Inc. Msg.", $sender, $message);

		// AFK/bot check
		if (preg_match("/$sender is AFK/si", $message, $arr)) {
			return;
		} else if (preg_match("/I am away from my keyboard right now/si", $message)) {
			return;
		} else if (preg_match("/Unknown command or access denied!/si", $message, $arr)) {
			return;
		} else if (preg_match("/I am responding/si", $message, $arr)) {
			return;
		} else if (preg_match("/I only listen/si", $message, $arr)) {
			return;
		} else if (preg_match("/Error!/si", $message, $arr)) {
			return;
		} else if (preg_match("/Unknown command input/si", $message, $arr)) {
			return;
		}

		if ($this->ban->is_banned($sender)) {
			return;
		} else if ($this->setting->get('spam_protection') == 1 && $this->spam[$sender] > 100) {
			$this->spam[$sender] += 20;
			return;
		}
		
		$this->event->fireEvent($eventObj);

		// remove the symbol if there is one
		if ($message[0] == $this->setting->get("symbol") && strlen($message) > 1) {
			$message = substr($message, 1);
		}

		// check tell limits
		$limits = Registry::getInstance('Limits');
		if (!$limits->check($sender, $message)) {
			return;
		}
		
		$this->command->process($type, $message, $sender, $sendto);
	}
	
	function process_private_channel_message($args) {
		$sender	= $this->lookup_user($args[1]);
		$sendto = 'prv';
		$channel = $this->lookup_user($args[0]);
		$message = $args[2];
		
		$eventObj = new stdClass;
		$eventObj->sender = $sender;
		$eventObj->channel = $channel;
		$eventObj->message = $message;
		
		Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_MESSAGE => sender: '$sender' channel: '$channel' message: '$message'");
		Logger::log_chat($channel, $sender, $message);
		
		if ($sender == $this->vars["name"] || $this->ban->is_banned($sender)) {
			return;
		}

		if ($this->setting->get('spam_protection') == 1) {
			if ($this->spam[$sender] == 40) $this->send("Error! Your client is sending a high frequency of chat messages. Stop or be kicked.", $sender);
			if ($this->spam[$sender] > 60) $this->privategroup_kick($sender);
			if (strlen($args[1]) > 400){
				$this->largespam[$sender] = $this->largespam[$sender] + 1;
				if ($this->largespam[$sender] > 1) {
					$this->privategroup_kick($sender);
				}
				if ($this->largespam[$sender] > 0) {
					$this->send("Error! Your client is sending large chat messages. Stop or be kicked.", $sender);
				}
			}
		}

		if ($channel == $this->vars['name']) {
			$type = "priv";
			$eventObj->type = $type;

			$this->event->fireEvent($eventObj);
			
			if ($message[0] == $this->setting->get("symbol") && strlen($message) > 1) {
				$message = substr($message, 1);
				$this->command->process($type, $message, $sender, $sendto);
			}
		} else {  // ext priv group message
			$type = "extpriv";
			$eventObj->type = $type;
			
			$this->event->fireEvent($eventObj);
		}
	}
	
	function process_public_channel_message($args) {
		$sender	 = $this->lookup_user($args[1]);
		$message = $args[2];
		$channel = $this->get_gname($args[0]);
		
		$eventObj = new stdClass;
		$eventObj->sender = $sender;
		$eventObj->channel = $channel;
		$eventObj->message = $message;
		
		Logger::log('DEBUG', 'Packets', "AOCP_GROUP_MESSAGE => sender: '$sender' channel: '$channel' message: '$message'");

		if (in_array($channel, $this->channelsToIgnore)) {
			return;
		}

		// don't log tower messages with rest of chat messages
		if ($channel != "All Towers" && $channel != "Tower Battle Outcome") {
			Logger::log_chat($channel, $sender, $message);
		} else {
			Logger::log('DEBUG', $channel, $message);
		}

		if ($sender) {
			// Ignore Message that are sent from the bot self
			if ($sender == $this->vars["name"]) {
				return;
			}
			if ($this->ban->is_banned($sender)) {
				return;
			}
		}
		
		$b = unpack("C*", $args[0]);

		if ($channel == "All Towers" || $channel == "Tower Battle Outcome") {
			$eventObj->type = "towers";
			
			$this->event->fireEvent($eventObj);
		} else if ($channel == "Org Msg"){
			$eventObj->type = "orgmsg";

			$this->event->fireEvent($eventObj);
		} else if ($b[1] == 3 && $this->setting->get('guild_channel_status') == 1) {
			$type = "guild";
			$sendto = 'guild';
			
			$eventObj->type = $type;
			
			$this->event->fireEvent($eventObj);
			
			if ($message[0] == $this->setting->get("symbol") && strlen($message) > 1) {
				$message = substr($message, 1);
				$this->command->process($type, $message, $sender, $sendto);
			}
		}
	}
	
	function process_private_channel_invite($args) {
		$type = "extjoinprivrequest"; // Set message type.
		$uid = $args[0];
		$sender = $this->lookup_user($uid);
		
		$eventObj = new stdClass;
		$eventObj->sender = $sender;
		$eventObj->type = $type;

		Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_INVITE => sender: '$sender'");

		Logger::log_chat("Priv Channel Invitation", -1, "$sender channel invited.");

		$this->event->fireEvent($eventObj);
	}
	
	public function registerInstance($MODULE_NAME, $name, &$obj) {
		$name = strtolower($name);
		Logger::log('DEBUG', 'CORE', "Registering instance name '$name' for module '$MODULE_NAME'");
		if (Registry::instanceExists($name)) {
			Logger::log('WARN', 'CORE', "Instance with name '$name' already registered--replaced with new instance");
		}
		Registry::setInstance($name, $obj);

		// register settings annotated on the class
		$reflection = new ReflectionAnnotatedClass($obj);
		forEach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation('Setting')) {
				$this->setting->add(
					$MODULE_NAME,
					$property->getAnnotation('Setting')->value,
					$property->getAnnotation('Description')->value,
					$property->getAnnotation('Visibility')->value,
					$property->getAnnotation('Type')->value,
					$obj->{$property->name},
					$property->getAnnotation('Options')->value,
					$property->getAnnotation('Intoptions')->value,
					$property->getAnnotation('AccessLevel')->value,
					$property->getAnnotation('Help')->value
				);
			}
		}
		
		// register commands, subcommands, and events annotated on the class
		forEach ($reflection->getMethods() as $method) {
			if ($method->hasAnnotation('Command')) {
				$this->command->register(
					$MODULE_NAME,
					$method->getAnnotation('Channels')->value,
					$name . '.' . $method->name,
					$method->getAnnotation('Command')->value,
					$method->getAnnotation('AccessLevel')->value,
					$method->getAnnotation('Description')->value,
					$method->getAnnotation('Help')->value
				);
			}
			if ($method->hasAnnotation('Subcommand')) {
				list($parentCommand) = explode(" ", $method->getAnnotation('Subcommand')->value, 2);
				$this->subcommand->register(
					$MODULE_NAME,
					$method->getAnnotation('Channels')->value,
					$name . '.' . $method->name,
					$method->getAnnotation('Subcommand')->value,
					$method->getAnnotation('AccessLevel')->value,
					$parentCommand,
					$method->getAnnotation('Description')->value,
					$method->getAnnotation('Help')->value
				);
			}
			if ($method->hasAnnotation('Event')) {
				$this->event->register(
					$MODULE_NAME,
					$method->getAnnotation('Event')->value,
					$name . '.' . $method->name,
					$method->getAnnotation('Description')->value,
					$method->getAnnotation('Help')->value,
					$method->getAnnotation('DefaultStatus')->value
				);
			}
		}
	}
	
	/**
	 * @name: is_ready
	 * @description: tells when the bot is logged on and all the start up events have finished
	 */
	public function is_ready() {
		return time() >= $this->vars["logondelay"];
	}
}

?>