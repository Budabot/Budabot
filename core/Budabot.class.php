<?php

class Budabot extends AOChat {

	var $buddyList = array();
	var $chatlist = array();
	var $guildmembers = array();
	
	var $events = array();
	var $helpfiles = array();
	var $subcommands = array();
	
	var $tellCmds = array();
	var $privCmds = array();
	var $guildCmds = array();
	
	// array where modules can store stateful session data
	var $data = array();
	
	//Ignore Messages from Vicinity/IRRK New Wire/OT OOC/OT Newbie OOC...
	var $channelsToIgnore = array("", 'IRRK News Wire', 'OT OOC', 'OT Newbie OOC', 'OT Jpn OOC', 'OT shopping 11-50',
		'Tour Announcements', 'Neu. Newbie OOC', 'Neu. Jpn OOC', 'Neu. shopping 11-50', 'Neu. OOC', 'Clan OOC',
		'Clan Newbie OOC', 'Clan Jpn OOC', 'Clan shopping 11-50', 'OT German OOC', 'Clan German OOC', 'Neu. German OOC');

/*===============================
** Name: __construct
** Constructor of this class.
*/	function __construct(&$vars){
		parent::__construct();

		$this->vars = $vars;
		
		// don't fire logon events when the bot starts up
		$this->vars["logondelay"] = time() + 100000;

		// Set startup time
		$this->vars["startup"] = time();
	}

/*===============================
** Name: connect
** Connect to AO chat servers.
*/	function connectAO($login, $password, $server, $port){
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
		
		Logger::log('DEBUG', 'Core', "Setting logondelay to '" . Setting::get("logon_delay") . "'");
		$this->vars["logondelay"] = time() + Setting::get("logon_delay");
	}
	
	public function run() {
		$start = time();
		$exec_connected_events = false;
		$time = 0;
		while (true) {
			$this->wait_for_packet();
			if ($this->is_ready()) {
				if ($exec_connected_events == false)	{
					Event::executeConnectEvents();
					$exec_connected_events = true;
				}
				
				// execute crons at most once every second
				if ($time < time()) {
					Event::crons();
					$time = time();
				}
			}
		}
	}
	
