<?php

namespace Budabot\Core;

use ReflectionAnnotatedClass;
use stdClass;
use DefineCommand;

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
	public $helpManager;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $banManager;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $limitsController;

	/** @Inject */
	public $buddylistManager;
	
	/** @Inject */
	public $relayController;
	
	/** @Inject */
	public $setting;

	/** @Logger("Core") */
	public $logger;

	public $ready = false;

	public $chatlist = array();
	public $guildmembers = array();
	public $vars;

	// array where modules can store stateful session data
	public $data = array();
	
	private $buddyListSize = 0;

	//Ignore Messages from Vicinity/IRRK New Wire/OT OOC/OT Newbie OOC...
	public $channelsToIgnore = array("", 'IRRK News Wire', 'OT OOC', 'OT Newbie OOC', 'OT Jpn OOC', 'OT shopping 11-50',
		'Tour Announcements', 'Neu. Newbie OOC', 'Neu. Jpn OOC', 'Neu. shopping 11-50', 'Neu. OOC', 'Clan OOC',
		'Clan Newbie OOC', 'Clan Jpn OOC', 'Clan shopping 11-50', 'OT German OOC', 'Clan German OOC', 'Neu. German OOC');
	
	public function init(&$vars) {
		$this->vars = $vars;

		// Set startup time
		$this->vars["startup"] = time();
	
		$this->logger->log('DEBUG', 'Initializing bot');

		// Create core tables if not exists
		$this->db->exec("CREATE TABLE IF NOT EXISTS cmdcfg_<myname> (`module` VARCHAR(50), `cmdevent` VARCHAR(6), `type` VARCHAR(18), `file` TEXT, `cmd` VARCHAR(50), `admin` VARCHAR(10), `description` VARCHAR(75) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `dependson` VARCHAR(25) DEFAULT 'none', `help` VARCHAR(255))");
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
		
		$this->db->beginTransaction();
		forEach (Registry::getAllInstances() as $name => $instance) {
			if (isset($instance->moduleName)) {
				$this->registerInstance($name, $instance);
			} else {
				$this->callSetupMethod($name, $instance);
			}
		}
		$this->db->commit();
		
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
	function connectAO($login, $password, $server, $port) {
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

		$this->buddyListSize += 1000;
		$this->logger->log('INFO', "All Systems ready!");
	}

	public function run() {
		$loop = new EventLoop();
		Registry::injectDependencies($loop);
		while (true) {
			$loop->execSingleLoop();
		}
	}

	public function processAllPackets() {
		while ($this->processNextPacket()) {

		}
	}
	
	public function processNextPacket() {
		// when bot isn't ready we wait for packets
		// to make sure the server has finished sending them
		// before marking the bot as ready
		$packet = $this->wait_for_packet($this->isReady() ? 0 : 1);
		if ($packet) {
			$this->process_packet($packet);
			return true;
		} else {
			$this->ready = true;
			return false;
		}
	}

	public function sendPrivate($message, $disable_relay = false, $group = null) {
		// for when $text->makeBlob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->sendPrivate($page, $disable_relay, $group);
			}
			return;
		}

		if ($group == null) {
			$group = $this->setting->default_private_channel;
		}

		$message = $this->text->formatMessage($message);
		$senderLink = $this->text->makeUserlink($this->vars['name']);
		$guildNameForRelay = $this->relayController->getGuildAbbreviation();
		$guestColorChannel = $this->settingManager->get('guest_color_channel');
		$privColor = $this->settingManager->get('default_priv_color');

		$this->send_privgroup($group, $privColor.$message);
		if ($this->isDefaultPrivateChannel($group)) {
			// relay to guild channel
			if (!$disable_relay && $this->settingManager->get('guild_channel_status') == 1 && $this->settingManager->get("guest_relay") == 1 && $this->settingManager->get("guest_relay_commands") == 1) {
				$this->send_guild("</font>{$guestColorChannel}[Guest]</font> {$senderLink}: {$privColor}$message</font>", "\0");
			}

			// relay to bot relay
			if (!$disable_relay && $this->settingManager->get("relaybot") != "Off" && $this->settingManager->get("bot_relay_commands") == 1) {
				$this->relayController->send_message_to_relay("grc [{$guildNameForRelay}] [Guest] {$senderLink}: $message");
			}
		}
	}

	public function sendGuild($message, $disable_relay = false, $priority = null) {
		if ($this->settingManager->get('guild_channel_status') != 1) {
			return;
		}

		// for when $text->makeBlob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->sendGuild($page, $disable_relay, $priority);
			}
			return;
		}

		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}

		$message = $this->text->formatMessage($message);
		$senderLink = $this->text->makeUserlink($this->vars['name']);
		$guildNameForRelay = $this->relayController->getGuildAbbreviation();
		$guestColorChannel = $this->settingManager->get('guest_color_channel');
		$guildColor = $this->settingManager->get("default_guild_color");

		$this->send_guild($guildColor.$message, "\0", $priority);

		// relay to private channel
		if (!$disable_relay && $this->settingManager->get("guest_relay") == 1 && $this->settingManager->get("guest_relay_commands") == 1) {
			$this->send_privgroup($this->setting->default_private_channel, "</font>{$guestColorChannel}[{$guildNameForRelay}]</font> {$senderLink}: {$guildColor}$message</font>");
		}

		// relay to bot relay
		if (!$disable_relay && $this->settingManager->get("relaybot") != "Off" && $this->settingManager->get("bot_relay_commands") == 1) {
			$this->relayController->send_message_to_relay("grc [{$guildNameForRelay}] {$senderLink}: $message");
		}
	}

	public function sendTell($message, $character, $priority = null, $formatMessage = true) {
		// for when $text->makeBlob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->sendTell($page, $character, $priority);
			}
			return;
		}

		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}

		if ($formatMessage) {
			$message = $this->text->formatMessage($message);
			$tellColor = $this->settingManager->get("default_tell_color");
		}

		$this->logger->logChat("Out. Msg.", $character, $message);
		$this->send_tell($character, $tellColor.$message, "\0", $priority);
	}

	public function sendPublic($message, $channel, $priority = null) {
		// for when $text->makeBlob generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->sendPublic($page, $channel, $priority);
			}
			return;
		}

		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}

		$message = $this->text->formatMessage($message);
		$guildColor = $this->settingManager->get("default_guild_color");

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
				case AOCP_LOGIN_OK: //5
					$this->buddyListSize += 1000;
					break;
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
				case AOCP_BUDDY_REMOVE: // 41, Incoming buddy removed
					$this->process_buddy_removed($packet->args);
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

		if ($this->isDefaultPrivateChannel($channel)) {
			$eventObj->type = "joinpriv";

			$this->logger->logChat("Priv Group", -1, "$sender joined the channel.");

			// Remove sender if they are banned or if spam filter is blocking them
			if ($this->banManager->isBanned($sender) || $this->spam[$sender] > 100){
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

		if ($this->isDefaultPrivateChannel($channel)) {
			$eventObj->type = "leavepriv";

			$this->logger->logChat("Priv Group", -1, "$sender left the channel.");

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

	function process_buddy_removed($args) {
		$sender	= $this->lookup_user($args[0]);

		$eventObj = new stdClass;
		$eventObj->sender = $sender;

		$this->logger->log('DEBUG', "AOCP_BUDDY_REMOVE => sender: '$sender'");

		$this->buddylistManager->updateRemoved($args);
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

		$this->logger->logChat("Inc. Msg.", $sender, $message);

		// AFK/bot check
		if (preg_match("|$sender is AFK|si", $message)) {
			return;
		} else if (preg_match("|I am away from my keyboard right now|si", $message)) {
			return;
		} else if (preg_match("|Unknown command or access denied!|si", $message)) {
			return;
		} else if (preg_match("|I am responding|si", $message)) {
			return;
		} else if (preg_match("|I only listen|si", $message)) {
			return;
		} else if (preg_match("|Error!|si", $message)) {
			return;
		} else if (preg_match("|Unknown command input|si", $message)) {
			return;
		} else if (preg_match("|/tell $sender !help|i", $message)) {
			return;
		}

		if ($this->banManager->isBanned($sender)) {
			return;
		} else if ($this->settingManager->get('spam_protection') == 1 && $this->spam[$sender] > 100) {
			$this->spam[$sender] += 20;
			return;
		}

		$this->eventManager->fireEvent($eventObj);

		// remove the symbol if there is one
		if ($message[0] == $this->settingManager->get("symbol") && strlen($message) > 1) {
			$message = substr($message, 1);
		}

		// check tell limits
		if (!$this->limitsController->check($sender, $message)) {
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
		$this->logger->logChat($channel, $sender, $message);

		if ($sender == $this->vars["name"] || $this->banManager->isBanned($sender)) {
			return;
		}

		if ($this->settingManager->get('spam_protection') == 1) {
			if ($this->spam[$sender] == 40) {
				$this->sendTell("Error! Your client is sending a high frequency of chat messages. Stop or be kicked.", $sender);
			}
			if ($this->spam[$sender] > 60) {
				$this->privategroup_kick($sender);
			}
		}

		if ($this->isDefaultPrivateChannel($channel)) {
			$type = "priv";
			$eventObj->type = $type;

			$this->eventManager->fireEvent($eventObj);

			if ($message[0] == $this->settingManager->get("symbol") && strlen($message) > 1) {
				$message = substr($message, 1);
				$sendto = new PrivateChannelCommandReply($this, $channel);
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
			$this->logger->logChat($channel, $sender, $message);
		} else {
			$this->logger->log('DEBUG', "[" . $channel . "]: " . $message);
		}

		if ($this->util->isValidSender($sender)) {
			// ignore messages that are sent from the bot self
			if ($sender == $this->vars["name"]) {
				return;
			}
			if ($this->banManager->isBanned($sender)) {
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
		} else if ($b[1] == 3 && $this->settingManager->get('guild_channel_status') == 1) {
			$type = "guild";
			$sendto = 'guild';

			$eventObj->type = $type;

			$this->eventManager->fireEvent($eventObj);

			if ($message[0] == $this->settingManager->get("symbol") && strlen($message) > 1) {
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

		$this->logger->logChat("Priv Channel Invitation", -1, "$sender channel invited.");

		$this->eventManager->fireEvent($eventObj);
	}

	public function registerInstance($name, $obj) {
		$this->logger->log('DEBUG', "Registering instance name '$name' for module '$moduleName'");
		$moduleName = $obj->moduleName;

		// register settings annotated on the class
		$reflection = new ReflectionAnnotatedClass($obj);
		forEach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation('Setting')) {
				$this->settingManager->add(
					$moduleName,
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
					$this->commandAlias->register($moduleName, $command, $annotation->alias);
				}
			}
		}

		forEach ($reflection->getMethods() as $method) {
			if ($method->hasAnnotation('Setup')) {
				if (call_user_func(array($obj, $method->name)) === false) {
					$this->logger->log('ERROR', "Failed to call setup handler for '$name'");
				}
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
				forEach ($method->getAllAnnotations('Event') as $eventAnnotation) {
					$this->eventManager->register(
						$moduleName,
						$eventAnnotation->value,
						$name . '.' . $method->name,
						@$method->getAnnotation('Description')->value,
						@$method->getAnnotation('Help')->value,
						@$method->getAnnotation('DefaultStatus')->value
					);
				}
			}
		}

		forEach ($commands as $command => $definition) {
			if (count($definition['handlers']) == 0) {
				$this->logger->log('ERROR', "No handlers defined for command $command in module '$moduleName'.");
				continue;
			}
			$this->commandManager->register(
				$moduleName,
				$definition['channels'],
				implode(',', $definition['handlers']),
				$command,
				$definition['accessLevel'],
				$definition['description'],
				$definition['help'],
				$definition['defaultStatus']
			);
		}

		forEach ($subcommands as $subcommand => $definition) {
			if (count($definition['handlers']) == 0) {
				$this->logger->log('ERROR', "No handlers defined for subcommand $subcommand in module '$moduleName'.");
				continue;
			}
			$this->subcommandManager->register(
				$moduleName,
				$definition['channels'],
				implode(',', $definition['handlers']),
				$subcommand,
				$definition['accessLevel'],
				$definition['parentCommand'],
				$definition['description'],
				$definition['help'],
				$definition['defaultStatus']
			);
		}
	}

	public function callSetupMethod($name, $obj) {
		$reflection = new ReflectionAnnotatedClass($obj);
		forEach ($reflection->getMethods() as $method) {
			if ($method->hasAnnotation('Setup')) {
				if (call_user_func(array($obj, $method->name)) === false) {
					$this->logger->log('ERROR', "Failed to call setup handler for '$name'");
				}
			}
		}
	}

	public function getBuddyListSize() {
		return $this->buddyListSize;
	}

	/**
	 * @name: isReady
	 * @description: tells when the bot is logged on and all the start up events have finished
	 */
	public function isReady() {
		return $this->ready && (time() >= $this->vars["startup"] + $this->settingManager->get("logon_delay"));
	}
	
	public function isDefaultPrivateChannel($channel) {
		return $channel == $this->setting->default_private_channel;
	}
}
