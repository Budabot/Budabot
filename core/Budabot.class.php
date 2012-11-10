<?php

require_once 'AOChat.class.php';

/**
 * @Instance("chatBot")
 */
class Budabot extends AOChat {

	/** @Inject */
	public $db;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $subcommandManager;

	/** @Inject */
	public $commandAlias;

	/** @Inject */
	public $eventManager;

	/** @Inject */
	public $help;

	/** @Inject */
	public $setting;

	/** @Inject */
	public $ban;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $timer;
	
	/** @Inject */
	public $limits;

	/** @Inject */
	public $buddylistManager;
	
	/** @Inject */
	public $socketManager;
	
	/** @Inject */
	public $relayController;

	/** @Logger("Core") */
	public $logger;

	public $ready = false;

	var $chatlist = array();
	var $guildmembers = array();

	// array where modules can store stateful session data
	var $data = array();

	//Ignore Messages from Vicinity/IRRK New Wire/OT OOC/OT Newbie OOC...
	var $channelsToIgnore = array("", 'IRRK News Wire', 'OT OOC', 'OT Newbie OOC', 'OT Jpn OOC', 'OT shopping 11-50',
		'Tour Announcements', 'Neu. Newbie OOC', 'Neu. Jpn OOC', 'Neu. shopping 11-50', 'Neu. OOC', 'Clan OOC',
		'Clan Newbie OOC', 'Clan Jpn OOC', 'Clan shopping 11-50', 'OT German OOC', 'Clan German OOC', 'Neu. German OOC');

	private $setupHandlers = array();
	
	public function init(&$vars) {
		$this->vars = $vars;

		// Set startup time
		$this->vars["startup"] = time();

		// set default value for module load paths if not set correctly
		if (!isset($this->vars['module_load_paths']) || !is_array($this->vars['module_load_paths'])) {
			$this->vars['module_load_paths'] = array('./modules');
		}
	
		$this->logger->log('DEBUG', 'Initializing bot');

		// Create core tables if not exists
		$this->db->exec("CREATE TABLE IF NOT EXISTS cmdcfg_<myname> (`module` VARCHAR(50), `cmdevent` VARCHAR(6), `type` VARCHAR(18), `file` TEXT, `cmd` VARCHAR(25), `admin` VARCHAR(10), `description` VARCHAR(75) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `dependson` VARCHAR(25) DEFAULT 'none', `help` VARCHAR(255))");
		$this->db->exec("CREATE TABLE IF NOT EXISTS eventcfg_<myname> (`module` VARCHAR(50), `type` VARCHAR(18), `file` VARCHAR(255), `description` VARCHAR(75) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `help` VARCHAR(255))");
		$this->db->exec("CREATE TABLE IF NOT EXISTS settings_<myname> (`name` VARCHAR(50) NOT NULL, `module` VARCHAR(50), `type` VARCHAR(30), `mode` VARCHAR(10), `value` VARCHAR(255) DEFAULT '0', `options` VARCHAR(255) DEFAULT '0', `intoptions` VARCHAR(50) DEFAULT '0', `description` VARCHAR(75), `source` VARCHAR(5), `admin` VARCHAR(25), `verify` INT DEFAULT '0', `help` VARCHAR(255))");
		$this->db->exec("CREATE TABLE IF NOT EXISTS hlpcfg_<myname> (`name` VARCHAR(25) NOT NULL, `module` VARCHAR(50), `file` VARCHAR(255), `description` VARCHAR(75), `admin` VARCHAR(10), `verify` INT DEFAULT '0')");
		$this->db->exec("CREATE TABLE IF NOT EXISTS cmd_alias_<myname> (`cmd` VARCHAR(255) NOT NULL, `module` VARCHAR(50), `alias` VARCHAR(25) NOT NULL, `status` INT DEFAULT '0')");

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

		//Load user modules
		$this->loadUserModules();
		
		Registry::checkForMissingDependencies();
		
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

		$this->commandManager->loadCommands();
		$this->subcommandManager->loadSubcommands();
		$this->commandAlias->load();
		$this->eventManager->loadEvents();
	}