	function init() {
		Logger::log('DEBUG', 'Core', 'Initializing bot');
		
		$db = DB::get_instance();
		
		// Create core tables if not exists
		$db->exec("CREATE TABLE IF NOT EXISTS cmdcfg_<myname> (`module` VARCHAR(50), `cmdevent` VARCHAR(6), `type` VARCHAR(18), `file` VARCHAR(255), `cmd` VARCHAR(25), `admin` VARCHAR(10), `description` VARCHAR(50) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `dependson` VARCHAR(25) DEFAULT 'none', `help` VARCHAR(25))");
		$db->exec("CREATE TABLE IF NOT EXISTS eventcfg_<myname> (`module` VARCHAR(50), `type` VARCHAR(18), `file` VARCHAR(255), `description` VARCHAR(50) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `help` VARCHAR(25))");
		$db->exec("CREATE TABLE IF NOT EXISTS settings_<myname> (`name` VARCHAR(30) NOT NULL, `module` VARCHAR(50), `type` VARCHAR(30), `mode` VARCHAR(10), `value` VARCHAR(255) Default '0', `options` VARCHAR(255) Default '0', `intoptions` VARCHAR(50) DEFAULT '0', `description` VARCHAR(50), `source` VARCHAR(5), `admin` VARCHAR(25), `verify` INT DEFAULT '0', `help` VARCHAR(25))");
		$db->exec("CREATE TABLE IF NOT EXISTS hlpcfg_<myname> (`name` VARCHAR(25) NOT NULL, `module` VARCHAR(50), `file` VARCHAR(255), `description` VARCHAR(50), `admin` VARCHAR(10), `verify` INT Default '0')");
		$db->exec("CREATE TABLE IF NOT EXISTS cmd_alias_<myname> (`cmd` VARCHAR(25) NOT NULL, `module` VARCHAR(50), `alias` VARCHAR(25) NOT NULL, `status` INT DEFAULT '0')");
		
		// Delete old vars in case they exist
		$this->events = array();
		$this->helpfiles = array();
		$this->subcommands = array();
		$this->cmd_aliases = array();

		$this->commands = array();
		
		unset($this->privMsgs);
		unset($this->privChat);
		unset($this->guildChat);

		// Prepare command/event settings table
		$db->exec("UPDATE cmdcfg_<myname> SET `verify` = 0");
		$db->exec("UPDATE eventcfg_<myname> SET `verify` = 0");
		$db->exec("UPDATE settings_<myname> SET `verify` = 0");
		$db->exec("UPDATE hlpcfg_<myname> SET `verify` = 0");
		$db->exec("UPDATE eventcfg_<myname> SET `status` = 1 WHERE `type` = 'setup'");

		// To reduce queries load core items into memory
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd'");
		$data = $db->fObject('all');
		forEach ($data as $row) {
		  	$this->existing_commands[$row->type][$row->cmd] = true;
		}

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'subcmd'");
		$data = $db->fObject('all');
		forEach ($data as $row) {
		  	$this->existing_subcmds[$row->type][$row->cmd] = true;
		}

		$db->query("SELECT * FROM eventcfg_<myname>");
		$data = $db->fObject('all');
		forEach ($data as $row) {
			$this->existing_events[$row->type][$row->file] = true;
		}

		$db->query("SELECT * FROM hlpcfg_<myname>");
		$data = $db->fObject('all');
		forEach ($data as $row) {
		  	$this->existing_helps[$row->name] = true;
		}

		$db->query("SELECT * FROM settings_<myname>");
		$data = $db->fObject('all');
		forEach ($data as $row) {
		  	$this->existing_settings[$row->name] = true;
		}
		
		$db->query("SELECT * FROM cmd_alias_<myname>");
		$data = $db->fObject('all');
		forEach ($data as $row) {
		  	$this->existing_cmd_aliases[$row->alias] = true;
		}

		// Load the Core Modules -- SETINGS must be first in case the other modules have settings
		Logger::log('INFO', 'Core', "Loading CORE modules...");
		$core_modules = array('SETTINGS', 'SYSTEM', 'ADMIN', 'BAN', 'HELP', 'CONFIG', 'LIMITS', 'PLAYER_LOOKUP', 'FRIENDLIST', 'ALTS', 'USAGE');
		$db->begin_transaction();
		forEach ($core_modules as $MODULE_NAME) {
			Logger::log('DEBUG', 'Core', "MODULE_NAME:({$MODULE_NAME}.php)");
			require "./core/{$MODULE_NAME}/{$MODULE_NAME}.php";
		}
		$db->commit();

		Logger::log('INFO', 'Core', "Loading USER modules...");

		//Load user modules
		$db->begin_transaction();
		$this->loadModules();
		$db->commit();
		
		//remove arrays
		unset($this->existing_commands);
		unset($this->existing_events);
		unset($this->existing_subcmds);
		unset($this->existing_settings);
		unset($this->existing_helps);
		unset($this->existing_cmd_aliases);
		
		//Delete old entrys in the DB
		$db->exec("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0");
		$db->exec("DELETE FROM eventcfg_<myname> WHERE `verify` = 0");
		$db->exec("DELETE FROM settings_<myname> WHERE `verify` = 0");
		$db->exec("DELETE FROM hlpcfg_<myname> WHERE `verify` = 0");

		Command::loadCommands();
		Subcommand::loadSubcommands();
		CommandAlias::load();
		Event::loadEvents();
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
		$this->send_privgroup($group, Setting::get("default_priv_color").$message);
	}

/*===============================
** Name: send
** Send chat messages back to aochat servers thru aochat.
*/	function send($message, $target, $disable_relay = false) {
		if ($target == null) {
			Logger::log('ERROR', 'Core', "Could not send message as no target was specified. message: '{$message}'");
			return;
		}

		// for when Text::make_blob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->send($page, $target, $disable_relay);
			}
			return;
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
			$this->send_privgroup($this->vars["name"], Setting::get("default_priv_color").$message);
			
