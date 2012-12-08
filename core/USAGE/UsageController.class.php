<?php

/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command       = 'usage',
 *		accessLevel   = 'guild',
 *		description   = 'Shows usage stats',
 *		help          = 'usage.txt',
 *		defaultStatus = '1'
 *	)
 */
class UsageController {
	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $http;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $util;
	
	/** @Inject */
	public $text;

	/** @Inject */
	public $chatBot;

	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'usage');
		
		$this->settingManager->add($this->moduleName, "record_usage_stats", "Record usage stats", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, 'botid', 'Botid', 'noedit', 'text', '');
		$this->settingManager->add($this->moduleName, 'last_submitted_stats', 'last_submitted_stats', 'noedit', 'text', 0);
	}
	
	/**
	 * @HandlesCommand("usage")
	 * @Matches("/^usage player ([0-9a-z-]+)$/i")
	 * @Matches("/^usage player ([0-9a-z-]+) ([a-z0-9]+)$/i")
	 */
	public function usagePlayerCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 3) {
			$time = $this->util->parseTime($args[2]);
			if ($time == 0) {
				$msg = "Please enter a valid time.";
				$sendto->reply($msg);
				return;
			}
			$time = $time;
		} else {
			$time = 604800;
		}

		$timeString = $this->util->unixtime_to_readable($time);
		$time = time() - $time;
	
		$player = ucfirst(strtolower($args[1]));
	
		// most used commands
		$sql = "SELECT command, COUNT(command) AS count FROM usage_<myname> WHERE sender = ? AND dt > ? GROUP BY command ORDER BY count DESC";
		$data = $this->db->query($sql, $player, $time);

		if (count($data) > 0) {
			$blob .= '';
			forEach ($data as $row) {
				$blob .= "<highlight>{$row->command}<end> ({$row->count})\n";
			}

			$msg = $this->text->make_blob("Usage for $player ({$timeString})", $blob);
		} else {
			$msg = "No usage statistics found for <highlight>$player<end>.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("usage")
	 * @Matches("/^usage$/i")
	 * @Matches("/^usage ([a-z0-9]+)$/i")
	 */
	public function usageCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 2) {
			$time = $this->util->parseTime($args[1]);
			if ($time == 0) {
				$msg = "Please enter a valid time.";
				$sendto->reply($msg);
				return;
			}
			$time = $time;
		} else {
			$time = 604800;
		}

		$timeString = $this->util->unixtime_to_readable($time);
		$time = time() - $time;
		$limit = 25;
		
		// channel usage
		$sql = "SELECT type, COUNT(type) cnt FROM usage_<myname> WHERE dt > ? GROUP BY type ORDER BY type";
		$data = $this->db->query($sql, $time);
		
		$blob = "<header2>Channel Usage<end>\n";
		forEach ($data as $row) {
			if ($row->type == "msg") {
				$blob .= "Number of commands executed in tells: <highlight>$row->cnt<end>\n";
			} else if ($row->type == "priv") {
				$blob .= "Number of commands executed in private channel: <highlight>$row->cnt<end>\n";
			} else if ($row->type == "guild") {
				$blob .= "Number of commands executed in guild channel: <highlight>$row->cnt<end>\n";
			}
		}
		$blob .= "\n";
		
		// most used commands
		$sql = "SELECT command, COUNT(command) AS count FROM usage_<myname> WHERE dt > ? GROUP BY command ORDER BY count DESC LIMIT $limit";
		$data = $this->db->query($sql, $time);

		$blob .= "<header2>Most Used Commands<end>\n";
		forEach ($data as $row) {
			$blob .= "<highlight>{$row->command}<end> ({$row->count})\n";
		}

		// users who have used the most commands
		$sql = "SELECT sender, COUNT(sender) AS count FROM usage_<myname> WHERE dt > ? GROUP BY sender ORDER BY count DESC LIMIT $limit";
		$data = $this->db->query($sql, $time);

		$blob .= "\n<header2>Most Active Users<end>\n";
		forEach ($data as $row) {
			$senderLink = $this->text->make_chatcmd($row->sender, "/tell <myname> usage player $row->sender");
			$blob .= "<highlight>{$senderLink}<end> ({$row->count})\n";
		}

		$msg = $this->text->make_blob("Usage Statistics ({$timeString})", $blob);
		$sendto->reply($msg);
	}

	public function record($type, $cmd, $sender, $commandHandler) {
		// don't record stats for !grc command or command aliases
		if ($cmd == 'grc' || $commandHandler->file == CommandAlias::ALIAS_HANDLER) {
			return;
		}

		$sql = "INSERT INTO usage_<myname> (type, command, sender, dt) VALUES (?, ?, ?, ?)";
		$this->db->exec($sql, $type, $cmd, $sender, time());
	}

	/**
	 * @Event("24hrs")
	 * @Description("Submits anonymous usage stats to Budabot website")
	 * @DefaultStatus("1")
	 */
	public function submitUsage($eventObj) {
		$debug = false;
		$time = time();
		$settingName = 'last_submitted_stats';
		$lastSubmittedStats = $this->settingManager->get($settingName);

		$postArray['stats'] = json_encode($this->getUsageInfo($lastSubmittedStats, $debug));

		$url = 'http://stats.jkbff.com/submitUsage.php';
		$this->http->post($url)->withQueryParams($postArray);

		$this->settingManager->save($settingName, $time);
	}

	public function getUsageInfo($lastSubmittedStats, $debug = false) {
		global $version;

		$botid = $this->settingManager->get('botid');
		if ($botid == '') {
			$botid = $this->util->genRandomString(20);
			$this->settingManager->save('botid', $botid);
		}

		$sql = "SELECT type, command FROM usage_<myname> WHERE dt >= ?";
		$data = $this->db->query($sql, $lastSubmittedStats);

		$settings = array();
		$settings['dimension'] = $this->chatBot->vars['dimension'];
		$settings['is_guild_bot'] = ($this->chatBot->vars['my_guild'] == '' ? '0' : '1');
		$settings['guildsize'] = $this->getGuildSizeClass(count($this->chatBot->guildmembers));
		$settings['using_chat_proxy'] = $this->chatBot->vars['use_proxy'];
		$settings['symbol'] = $this->settingManager->get('symbol');
		$settings['spam_protection'] = $this->settingManager->get('spam_protection');
		$settings['db_type'] = $this->db->get_type();
		$settings['bot_version'] = $version;
		$settings['using_svn'] = (file_exists("./modules/SVN_MODULE/svn.php") === true ? '1' : '0');
		$settings['os'] = (isWindows() === true ? 'Windows' : 'Other');
		$settings['relay_enabled'] = ($this->settingManager->get('relaybot') == 'Off' ? '0' : '1');
		$settings['relay_type'] = $this->settingManager->get('relaytype');
		$settings['alts_inherit_admin'] = $this->settingManager->get('alts_inherit_admin');
		$settings['bbin_status'] = $this->settingManager->get('bbin_status');
		$settings['irc_status'] = $this->settingManager->get('irc_status');
		$settings['first_and_last_alt_only'] = $this->settingManager->get('first_and_last_alt_only');
		$settings['aodb_db_version'] = $this->settingManager->get('aodb_db_version');
		$settings['guild_admin_access_level'] = $this->settingManager->get('guild_admin_access_level');
		$settings['guild_admin_rank'] = $this->settingManager->get('guild_admin_rank');
		$settings['max_blob_size'] = $this->settingManager->get('max_blob_size');
		$settings['logon_delay'] = $this->settingManager->get('logon_delay');

		$obj = new stdClass;
		$obj->id = sha1($botid . $this->chatBot->vars['name'] . $this->chatBot->vars['dimension']);
		$obj->version = "1.3";
		$obj->debug = ($debug == true ? '1' : '0');
		$obj->commands = $data;
		$obj->settings = $settings;

		return $obj;
	}

	public function getGuildSizeClass($size) {
		$guildClass = "";
		if ($size == 0) {
			$guildClass = "class0";
		} else if ($size < 10) {
			$guildClass = "class1";
		} else if ($size < 30) {
			$guildClass = "class2";
		} else if ($size < 150) {
			$guildClass = "class3";
		} else if ($size < 300) {
			$guildClass = "class4";
		} else if ($size < 650) {
			$guildClass = "class5";
		} else if ($size < 1000) {
			$guildClass = "class6";
		} else {
			$guildClass = "class7";
		}
		return $guildClass;
	}
}

?>
