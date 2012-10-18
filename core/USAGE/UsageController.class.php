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
 *		defaultStatus = 1
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
	public $setting;

	/** @Inject */
	public $util;
	
	/** @Inject */
	public $text;

	/** @Inject */
	public $chatBot;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'usage');
		
		$this->setting->add($this->moduleName, "record_usage_stats", "Enable recording usage stats", "edit", "options", "1", "true;false", "1;0");
		$this->setting->add($this->moduleName, 'botid', 'Botid', 'noedit', 'text', '');
		$this->setting->add($this->moduleName, 'last_submitted_stats', 'last_submitted_stats', 'noedit', 'text', 0);
	}
	
	/**
	 * @HandlesCommand("usage")
	 * @Matches("/^usage$/i")
	 * @Matches("/^usage ([a-z0-9]+)$/i")
	 */
	public function cloakCommand($message, $channel, $sender, $sendto, $args) {
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

		// most used commands
		$sql = "SELECT command, COUNT(command) AS count FROM usage_<myname> WHERE dt > ? GROUP BY command ORDER BY count DESC LIMIT $limit";
		$data = $this->db->query($sql, $time);

		$blob = "<header2> ::: Most Used Commands ::: <end>\n";
		forEach ($data as $row) {
			$blob .= "<highlight>{$row->command}<end> ({$row->count})\n";
		}

		// users who have used the most commands
		$sql = "SELECT sender, COUNT(sender) AS count FROM usage_<myname> WHERE dt > ? GROUP BY sender ORDER BY count DESC LIMIT $limit";
		$data = $this->db->query($sql, $time);

		$blob .= "\n<header2> ::: Most Active Users ::: <end>\n";
		forEach ($data as $row) {
			$blob .= "<highlight>{$row->sender}<end> ({$row->count})\n";
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
		$lastSubmittedStats = $this->setting->get($settingName);

		$postArray['stats'] = json_encode($this->getUsageInfo($lastSubmittedStats, $debug));

		$url = 'stats.jkbff.com/submitUsage.php';
		$mycurl = new MyCurl($url);
		$mycurl->setPost($postArray);
		$mycurl->createCurl();
		if ($debug) {
			echo $mycurl->__toString() . "\n";
		}

		$this->setting->save($settingName, $time);
	}

	public function getUsageInfo($lastSubmittedStats, $debug = false) {
		global $version;

		$botid = $this->setting->get('botid');
		if ($botid == '') {
			$botid = $this->util->genRandomString(20);
			$this->setting->save('botid', $botid);
		}

		$sql = "SELECT type, command FROM usage_<myname> WHERE dt >= ?";
		$data = $this->db->query($sql, $lastSubmittedStats);

		$settings = array();
		$settings['dimension'] = $this->chatBot->vars['dimension'];
		$settings['is_guild_bot'] = ($this->chatBot->vars['my_guild'] == '' ? '0' : '1');
		$settings['guildsize'] = $this->getGuildSizeClass(count($this->chatBot->guildmembers));
		$settings['using_chat_proxy'] = $this->chatBot->vars['use_proxy'];
		$settings['symbol'] = $this->setting->get('symbol');
		$settings['spam_protection'] = $this->setting->get('spam_protection');
		$settings['db_type'] = $this->db->get_type();
		$settings['bot_version'] = $version;
		$settings['using_svn'] = (file_exists("./modules/SVN_MODULE/svn.php") === true ? '1' : '0');
		$settings['os'] = (isWindows() === true ? 'Windows' : 'Other');
		$settings['relay_enabled'] = ($this->setting->get('relaybot') == 'Off' ? '0' : '1');
		$settings['relay_type'] = $this->setting->get('relaytype');
		$settings['alts_inherit_admin'] = $this->setting->get('alts_inherit_admin');
		$settings['bbin_status'] = $this->setting->get('bbin_status');
		$settings['irc_status'] = $this->setting->get('irc_status');
		$settings['first_and_last_alt_only'] = $this->setting->get('first_and_last_alt_only');
		$settings['aodb_db_version'] = $this->setting->get('aodb_db_version');
		$settings['guild_admin_access_level'] = $this->setting->get('guild_admin_access_level');
		$settings['guild_admin_rank'] = $this->setting->get('guild_admin_rank');
		$settings['max_blob_size'] = $this->setting->get('max_blob_size');
		$settings['logon_delay'] = $this->setting->get('logon_delay');

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