			// relay to guild channel
			if (!$disable_relay && Setting::get('guild_channel_status') == 1 && Setting::get("guest_relay") == 1 && Setting::get("guest_relay_commands") == 1) {
				$this->send_guild("</font>{$this->settings["guest_color_channel"]}[Guest]</font> {$this->settings["guest_color_username"]}{$sender_link}</font>: {$this->settings["default_priv_color"]}$message</font>");
			}

			// relay to bot relay
			if (!$disable_relay && Setting::get("relaybot") != "Off" && Setting::get("bot_relay_commands") == 1) {
				send_message_to_relay("grc <grey>[{$this->vars["my_guild"]}] [Guest] {$sender_link}: $message");
			}
		} else if (($target == $this->vars["my_guild"] || $target == 'org') && Setting::get('guild_channel_status') == 1) {
    		$this->send_guild(Setting::get("default_guild_color").$message);
			
			// relay to private channel
			if (!$disable_relay && Setting::get("guest_relay") == 1 && Setting::get("guest_relay_commands") == 1) {
				$this->send_privgroup($this->vars["name"], "</font>{$this->settings["guest_color_channel"]}[{$this->vars["my_guild"]}]</font> {$this->settings["guest_color_username"]}{$sender_link}</font>: {$this->settings["default_guild_color"]}$message</font>");
			}
			
			// relay to bot relay
			if (!$disable_relay && Setting::get("relaybot") != "Off" && Setting::get("bot_relay_commands") == 1) {
				send_message_to_relay("grc <grey>[{$this->vars["my_guild"]}] {$sender_link}: $message");
			}
		} else if ($this->get_uid($target) != NULL) {// Target is a player.
			Logger::log_chat("Out. Msg.", $target, $message);
    		$this->send_tell($target, Setting::get("default_tell_color").$message);
		} else { // Public channels that are not guild
	    	$this->send_group($target, Setting::get("default_guild_color").$message);
		}
	}

/*===============================
** Name: loadModules
** Load all Modules
*/	function loadModules(){
		$db = DB::get_instance();
		global $chatBot;

		if ($d = dir("./modules")) {
			while (false !== ($MODULE_NAME = $d->read())) {
				// filters out ., .., .svn
				if (!is_dir($MODULE_NAME)) {
					// Look for the plugin's ... setup file
					if (file_exists("./modules/{$MODULE_NAME}/{$MODULE_NAME}.php")) {
						Logger::log('DEBUG', 'Core', "MODULE_NAME:({$MODULE_NAME}.php)");
						require "./modules/{$MODULE_NAME}/{$MODULE_NAME}.php";
					} else {
						Logger::log('ERROR', 'Core', "Could not load module {$MODULE_NAME}. {$MODULE_NAME}.php does not exist!");
					}
				}
			}
			$d->close();
		}
	}

