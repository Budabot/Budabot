<?php

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
	 * @Setting("spam_protection")
	 * @Description("Enable spam protection")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 * @Help("spam_protection.txt")
	 */
	public $defaultSpamProtection = "0";

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
	 * @Setting("xml_timeout")
	 * @Description("Max time to wait for response from xml servers")
	 * @Visibility("edit")
	 * @Type("time")
	 * @Options("1s;2s;5s;10s;30s")
	 * @AccessLevel("mod")
	 */
	public $defaultXmlTimeout = "5s";

	/**
	 * @Setting("logon_delay")
	 * @Description("Time to wait before executing connect events and cron jobs")
	 * @Visibility("edit")
	 * @Type("time")
	 * @Options("5s;10s;20s;30s")
	 * @AccessLevel("mod")
	 */
	public $defaultLogonDelay = "10s";

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
	 * @Description("Bot version that database was created from")
	 * @Visibility("noedit")
	 * @Type("text")
	 * @AccessLevel("mod")
	 */
	public $defaultVersion = "0";

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$name = 'SystemController';
		// don't register, only activate these commands to prevent them from appearing in !config
		forEach (array('msg', 'priv', 'guild') as $channel) {
			$this->commandManager->activate($channel, "$name.restartCommand", "restart", "admin");
			$this->commandManager->activate($channel, "$name.shutdownCommand", "shutdown", "admin");
			$this->commandManager->activate($channel, "$name.reloadconfigCommand", "reloadconfig", "admin");
			$this->commandManager->activate($channel, "$name.systemCommand", "system", "mod");
			$this->commandManager->activate($channel, "$name.logsCommand,$name.logsFileCommand", "logs", "admin");
		}

		// don't register, only activate these events to prevent them from appearing in !config
		$this->eventManager->activate("1hour", "$name.refreshMySQLConnectionEvent");
		$this->eventManager->activate("2sec", "$name.reduceSpamValuesEvent");
		$this->eventManager->activate("connect", "$name.onConnectEvent");

		global $version;
		$this->settingManager->save('version', $version);

		$this->helpManager->register($this->moduleName, "system", "system.txt", "admin", "Admin System Help file");
		$this->helpManager->register($this->moduleName, "budatime", "budatime.txt", "all", "Format for budatime");
		$this->helpManager->register($this->moduleName, "logs", "logs.txt", "all", "View bot logs");
	}
	
	/**
	 * This command handler restarts the bot.
	 * Note: This handler has not been not registered, only activated.
	 */
	public function restartCommand($message, $channel, $sender, $sendto, $args) {
		$msg = "Bot is restarting.";
		$this->chatBot->sendTell($msg, $sender);
		$this->chatBot->sendPrivate($msg, true);
		$this->chatBot->sendGuild($msg, true);

		$this->chatBot->disconnect();
		$this->logger->log('INFO', "The Bot is restarting.");
		exit();
	}

	/**
	 * This command handler shutdowns the bot.
	 * Note: This handler has not been not registered, only activated.
	 */
	public function shutdownCommand($message, $channel, $sender, $sendto, $args) {
		$msg = "The Bot is shutting down.";
		$this->chatBot->sendTell($msg, $sender);
		$this->chatBot->sendPrivate($msg, true);
		$this->chatBot->sendGuild($msg, true);

		$this->chatBot->disconnect();
		$this->logger->log('INFO', "The Bot is shutting down.");
		die("The Bot is shutting down.");
	}

	/**
	 * This command handler reloads the configuration file.
	 * Note: This handler has not been not registered, only activated.
	 */
	public function reloadconfigCommand($message, $channel, $sender, $sendto, $args) {
		global $configFile;
		$configFile->load();
		$vars = $configFile->getVars();

		// remove variables that shouldn't change without a restart
		unset($vars['name']);
		unset($vars['login']);
		unset($vars['password']);
		unset($vars['dimension']);

		unset($vars["DB Type"]);
		unset($vars["DB Name"]);
		unset($vars["DB Host"]);
		unset($vars["DB username"]);
		unset($vars["DB password"]);

		forEach ($vars as $key => $value) {
			$this->chatBot->vars[$key] = $value;

			// since the logger accesses the global $vars variable we must change the values there also
			$GLOBALS['vars'][$key] = $value;
		}

		$sendto->reply('Config file has been reloaded.');
	}

	/**
	 * This command handler lists log files available in the bot's log folder.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^logs$/i")
	 */
	public function logsCommand($message, $channel, $sender, $sendto, $args) {
		$files = $this->util->getFilesInDirectory($this->logger->get_logging_directory());
		sort($files);
		$blob = '';
		forEach ($files as $file) {
			$file_link = $this->text->make_chatcmd($file, "/tell <myname> logs $file");
			$errorLink = $this->text->make_chatcmd("ERROR", "/tell <myname> logs $file ERROR");
			$chatLink = $this->text->make_chatcmd("CHAT", "/tell <myname> logs $file CHAT");
			$blob .= "$file_link [$errorLink] [$chatLink] \n";
		}

		$msg = $this->text->make_blob('Log Files', $blob);
		$sendto->reply($msg);
	}

	/**
	 * This command handler returns contents of given log file.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^logs ([a-zA-Z0-9-_\\.]+)$/i")
	 * @Matches("/^logs ([a-zA-Z0-9-_\\.]+) (.+)$/i")
	 */
	public function logsFileCommand($message, $channel, $sender, $sendto, $args) {
		$filename = $this->logger->get_logging_directory() . "/" . $args[1];
		$readsize = $this->settingManager->get('max_blob_size') - 500;

		try {
			$file = new ReverseFileReader($filename);
			$contents = '';
			while (!$file->sof()) {
				$line = $file->getLine();

				// if user entered search criteria, filter by that
				if (isset($args[2]) && !preg_match("/{$args[2]}/i", $line)) {
					continue;
				}

				if (strlen($contents . $line) > $readsize) {
					break;
				}
				$contents .= $line;
			}
			$file->close();
			if (empty($contents)) {
				$msg = "File is empty or nothing matched your search criteria.";
			} else {
				if (isset($args[2])) {
					$contents = "Search: $args[2]\n\n" . $contents;
				}
				$msg = $this->text->make_blob($args[1], $contents);
			}
		} catch (Exception $e) {
			$msg = "Error: " . $e->getMessage();
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows system information.
	 * Note: This handler has not been not registered, only activated.
	 */
	public function systemCommand($message, $channel, $sender, $sendto, $args) {
		global $version;

		$sql = "SELECT count(*) AS count FROM players";
		$row = $this->db->queryRow($sql);
		$num_player_cache = $row->count;

		$num_friendlist = 0;
		forEach ($this->buddylistManager->buddyList as $key => $value) {
			if (!isset($value['name'])) {
				// skip the buddies that have been added but the server hasn't sent back an update yet
				continue;
			}

			$num_friendlist++;
		}

		$blob = "Name: <highlight><myname><end>\n";
		$blob .= "SuperAdmin: <highlight>'{$this->chatBot->vars['SuperAdmin']}'<end>\n";
		$blob .= "Guild: <highlight>'<myguild>' (" . $this->chatBot->vars['my_guild_id'] . ")<end>\n\n";

		$blob .= "Budabot: <highlight>$version<end>\n";
		$blob .= "PHP: <highlight>" . phpversion() . "<end>\n";
		$blob .= "OS: <highlight>" . php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m') . "<end>\n";
		$blob .= "Database: <highlight>" . $this->db->get_type() . "<end>\n\n";

		$blob .= "Current Memory Usage: <highlight>" . $this->util->bytes_convert(memory_get_usage()) . "<end>\n";
		$blob .= "Current Memory Usage (Real): <highlight>" . $this->util->bytes_convert(memory_get_usage(1)) . "<end>\n";
		if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
			$blob .= "Peak Memory Usage: <highlight>" . $this->util->bytes_convert(memory_get_peak_usage()) . "<end>\n";
			$blob .= "Peak Memory Usage (Real): <highlight>" . $this->util->bytes_convert(memory_get_peak_usage(1)) . "<end>\n\n";
		}
		
		$blob .= "Runkit Classloading: <highlight>" . (USE_RUNKIT_CLASS_LOADING ? "enabled" : "disabled") . "<end>\n";

		$date_string = $this->util->unixtime_to_readable(time() - $this->chatBot->vars['startup']);
		$blob .= "Uptime: <highlight>$date_string<end>\n\n";

		$eventnum = 0;
		forEach ($this->eventManager->events as $type => $events) {
			$eventnum += count($events);
		}

		$numAliases = count($this->commandAlias->cmd_aliases);

		$blob .= "Number of active tell commands: <highlight>" . (count($this->commandManager->commands['msg']) - $numAliases) . "<end>\n";
		$blob .= "Number of active private channel commands: <highlight>" . (count($this->commandManager->commands['priv']) - $numAliases) . "<end>\n";
		$blob .= "Number of active guild channel commands: <highlight>" . (count($this->commandManager->commands['guild']) - $numAliases) . "<end>\n";
		$blob .= "Number of active subcommands: <highlight>" . count($this->subcommandManager->subcommands) . "<end>\n";
		$blob .= "Number of active command aliases: <highlight>" . $numAliases . "<end>\n";
		$blob .= "Number of active events: <highlight>" . $eventnum . "<end>\n";
		$blob .= "Number of active help commands: <highlight>" . count($this->helpManager->getAllHelpTopics(null)) . "<end>\n\n";

		$blob .= "Number of characters on the friendlist: <highlight>$num_friendlist / " . count($this->buddylistManager->buddyList) . "<end>\n";
		$blob .= "Number of characters in the private channel: <highlight>" . count($this->chatBot->chatlist) . "<end>\n";
		$blob .= "Number of guild members: <highlight>" . count($this->chatBot->guildmembers) . "<end>\n";
		$blob .= "Number of character infos in cache: <highlight>" . $num_player_cache . "<end>\n";
		$blob .= "Number of messages in the chat queue: <highlight>" . count($this->chatBot->chatqueue->queue) . "<end>\n\n";

		$blob .= "Public Channels:\n";
		forEach ($this->chatBot->grp as $gid => $status) {
			$string = unpack("N", substr($gid, 1));
			$blob .= "<tab><highlight>'{$this->chatBot->gid[$gid]}'<end> (" . ord(substr($gid, 0, 1)) . " " . $string[1] . ")\n";
		}

		$msg = $this->text->make_blob('System Info', $blob);
		$sendto->reply($msg);
	}

	/**
	 * This command handler check effective access level of a character.
	 *
	 * @HandlesCommand("checkaccess")
	 * @Matches("/^checkaccess$/i")
	 * @Matches("/^checkaccess (.+)$/i")
	 */
	public function checkaccessCommand($message, $channel, $sender, $sendto, $args) {
		if (isset($args[1])) {
			$name = ucfirst(strtolower($args[1]));
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
	 * @Matches("/^macro (.+)$/i")
	 */
	public function macroCommand($message, $channel, $sender, $sendto, $args) {
		$commands = explode("|", $args[1]);
		forEach ($commands as $commandString) {
			$this->commandManager->process($channel, $commandString, $sender, $sendto);
		}
	}

	/**
	 * This event handler is called every hour to keep MySQL connection active.
	 * Note: This handler has not been not registered, only activated.
	 */
	public function refreshMySQLConnectionEvent($eventObj) {
		// if the bot doesn't query the mysql database for 8 hours the db connection is closed
		$this->logger->log('DEBUG', "Pinging database");
		$sql = "SELECT * FROM settings_<myname>";
		$this->db->query($sql);
	}

	/**
	 * This event handler is called every 2 seconds to reduce spam values.
	 * Note: This handler has not been not registered, only activated.
	 */
	public function reduceSpamValuesEvent($eventObj) {
		if (isset($this->chatBot->spam)) {
			forEach ($this->chatBot->spam as $key => $value){
				if ($value > 0) {
					$this->chatBot->spam[$key] = $value - 10;
				} else {
					$this->chatBot->spam[$key] = 0;
				}
			}
		}
	}

	/**
	 * This event handler is called on 'connect' event.
	 * Note: This handler has not been not registered, only activated.
	 */
	public function onConnectEvent($eventObj) {
		// send Admin(s) a tell that the bot is online
		forEach ($this->adminManager->admins as $name => $info) {
			if ($info["level"] == 4 && $this->buddylistManager->is_online($name) == 1) {
				$this->chatBot->sendTell("<myname> is <green>online<end>. For updates or help use the Budabot Forums <highlight>http://budabot.com<end>", $name);
			}
		}

		// send a message to guild channel
		$this->chatBot->sendGuild("Logon Complete :: All systems ready to use.", true);
		$this->chatBot->sendPrivate("Logon Complete :: All systems ready to use.", true);
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
