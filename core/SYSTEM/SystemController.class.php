<?php

namespace Budabot\Core\Modules;

use Budabot\Core\PrivateMessageCommandReply;

/**
 * Authors: 
 *  - Sebuda (RK2)
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'checkaccess',
 *		accessLevel   = 'all',
 *		description   = 'Check effective access level of a character',
 *		help          = 'checkaccess.txt'
 *	)
 *	@DefineCommand(
 *		command       = 'clearqueue',
 *		accessLevel   = 'mod',
 *		description   = 'Clear outgoing chatqueue from all pending messages',
 *		help          = 'clearqueue.txt'
 *	)
 *	@DefineCommand(
 *		command       = 'macro',
 *		accessLevel   = 'all',
 *		description   = 'Execute multiple commands at once',
 *		help          = 'macro.txt'
 *	)
 *	@DefineCommand(
 *		command       = 'showcommand',
 *		accessLevel   = 'mod',
 *		description   = 'Execute a command and have output sent to another player',
 *		help          = 'showcommand.txt'
 *	)
 *	@DefineCommand(
 *		command       = 'system',
 *		accessLevel   = 'mod',
 *		description   = 'Show detailed information about the bot',
 *		help          = 'system.txt'
 *	)
 *	@DefineCommand(
 *		command       = 'restart',
 *		accessLevel   = 'admin',
 *		description   = 'Restart the bot',
 *		help          = 'system.txt',
 *      defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'shutdown',
 *		accessLevel   = 'admin',
 *		description   = 'Shutdown the bot',
 *		help          = 'system.txt',
 *		defaultStatus = '1'
 *	)
 */