	/**
	 * @name: connect
	 * @description: connect to AO chat servers
	 */
	function connectAO($login, $password, $server, $port){
		// Begin the login process
		$this->logger->log('INFO', "Connecting to AO Server...({$server}:{$port})");
		$this->connect($server, $port);
		if ($this->state != "auth") {
			$this->logger->log('ERROR', "Connection failed! Please check your Internet connection and firewall.");
			sleep(10);
			die();
		}

		$this->logger->log('INFO', "Authenticate login data...");
		$this->authenticate($login, $password);
		if ($this->state != "login") {
			$this->logger->log('ERROR', "Authentication failed! Invalid username or password.");
			sleep(10);
			die();
		}

		$this->logger->log('INFO', "Logging in {$this->vars["name"]}...");
		$this->login($this->vars["name"]);
		if ($this->state != "ok") {
			$this->logger->log('ERROR', "Character selection failed! Could not login on as character '{$this->vars["name"]}'.");
			sleep(10);
			die();
		}

		$this->logger->log('INFO', "All Systems ready!");
	}

	public function run() {
		$start = time();
		$exec_connected_events = false;
		$time = 0;
		while (true) {
			while ($this->processNextPacket()) {

			}
			if ($this->is_ready()) {
				// check monitored sockets and notify socket-notifiers if any activity occur in their sockets
				$this->socketManager->checkMonitoredSockets();

				if ($exec_connected_events == false) {
					$this->eventManager->executeConnectEvents();
					$exec_connected_events = true;
				}

				$this->timer->executeTimerEvents();

				// execute cron events at most once every second
				if ($time < time()) {
					$this->eventManager->crons();
					$time = time();
				}

				usleep(10000);
			}
		}
	}
	
	public function processNextPacket() {
		// when bot isn't ready we wait for packets
		// to make sure the server has finished sending them
		// before marking the bot as ready
		$packet = $this->wait_for_packet($this->is_ready() ? 0 : 1);
		if ($packet) {
			$this->process_packet($packet);
			return true;
		} else {
			$this->ready = true;
			return false;
		}
	}

	function loadCoreModules() {
		// Load the Core Modules -- SETINGS must be first in case the other modules have settings
		$this->logger->log('INFO', "Loading CORE modules...");
		$core_modules = array('SETTINGS', 'SYSTEM', 'ADMIN', 'BAN', 'HELP', 'CONFIG', 'LIMITS', 'PLAYER_LOOKUP', 'FRIENDLIST', 'ALTS', 'USAGE', 'PREFERENCES', 'API_MODULE', 'HTTPAPI_MODULE', 'PROFILE');
		$this->db->begin_transaction();
		forEach ($core_modules as $MODULE_NAME) {
			$this->registerModule("./core", $MODULE_NAME);
		}
		$this->callAndClearSetupHandlers();
		$this->db->commit();
	}

	/**
	 * @name: loadUserModules
	 * @description: load all user modules
	 */
	function loadUserModules() {
		$this->logger->log('INFO', "Loading USER modules...");
		$this->db->begin_transaction();
		forEach ($this->vars['module_load_paths'] as $path) {
			if ($d = dir($path)) {
				while (false !== ($MODULE_NAME = $d->read())) {
					// filters out ., .., .svn
					if (!is_dir($MODULE_NAME) && $MODULE_NAME != '.svn') {
						$this->registerModule($path, $MODULE_NAME);
					}
				}
				$d->close();
			}
		}
		$this->callAndClearSetupHandlers();
		$this->db->commit();
	}
	
