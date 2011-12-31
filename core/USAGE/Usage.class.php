<?php

class Usage {
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $setting;

	public function record($type, $cmd, $sender) {
		$sql = "INSERT INTO usage_<myname> (type, command, sender, dt) VALUES (?, ?, ?, ?)";
		$this->db->exec($sql, $type, $cmd, $sender, time());
	}

	public function submitUsage() {
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
		global $chatBot;
		global $version;

		$botid = $this->setting->get('botid');
		if ($botid == '') {
			$botid = Util::genRandomString(20);
			$this->setting->add("USAGE", 'botid', 'botid', 'noedit', 'text', $botid);
		}

		$sql = "SELECT type, command FROM usage_<myname> WHERE dt >= ?";
		$data = $this->db->query($sql, $lastSubmittedStats);

		$settings = array();
		$settings['dimension'] = $chatBot->vars['dimension'];
		$settings['is_guild_bot'] = ($chatBot->vars['my_guild'] == '' ? '0' : '1');
		$settings['guildsize'] = $this->getGuildSizeClass(count($chatBot->guildmembers));
		$settings['using_chat_proxy'] = $chatBot->vars['use_proxy'];
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
		$obj->id = sha1($botid . $chatBot->vars['name'] . $chatBot->vars['dimension']);
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