class SystemController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $accessManager;

	/** @Inject */
	public $adminManager;

	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $db;

	/** @Inject */
	public $commandManager;
	
	/** @Inject */
	public $eventManager;

	/** @Inject */
	public $commandAlias;

	/** @Inject */
	public $subcommandManager;

	/** @Inject */
	public $helpManager;
	
	/** @Inject */
	public $buddylistManager;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;

	/** @Logger */
	public $logger;

	/**
	 * @Setting("symbol")
	 * @Description("Command prefix symbol")
	 * @Visibility("edit")
	 * @Type("text")
	 * @Options("!;#;*;@;$;+;-")
	 * @AccessLevel("mod")
	 */
	public $defaultSymbol = "!";

	/**
	 * @Setting("guild_admin_rank")
	 * @Description("Guild rank required to be considered a guild admin")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("0;1;2;3;4;5;6")
	 * @AccessLevel("mod")
	 * @Help("guild_admin_rank.txt")
	 */
	public $defaultGuildAdminRank = "1";

	/**
	 * @Setting("guild_admin_access_level")
	 * @Description("Access level that guild admins acquire")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("admin;mod;rl;all")
	 * @AccessLevel("mod")
	 */
	public $defaultGuildAdminAccessLevel = "all";

	/**
	 * @Setting("max_blob_size")
	 * @Description("Max chars for a window")
	 * @Visibility("edit")
	 * @Type("number")
	 * @Options("4500;6000;7500;9000;10500;12000")
	 * @AccessLevel("mod")
	 * @Help("max_blob_size.txt")
	 */
	public $defaultMaxBlobSize = "7500";

	/**
	 * @Setting("http_timeout")
	 * @Description("Max time to wait for response from making http queries")
	 * @Visibility("edit")
	 * @Type("time")
	 * @Options("1s;2s;5s;10s;30s")
	 * @AccessLevel("mod")
	 */
	public $defaultHttpTimeout = "10s";

	/**
	 * @Setting("guild_channel_status")
	 * @Description("Enable the guild channel")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 */
	public $defaultGuildChannelStatus = "1";

	/**
	 * @Setting("guild_channel_cmd_feedback")
	 * @Description("Show message on invalid command in guild channel")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 */
	public $defaultGuildChannelCmdFeedback = "1";

	/**
	 * @Setting("private_channel_cmd_feedback")
	 * @Description("Show message on invalid command in private channel")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 */
	public $defaultPrivateChannelCmdFeedback = "1";

	/**
	 * @Setting("version")
	 * @Description("Database version")
	 * @Visibility("noedit")
	 * @Type("text")
	 * @AccessLevel("mod")
	 */
	public $defaultVersion = "0";
	
	/**
	 * @Setting("access_denied_notify_guild")
	 * @Description("Notify guild channel when a player is denied access to a command in tell")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 */
	public $defaultAccessDeniedNotifyGuild = "1";
	
	/**
	 * @Setting("access_denied_notify_priv")
	 * @Description("Notify private channel when a player is denied access to a command in tell")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 */
	public $defaultAccessDeniedNotifyPriv = "1";

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		global $version;
		$this->settingManager->save('version', $version);

		$this->helpManager->register($this->moduleName, "budatime", "budatime.txt", "all", "Format for budatime");
		
		$name = $this->chatBot->vars['name'];
		$this->settingManager->add($this->moduleName, "default_private_channel", "Private channel to process commands from", "edit", "text", $name, $name);
	}
	
	/**
	 * @HandlesCommand("restart")
	 * @Matches("/^restart$/i")
	 */
	public function restartCommand($message, $channel, $sender, $sendto, $args) {
		$msg = "Bot is restarting.";
		$this->chatBot->sendTell($msg, $sender);
		$this->chatBot->sendPrivate($msg, true);
		$this->chatBot->sendGuild($msg, true);

		$this->chatBot->disconnect();
		$this->logger->log('INFO', "The Bot is restarting.");
		exit(-1);
	}

	/**
	 * @HandlesCommand("shutdown")
	 * @Matches("/^shutdown$/i")
	 */
	public function shutdownCommand($message, $channel, $sender, $sendto, $args) {
		$msg = "The Bot is shutting down.";
		$this->chatBot->sendTell($msg, $sender);
		$this->chatBot->sendPrivate($msg, true);
		$this->chatBot->sendGuild($msg, true);

		$this->chatBot->disconnect();
		$this->logger->log('INFO', "The Bot is shutting down.");
		exit(10);
	}

	/**
	 * @HandlesCommand("system")
	 * @Matches("/^system$/i")
	 */
	public function systemCommand($message, $channel, $sender, $sendto, $args) {
		global $version;

		$sql = "SELECT count(*) AS count FROM players";
		$row = $this->db->queryRow($sql);
		$num_player_cache = $row->count;

		$num_buddylist = 0;
		forEach ($this->buddylistManager->buddyList as $key => $value) {
			if (!isset($value['name'])) {
				// skip the buddies that have been added but the server hasn't sent back an update yet
				continue;
			}

			$num_buddylist++;
		}

		$blob = "Name: <highlight><myname><end>\n";
		$blob .= "SuperAdmin: <highlight>'{$this->chatBot->vars['SuperAdmin']}'<end>\n";
		$blob .= "Guild: <highlight>'<myguild>' (" . $this->chatBot->vars['my_guild_id'] . ")<end>\n\n";

		$blob .= "Budabot: <highlight>$version<end>\n";
		$blob .= "PHP: <highlight>" . phpversion() . "<end>\n";
		$blob .= "OS: <highlight>" . php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m') . "<end>\n";
		$blob .= "Database: <highlight>" . $this->db->getType() . "<end>\n\n";

		$blob .= "Current Memory Usage: <highlight>" . $this->util->bytesConvert(memory_get_usage()) . "<end>\n";
		$blob .= "Current Memory Usage (Real): <highlight>" . $this->util->bytesConvert(memory_get_usage(1)) . "<end>\n";
		if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
			$blob .= "Peak Memory Usage: <highlight>" . $this->util->bytesConvert(memory_get_peak_usage()) . "<end>\n";
			$blob .= "Peak Memory Usage (Real): <highlight>" . $this->util->bytesConvert(memory_get_peak_usage(1)) . "<end>\n\n";
		}
		
		$blob .= "Using Chat Proxy: <highlight>" . ($this->chatBot->vars['use_proxy'] == 1 ? "enabled" : "disabled") . "<end>\n";

		$date_string = $this->util->unixtimeToReadable(time() - $this->chatBot->vars['startup']);
		$blob .= "Uptime: <highlight>$date_string<end>\n\n";

		$eventnum = 0;
		forEach ($this->eventManager->events as $type => $events) {
			$eventnum += count($events);
		}

		$numAliases = count($this->commandAlias->getEnabledAliases());

		$blob .= "Active tell commands: <highlight>" . (count($this->commandManager->commands['msg']) - $numAliases) . "<end>\n";
		$blob .= "Active private channel commands: <highlight>" . (count($this->commandManager->commands['priv']) - $numAliases) . "<end>\n";
		$blob .= "Active guild channel commands: <highlight>" . (count($this->commandManager->commands['guild']) - $numAliases) . "<end>\n";
		$blob .= "Active subcommands: <highlight>" . count($this->subcommandManager->subcommands) . "<end>\n";
		$blob .= "Active command aliases: <highlight>" . $numAliases . "<end>\n";
		$blob .= "Active events: <highlight>" . $eventnum . "<end>\n";
		$blob .= "Active help commands: <highlight>" . count($this->helpManager->getAllHelpTopics(null)) . "<end>\n\n";

		$blob .= "Characters on the buddy list: <highlight>$num_buddylist / " . count($this->buddylistManager->buddyList) . "<end>\n";
		$blob .= "Maximum buddy list size: <highlight>" . $this->chatBot->getBuddyListSize() . "<end>\n";
		$blob .= "Characters in the private channel: <highlight>" . count($this->chatBot->chatlist) . "<end>\n";
		$blob .= "Guild members: <highlight>" . count($this->chatBot->guildmembers) . "<end>\n";
		$blob .= "Character infos in cache: <highlight>" . $num_player_cache . "<end>\n";
		$blob .= "Messages in the chat queue: <highlight>" . count($this->chatBot->chatqueue->queue) . "<end>\n\n";

		$blob .= "Public Channels:\n";
		forEach ($this->chatBot->grp as $gid => $status) {
			$string = unpack("N", substr($gid, 1));
			$blob .= "<tab><highlight>'{$this->chatBot->gid[$gid]}'<end> (" . ord(substr($gid, 0, 1)) . " " . $string[1] . ")\n";
		}

		$msg = $this->text->makeBlob('System Info', $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("checkaccess")
	 * @Matches("/^checkaccess$/i")
	 * @Matches("/^checkaccess (.+)$/i")
	 */
	public function checkaccessCommand($message, $channel, $sender, $sendto, $args) {
		if (isset($args[1])) {
			$name = ucfirst(strtolower($args[1]));
			if (!$this->chatBot->get_uid($name)) {
				$sendto->reply("Character <highlight>{$name}<end> does not exist.");
				return;
			}
		} else {
			$name = $sender;
		}
	
		$accessLevel = $this->accessManager->getDisplayName($this->accessManager->getAccessLevelForCharacter($name));
	
		$msg = "Access level for $name is <highlight>$accessLevel<end>.";
		$sendto->reply($msg);
	}

	/**
	 * This command handler clear outgoing chatqueue from all pending messages.
	 *
	 * @HandlesCommand("clearqueue")
	 */
	public function clearqueueCommand($message, $channel, $sender, $sendto, $args) {
		$num = 0;
		forEach ($this->chatBot->chatqueue->queue as $priority) {
			$num += count($priority);
		}
		$this->chatBot->chatqueue->queue = array();
	
		$sendto->reply("Chat queue has been cleared of $num messages.");
	}

	/**
	 * This command handler execute multiple commands at once.
	 *
	 * @HandlesCommand("macro")
	 * @Matches("/^macro (.+)$/si")
	 */
	public function macroCommand($message, $channel, $sender, $sendto, $args) {
		$commands = explode("|", $args[1]);
		forEach ($commands as $commandString) {
			$this->commandManager->process($channel, $commandString, $sender, $sendto);
		}
	}

	/**
	 * @Event("timer(1hr)")
	 * @Description("This event handler is called every hour to keep MySQL connection active")
	 * @DefaultStatus("1")
	 */
	public function refreshMySQLConnectionEvent($eventObj) {
		// if the bot doesn't query the mysql database for 8 hours the db connection is closed
		$this->logger->log('DEBUG', "Pinging database");
		$sql = "SELECT * FROM settings_<myname>";
		$this->db->query($sql);
	}

	/**
	 * @Event("connect")
	 * @Description("Notify private channel, guild channel, and admins that bot is online")
	 * @DefaultStatus("1")
	 */
	public function onConnectEvent($eventObj) {
		// send Admin(s) a tell that the bot is online
		forEach ($this->adminManager->admins as $name => $info) {
			if ($info["level"] == 4 && $this->buddylistManager->isOnline($name) == 1) {
				$this->chatBot->sendTell("<myname> is <green>online<end>. For updates or help use the Budabot Forums <highlight>http://budabot.com<end>", $name);
			}
		}
		
		global $version;
		$msg = "Budabot <highlight>$version<end> now <green>online<end>.";

		// send a message to guild channel
		$this->chatBot->sendGuild($msg, true);
		$this->chatBot->sendPrivate($msg, true);
	}
	
	/**
	 * @HandlesCommand("showcommand")
	 * @Matches("/^showcommand ([^ ]+) (.+)$/i")
	 */
	public function showCommandCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$cmd = $args[2];
		$type = "msg";
	
		$showSendto = new PrivateMessageCommandReply($this->chatBot, $name);
		$this->commandManager->process($type, $cmd, $sender, $showSendto);
		
		$sendto->reply("Command <highlight>$cmd<end> has been sent to <highlight>$name<end>.");
	}
}