	/**
	 * Calls all so far collected @Setup handlers and clears them after use.
	 */
	private function callAndClearSetupHandlers() {
		// changed to while loop since other setupHandlers can be added
		// during the loop due to LegacyController
		while (!empty($this->setupHandlers)) {
			$handler = array_shift($this->setupHandlers);
			$handler[0] = Registry::getInstance($handler[0]);
			if (call_user_func($handler) === false) {
				$this->logger->log('ERROR', "Failed to call setup handler");
			}
		}
	}

	public function registerModule($baseDir, $MODULE_NAME) {
		// read module.ini file (if it exists) from module's directory
		if (file_exists("{$baseDir}/{$MODULE_NAME}/module.ini")) {
			$entries = parse_ini_file("{$baseDir}/{$MODULE_NAME}/module.ini");
			// check that current PHP version is greater or equal than module's
			// minimum required PHP version
			if (isset($entries["minimum_php_version"])) {
				$minimum = $entries["minimum_php_version"];
				$current = phpversion();
				if (strnatcmp($minimum, $current) > 0) {
					$this->logger->log('WARN', "Could not load module"
					." {$MODULE_NAME} as it requires at least PHP version '$minimum',"
					." but current PHP version is '$current'");
					return;
				}
			}
		}

		$newInstances = Registry::getNewInstancesInDir("{$baseDir}/{$MODULE_NAME}");
		forEach ($newInstances as $name => $className) {
			$this->registerInstance($MODULE_NAME, $name, new $className);
		}

		if (count($newInstances) == 0) {
			$this->logger->log('ERROR', "Could not load module {$MODULE_NAME}. No classes found with @Instance annotation!");
			return;
		}
	}