/*===============================
** Name: processCommandType
** 	Returns a command type in the proper format
*/	function processCommandArgs(&$type, &$admin) {
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

	/*
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
		$db = DB::get_instance();
		global $chatBot;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;

		$type = 'allpackets';

		forEach ($chatBot->events[$type] as $filename) {
			$msg = "";
			include $filename;
			if ($stop_execution) {
				return;
			}
		}
	}
	
	function process_group_announce($args) {
		$db = DB::get_instance();
		global $chatBot;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
	
		$b = unpack("C*", $args[0]);
		Logger::log('DEBUG', 'Packets', "AOCP_GROUP_ANNOUNCE => name: '$args[1]'");
		if ($b[1] == 3) {
			$chatBot->vars["my_guild_id"] = ($b[2] << 24) + ($b[3] << 16) + ($b[4] << 8) + ($b[5]);
			//$this->vars["my_guild"] = $args[1];
		}
	}
	
	function process_private_channel_join($args) {
		$db = DB::get_instance();
		global $chatBot;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
	
		$channel = $chatBot->lookup_user($args[0]);
		$sender = $chatBot->lookup_user($args[1]);

		Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_CLIJOIN => channel: '$channel' sender: '$sender'");
		
		if ($channel == $chatBot->vars['name']) {
			$type = "joinPriv";

			Logger::log_chat("Priv Group", -1, "$sender joined the channel.");

			// Remove sender if they are banned or if spam filter is blocking them
			if (Ban::is_banned($sender) || $chatBot->spam[$sender] > 100){
				$chatBot->privategroup_kick($sender);
				return;
			}

			// Add sender to the chatlist.
			$chatBot->chatlist[$sender] = true;

			// Check files, for all 'player joined channel events'.
			forEach ($chatBot->events[$type] as $filename) {
				$msg = '';
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
		} else {
			$type = "extJoinPriv";
			
			forEach ($chatBot->events[$type] as $filename) {
				$msg = '';
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
		}
	}
	
	function process_private_channel_leave($args) {
		$db = DB::get_instance();
		global $chatBot;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
	
		$channel = $chatBot->lookup_user($args[0]);
		$sender	= $chatBot->lookup_user($args[1]);
		
		Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_CLIPART => channel: '$channel' sender: '$sender'");
		
		if ($channel == $chatBot->vars['name']) {
			$type = "leavePriv";
		
			Logger::log_chat("Priv Group", -1, "$sender left the channel.");

			// Remove from Chatlist array.
			unset($chatBot->chatlist[$sender]);
			
			// Check files, for all 'player left channel events'.
			forEach ($chatBot->events[$type] as $filename) {
				$msg = '';
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
		} else {
			$type = "extLeavePriv";
			
			forEach ($chatBot->events[$type] as $filename) {
				$msg = '';
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
		}
	}
	
	function process_buddy_update($args) {
		$db = DB::get_instance();
		global $chatBot;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
	
		$sender	= $chatBot->lookup_user($args[0]);
		$status	= 0 + $args[1];
		
		Logger::log('DEBUG', 'Packets', "AOCP_BUDDY_ADD => sender: '$sender' status: '$status'");
		
		// store buddy info
		list($bid, $bonline, $btype) = $args;
		$chatBot->buddyList[$bid]['uid'] = $bid;
		$chatBot->buddyList[$bid]['name'] = $sender;
		$chatBot->buddyList[$bid]['online'] = ($bonline ? 1 : 0);
		$chatBot->buddyList[$bid]['known'] = (ord($btype) ? 1 : 0);

		// Ignore Logon/Logoff from other bots or phantom logon/offs
		if ($sender == "") {
			return;
		}

		// Status => 0: logoff  1: logon
		if ($status == 0) {
			$type = "logOff";
			
			Logger::log('DEBUG', "Buddy", "$sender logged off");

			// Check files, for all 'player logged off events'
			forEach ($chatBot->events[$type] as $filename) {
				$msg = "";
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
		} else if ($status == 1) {
			$type = "logOn";
			
			Logger::log('INFO', "Buddy", "$sender logged on");

			// Check files, for all 'player logged on events'.
			forEach ($chatBot->events[$type] as $filename) {
				$msg = "";
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
		}
	}
	
	function process_private_message($args) {
		$db = DB::get_instance();
		global $chatBot;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
		$restricted = false;
	
		$type = "msg";
		$sender	= $chatBot->lookup_user($args[0]);
		$sendto = $sender;
		
		Logger::log('DEBUG', 'Packets', "AOCP_MSG_PRIVATE => sender: '$sender' message: '$args[1]'");
		
		// Removing tell color
		if (preg_match("/^<font color='#([0-9a-f]+)'>(.+)$/si", $args[1], $arr)) {
			$message = $arr[2];
		} else {
			$message = $args[1];
		}

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

		if (Ban::is_banned($sender)) {
			return;
		} else if (Setting::get('spam_protection') == 1 && $chatBot->spam[$sender] > 100) {
			$chatBot->spam[$sender] += 20;
			return;
		}
		
		// Events
		forEach ($chatBot->events[$type] as $filename) {
			$msg = "";
			include $filename;
			if ($stop_execution) {
				return;
			}
		}

		// Remove the symbol if there is one
		if ($message[0] == Setting::get("symbol") && strlen($message) > 1) {
			$message = substr($message, 1);
		}

		// Check private join and tell Limits
		if (file_exists("./core/PRIV_TELL_LIMIT/check.php")) {
			include './core/PRIV_TELL_LIMIT/check.php';
			if ($restricted) {
				return;
			}
		}
		
		$chatBot->process_command($type, $message, $sender, $sendto);
	}
	
	function process_private_channel_message($args) {
		$db = DB::get_instance();
		global $chatBot;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
	
		$sender	= $chatBot->lookup_user($args[1]);
		$sendto = 'prv';
		$channel = $chatBot->lookup_user($args[0]);
		$message = $args[2];
		
		Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_MESSAGE => sender: '$sender' channel: '$channel' message: '$message'");
		Logger::log_chat($channel, $sender, $message);
		
		if ($sender == $chatBot->vars["name"] || Ban::is_banned($sender)) {
			return;
		}

		if (Setting::get('spam_protection') == 1) {
			if ($chatBot->spam[$sender] == 40) $chatBot->send("Error! Your client is sending a high frequency of chat messages. Stop or be kicked.", $sender);
			if ($chatBot->spam[$sender] > 60) $chatBot->privategroup_kick($sender);
			if (strlen($args[1]) > 400){
				$chatBot->largespam[$sender] = $chatBot->largespam[$sender] + 1;
				if ($chatBot->largespam[$sender] > 1) {
					$chatBot->privategroup_kick($sender);
				}
				if ($chatBot->largespam[$sender] > 0) {
					$chatBot->send("Error! Your client is sending large chat messages. Stop or be kicked.", $sender);
				}
			}
		}

		if ($channel == $chatBot->vars['name']) {

			$type = "priv";

			// Events
			forEach ($chatBot->events[$type] as $filename) {
				$msg = "";
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
			
			if ($message[0] == Setting::get("symbol") && strlen($message) > 1) {
				$message = substr($message, 1);
				$chatBot->process_command($type, $message, $sender, $sendto);
			}
		
		} else {  // ext priv group message
			
			$type = "extPriv";
			
			forEach ($chatBot->events[$type] as $filename) {
				$msg = "";
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
		}
	}
	
	function process_public_channel_message($args) {
		$db = DB::get_instance();
		global $chatBot;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
	
		$syntax_error = false;
		$sender	 = $chatBot->lookup_user($args[1]);
		$message = $args[2];
		$channel = $chatBot->get_gname($args[0]);
		
		Logger::log('DEBUG', 'Packets', "AOCP_GROUP_MESSAGE => sender: '$sender' channel: '$channel' message: '$message'");

		if (in_array($channel, $chatBot->channelsToIgnore)) {
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
			if ($sender == $chatBot->vars["name"]) {
				return;
			}
			if (Ban::is_banned($sender)) {
				return;
			}
		}
		
		$b = unpack("C*", $args[0]);

		if ($channel == "All Towers" || $channel == "Tower Battle Outcome") {
			$type = "towers";
			
			forEach ($chatBot->events[$type] as $filename) {
				$msg = "";
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
			return;
		} else if ($channel == "Org Msg"){
			$type = "orgmsg";

			forEach ($chatBot->events[$type] as $filename) {
				$msg = "";
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
			return;
		} else if ($b[1] == 3 && Setting::get('guild_channel_status') == 1) {
			$type = "guild";
			$sendto = 'guild';
			
			// Events
			forEach ($chatBot->events[$type] as $filename) {
				$msg = "";
				include $filename;
				if ($stop_execution) {
					return;
				}
			}
			
			if ($message[0] == Setting::get("symbol") && strlen($message) > 1) {
				$message = substr($message, 1);
				$chatBot->process_command($type, $message, $sender, $sendto);
			}
		}
	}
	
	function process_private_channel_invite($args) {
		$db = DB::get_instance();
		global $chatBot;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
	
		$type = "extJoinPrivRequest"; // Set message type.
		$uid = $args[0];
		$sender = $chatBot->lookup_user($uid);

		Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_INVITE => sender: '$sender'");

		Logger::log_chat("Priv Channel Invitation", -1, "$sender channel invited.");

		forEach ($chatBot->events[$type] as $filename) {
			$msg = "";
			include $filename;
			if ($stop_execution) {
				return;
			}
		}
	}
	
	function process_command($type, $message, $sender, $sendto) {
		$db = DB::get_instance();
		global $chatBot;
		
		// Admin Code
		list($cmd, $params) = explode(' ', $message, 2);
		$cmd = strtolower($cmd);
		
		// Check if this is an alias for a command
		if (isset($chatBot->cmd_aliases[$cmd])) {
			Logger::log('DEBUG', 'Core', "Command alias found command: '{$chatBot->cmd_aliases[$cmd]}' alias: '{$cmd}'");
			$cmd = $chatBot->cmd_aliases[$cmd];
			if ($params) {
				$message = $cmd . ' ' . $params;
			} else {
				$message = $cmd;
			}
			$chatBot->process_command($type, $message, $sender, $sendto);
			return;
		}
		
		$admin = $chatBot->commands[$type][$cmd]["admin"];
		$filename = $chatBot->commands[$type][$cmd]["filename"];

		// Check if a subcommands for this exists
		if (isset($chatBot->subcommands[$cmd])) {
			forEach ($chatBot->subcommands[$cmd] as $row) {
				if ($row->type == $type && preg_match("/^{$row->cmd}$/i", $message)) {
					$admin = $row->admin;
					$filename = $row->file;
				}
			}
		}

		// if file doesn't exist or the character doesn't have access
		if ($filename == "" || AccessLevel::check_access($sender, $admin) !== true) {
			// if they've disabled feedback for guild or private channel, just return
			if ((Setting::get('guild_channel_cmd_feedback') == 0 && $type == 'guild') || ((Setting::get('private_channel_cmd_feedback') == 0 && $type == 'priv'))) {
				return;
			}
				
			$chatBot->send("Error! Unknown command or Access denied.", $sendto);
			$chatBot->spam[$sender] = $chatBot->spam[$sender] + 20;
			return;
		} else {
			if ($cmd != 'grc' && Setting::get('record_usage_stats') == 1) {
				Usage::record($type, $cmd, $sender);
			}
		
			$syntax_error = false;
			$msg = "";
			include $filename;
			if ($syntax_error == true) {
				$results = Command::get($cmd, $type);
				$result = $results[0];
				if ($result->help != '') {
					$blob = Help::find($result->help, $sender);
					$helpcmd = ucfirst($result->help);
				} else {
					$blob = Help::find($cmd, $sender);
					$helpcmd = ucfirst($cmd);
				}
				if ($blob !== false) {
					$msg = Text::make_blob("Help ($helpcmd)", $blob);
					$chatBot->send($msg, $sendto);
				} else {
					$chatBot->send("Error! Invalid syntax for this command.", $sendto);
				}
			}
			$chatBot->spam[$sender] = $chatBot->spam[$sender] + 10;
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