	public function sendPrivate($message, $disable_relay = false, $group = null) {
		// for when $text->make_blob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->sendPrivate($page, $group, $disable_relay);
			}
			return;
		}

		if ($group == null) {
			$group = $this->vars['name'];
		}

		$message = $this->text->format_message($message);
		$senderLink = $this->text->make_userlink($this->vars['name']);
		$guildNameForRelay = $this->relayController->getGuildAbbreviation();
		$guestColorChannel = $this->setting->get('guest_color_channel');
		$privColor = $this->setting->get('default_priv_color');

		$this->send_privgroup($group, $privColor.$message);
		if ($group == $this->vars["name"]) {
			// relay to guild channel
			if (!$disable_relay && $this->setting->get('guild_channel_status') == 1 && $this->setting->get("guest_relay") == 1 && $this->setting->get("guest_relay_commands") == 1) {
				$this->send_guild("</font>{$guestColorChannel}[Guest]</font> {$senderLink}: {$privColor}$message</font>", "\0");
			}

			// relay to bot relay
			if (!$disable_relay && $this->setting->get("relaybot") != "Off" && $this->setting->get("bot_relay_commands") == 1) {
				$this->relayController->send_message_to_relay("grc [{$guildNameForRelay}] [Guest] {$senderLink}: $message");
			}
		}
	}

	public function sendGuild($message, $disable_relay = false, $priority = null) {
		// for when $text->make_blob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->sendGuild($page, $disable_relay, $priority);
			}
			return;
		}

		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}

		$message = $this->text->format_message($message);
		$senderLink = $this->text->make_userlink($this->vars['name']);
		$guildNameForRelay = $this->relayController->getGuildAbbreviation();
		$guestColorChannel = $this->setting->get('guest_color_channel');
		$guildColor = $this->setting->get("default_guild_color");

		$this->send_guild($guildColor.$message, "\0", $priority);

		// relay to private channel
		if (!$disable_relay && $this->setting->get("guest_relay") == 1 && $this->setting->get("guest_relay_commands") == 1) {
			$this->send_privgroup($this->vars["name"], "</font>{$guestColorChannel}[{$guildNameForRelay}]</font> {$senderLink}: {$guildColor}$message</font>");
		}

		// relay to bot relay
		if (!$disable_relay && $this->setting->get("relaybot") != "Off" && $this->setting->get("bot_relay_commands") == 1) {
			$this->relayController->send_message_to_relay("grc [{$guildNameForRelay}] {$senderLink}: $message");
		}
	}

	public function sendTell($message, $character, $priority = null) {
		// for when $text->make_blob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->sendTell($page, $character, $priority);
			}
			return;
		}

		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}

		$message = $this->text->format_message($message);
		$tellColor = $this->setting->get("default_tell_color");

		$this->logger->log_chat("Out. Msg.", $character, $message);
		$this->send_tell($character, $tellColor.$message, "\0", $priority);
	}

	public function sendPublic($message, $channel, $priority = null) {
		// for when $text->make_blob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->sendPublic($page, $channel, $priority);
			}
			return;
		}

		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}

		$message = $this->text->format_message($message);
		$guildColor = $this->setting->get("default_guild_color");

		$this->send_group($channel, $guildColor.$message, "\0", $priority);
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
			$this->logger->log('ERROR', "The number of type arguments does not equal the number of admin arguments for command/subcommand registration");
			return false;
		}
		return true;
	}

	/**
	 * @name: process_packet
	 * @description: Proccess all incoming messages that bot recives
	 */
	function process_packet($packet) {
		try {
			$this->process_all_packets($packet);

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
		} catch (StopExecutionException $e) {
			$this->logger->log('DEBUG', 'Execution stopped prematurely', $e);
		}
	}

	function process_all_packets($packet) {
		$eventObj = new stdClass;
		$eventObj->type = 'allpackets';
		$eventObj->packet = $packet;
		$this->eventManager->fireEvent($eventObj);
	}

	function process_group_announce($args) {
		$b = unpack("C*", $args[0]);
		$this->logger->log('DEBUG', "AOCP_GROUP_ANNOUNCE => name: '$args[1]'");
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

		$this->logger->log('DEBUG', "AOCP_PRIVGRP_CLIJOIN => channel: '$channel' sender: '$sender'");

		if ($channel == $this->vars['name']) {
			$eventObj->type = "joinpriv";

			$this->logger->log_chat("Priv Group", -1, "$sender joined the channel.");

			// Remove sender if they are banned or if spam filter is blocking them
			if ($this->ban->is_banned($sender) || $this->spam[$sender] > 100){
				$this->privategroup_kick($sender);
				return;
			}

			// Add sender to the chatlist
			$this->chatlist[$sender] = true;

			$this->eventManager->fireEvent($eventObj);
		} else {
			$eventObj->type = "extjoinpriv";
			$this->eventManager->fireEvent($eventObj);
		}
	}

	function process_private_channel_leave($args) {
		$eventObj = new stdClass;
		$channel = $this->lookup_user($args[0]);
		$sender = $this->lookup_user($args[1]);
		$eventObj->channel = $channel;
		$eventObj->sender = $sender;

		$this->logger->log('DEBUG', "AOCP_PRIVGRP_CLIPART => channel: '$channel' sender: '$sender'");

		if ($channel == $this->vars['name']) {
			$eventObj->type = "leavepriv";

			$this->logger->log_chat("Priv Group", -1, "$sender left the channel.");

			// Remove from Chatlist array
			unset($this->chatlist[$sender]);

			$this->eventManager->fireEvent($eventObj);
		} else {
			$eventObj->type = "extleavepriv";

			$this->eventManager->fireEvent($eventObj);
		}
	}

	function process_buddy_update($args) {
		$sender	= $this->lookup_user($args[0]);
		$status	= 0 + $args[1];

		$eventObj = new stdClass;
		$eventObj->sender = $sender;

		$this->logger->log('DEBUG', "AOCP_BUDDY_ADD => sender: '$sender' status: '$status'");

		$this->buddylistManager->update($args);

		// Ignore Logon/Logoff from other bots or phantom logon/offs
		if ($sender == "") {
			return;
		}

		// Status => 0: logoff  1: logon
		if ($status == 0) {
			$eventObj->type = "logoff";

			$this->logger->log('DEBUG', "$sender logged off");

			$this->eventManager->fireEvent($eventObj);
		} else if ($status == 1) {
			$eventObj->type = "logon";

			$this->logger->log('INFO', "$sender logged on");

			$this->eventManager->fireEvent($eventObj);
		}
	}

	function process_private_message($args) {
		$type = "msg";
		$sender	= $this->lookup_user($args[0]);

		$this->logger->log('DEBUG', "AOCP_MSG_PRIVATE => sender: '$sender' message: '$args[1]'");

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

		$this->logger->log_chat("Inc. Msg.", $sender, $message);

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

		$this->eventManager->fireEvent($eventObj);

		// remove the symbol if there is one
		if ($message[0] == $this->setting->get("symbol") && strlen($message) > 1) {
			$message = substr($message, 1);
		}

		// check tell limits
		if (!$this->limits->check($sender, $message)) {
			return;
		}

		$sendto = new PrivateMessageCommandReply($this, $sender);
		$this->commandManager->process($type, $message, $sender, $sendto);
	}

	function process_private_channel_message($args) {
		$sender	= $this->lookup_user($args[1]);
		$channel = $this->lookup_user($args[0]);
		$message = $args[2];

		$eventObj = new stdClass;
		$eventObj->sender = $sender;
		$eventObj->channel = $channel;
		$eventObj->message = $message;

		$this->logger->log('DEBUG', "AOCP_PRIVGRP_MESSAGE => sender: '$sender' channel: '$channel' message: '$message'");
		$this->logger->log_chat($channel, $sender, $message);

		if ($sender == $this->vars["name"] || $this->ban->is_banned($sender)) {
			return;
		}

		if ($this->setting->get('spam_protection') == 1) {
			if ($this->spam[$sender] == 40) {
				$this->sendTell("Error! Your client is sending a high frequency of chat messages. Stop or be kicked.", $sender);
			}
			if ($this->spam[$sender] > 60) {
				$this->privategroup_kick($sender);
			}
		}

		if ($channel == $this->vars['name']) {
			$type = "priv";
			$eventObj->type = $type;

			$this->eventManager->fireEvent($eventObj);

			if ($message[0] == $this->setting->get("symbol") && strlen($message) > 1) {
				$message = substr($message, 1);
				$sendto = new PrivateChannelCommandReply($this);
				$this->commandManager->process($type, $message, $sender, $sendto);
			}
		} else {  // ext priv group message
			$type = "extpriv";
			$eventObj->type = $type;

			$this->eventManager->fireEvent($eventObj);
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

		$this->logger->log('DEBUG', "AOCP_GROUP_MESSAGE => sender: '$sender' channel: '$channel' message: '$message'");

		if (in_array($channel, $this->channelsToIgnore)) {
			return;
		}

		// don't log tower messages with rest of chat messages
		if ($channel != "All Towers" && $channel != "Tower Battle Outcome") {
			$this->logger->log_chat($channel, $sender, $message);
		} else {
			$this->logger->log('DEBUG', "[" . $channel . "]: " . $message);
		}

		if ($sender) {
			// ignore messages that are sent from the bot self
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

			$this->eventManager->fireEvent($eventObj);
		} else if ($channel == "Org Msg"){
			$eventObj->type = "orgmsg";

			$this->eventManager->fireEvent($eventObj);
		} else if ($b[1] == 3 && $this->setting->get('guild_channel_status') == 1) {
			$type = "guild";
			$sendto = 'guild';

			$eventObj->type = $type;

			$this->eventManager->fireEvent($eventObj);

			if ($message[0] == $this->setting->get("symbol") && strlen($message) > 1) {
				$message = substr($message, 1);
				$sendto = new GuildChannelCommandReply($this);
				$this->commandManager->process($type, $message, $sender, $sendto);
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

		$this->logger->log('DEBUG', "AOCP_PRIVGRP_INVITE => sender: '$sender'");

		$this->logger->log_chat("Priv Channel Invitation", -1, "$sender channel invited.");

		$this->eventManager->fireEvent($eventObj);
	}

	public function registerInstance($MODULE_NAME, $name, &$obj) {
		$name = strtolower($name);
		$this->logger->log('DEBUG', "Registering instance name '$name' for module '$MODULE_NAME'");
		if (Registry::instanceExists($name)) {
			$this->logger->log('WARN', "Instance with name '$name' already registered--replaced with new instance");
		}
		$obj->moduleName = $MODULE_NAME;
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
					@$property->getAnnotation('Options')->value,
					@$property->getAnnotation('Intoptions')->value,
					@$property->getAnnotation('AccessLevel')->value,
					@$property->getAnnotation('Help')->value
				);
			}
		}

		// register commands, subcommands, and events annotated on the class
		$commands = array();
		$subcommands = array();
		forEach ($reflection->getAllAnnotations() as $annotation) {
			if ($annotation instanceof DefineCommand) {
				if (!$annotation->command) {
					$this->logger->log('WARN', "Cannot parse @DefineCommand annotation in '$name'.");
				}
				$command = $annotation->command;
				$definition = array(
					'channels'      => $annotation->channels,
					'defaultStatus' => $annotation->defaultStatus,
					'accessLevel'   => $annotation->accessLevel,
					'description'   => $annotation->description,
					'help'          => $annotation->help,
					'handlers'      => array()
				);
				list($parentCommand, $subCommand) = explode(" ", $command, 2);
				if ($subCommand) {
					$definition['parentCommand'] = $parentCommand;
					$subcommands[$command] = $definition;
				} else {
					$commands[$command] = $definition;
				}
				// register command alias if defined
				if ($annotation->alias) {
					$this->commandAlias->register($MODULE_NAME, $command, $annotation->alias);
				}
			}
		}

		forEach ($reflection->getMethods() as $method) {
			if ($method->hasAnnotation('Setup')) {
				$this->setupHandlers[] = array($name, $method->name);
			} else if ($method->hasAnnotation('HandlesCommand')) {
				$commandName = $method->getAnnotation('HandlesCommand')->value;
				$methodName  = $method->name;
				$handlerName = "{$name}.{$method->name}";
				if (isset($commands[$commandName])) {
					$commands[$commandName]['handlers'][] = $handlerName;
				} else if (isset($subcommands[$commandName])) {
					$subcommands[$commandName]['handlers'][] = $handlerName;
				} else {
					$this->logger->log('WARN', "Cannot handle command '$commandName' as it is not defined with @DefineCommand in '$name'.");
				}
			} else if ($method->hasAnnotation('Event')) {
				$this->eventManager->register(
					$MODULE_NAME,
					$method->getAnnotation('Event')->value,
					$name . '.' . $method->name,
					@$method->getAnnotation('Description')->value,
					@$method->getAnnotation('Help')->value,
					@$method->getAnnotation('DefaultStatus')->value
				);
			}
		}

		forEach ($commands as $command => $definition) {
			$this->commandManager->register(
				$MODULE_NAME,
				$definition['channels'],
				implode(',', $definition['handlers']),
				$command,
				$definition['accessLevel'],
				$definition['description'],
				$definition['help'],
				$definition['defaultStatus']
			);
		}
		forEach ($subcommands as $subCommand => $definition) {
			$this->subcommandManager->register(
				$MODULE_NAME,
				$definition['channels'],
				implode(',', $definition['handlers']),
				$subCommand,
				$definition['accessLevel'],
				$definition['parentCommand'],
				$definition['description'],
				$definition['help'],
				$definition['defaultStatus']
			);
		}
	}

	/**
	 * @name: is_ready
	 * @description: tells when the bot is logged on and all the start up events have finished
	 */
	public function is_ready() {
		return $this->ready && (time() >= $this->vars["startup"] + $this->setting->get("logon_delay"));
	}
}

?>
