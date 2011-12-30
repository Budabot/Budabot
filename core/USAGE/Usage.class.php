<?php

class Usage {
	public static function record($type, $cmd, $sender) {
		global $chatBot;
		$db = $chatBot->getInstance('db');

		$sql = "INSERT INTO usage_<myname> (type, command, sender, dt) VALUES (?, ?, ?, ?)";
		$db->exec($sql, $type, $cmd, $sender, time());
	}

	public static function submitUsage($debug = false) {
		$time = time();
		$settingName = 'last_submitted_stats';
		$lastSubmittedStats = Setting::get($settingName);

		$postArray['stats'] = json_encode(Usage::getUsageInfo($lastSubmittedStats, $debug));

		$url = 'stats.jkbff.com/submitUsage.php';
		$mycurl = new MyCurl($url);
		$mycurl->setPost($postArray);
		$mycurl->createCurl();
		if ($debug) {
			echo $mycurl->__toString() . "\n";
		}

		Setting::save($settingName, $time);
	}
	
	public static function getUsageInfo($lastSubmittedStats, $debug = false) {
		global $chatBot;
		$db = $chatBot->getInstance('db');
		global $version;

		$botid = Setting::get('botid');
		if ($botid == '') {
			$botid = Util::genRandomString(20);
			Setting::add("USAGE", 'botid', 'botid', 'noedit', 'text', $botid);
		}

		$sql = "SELECT type, command FROM usage_<myname> WHERE dt >= ?";
		$data = $db->query($sql, $lastSubmittedStats);

		$settings = array();
		$settings['dimension'] = $chatBot->vars['dimension'];
		$settings['is_guild_bot'] = ($chatBot->vars['my_guild'] == '' ? '0' : '1');
		$settings['guildsize'] = Usage::getGuildSizeClass(count($chatBot->guildmembers));
		$settings['using_chat_proxy'] = $chatBot->vars['use_proxy'];
		$settings['symbol'] = Setting::get('symbol');
		$settings['spam_protection'] = Setting::get('spam_protection');
		$settings['db_type'] = $db->get_type();
		$settings['bot_version'] = $version;
		$settings['using_svn'] = (file_exists("./modules/SVN_MODULE/svn.php") === true ? '1' : '0');
		$settings['os'] = (isWindows() === true ? 'Windows' : 'Other');
		$settings['relay_enabled'] = (Setting::get('relaybot') == 'Off' ? '0' : '1');
		$settings['relay_type'] = Setting::get('relaytype');
		$settings['alts_inherit_admin'] = Setting::get('alts_inherit_admin');
		$settings['bbin_status'] = Setting::get('bbin_status');
		$settings['irc_status'] = Setting::get('irc_status');
		$settings['first_and_last_alt_only'] = Setting::get('first_and_last_alt_only');
		$settings['aodb_db_version'] = Setting::get('aodb_db_version');
		$settings['guild_admin_access_level'] = Setting::get('guild_admin_access_level');
		$settings['guild_admin_rank'] = Setting::get('guild_admin_rank');
		$settings['max_blob_size'] = Setting::get('max_blob_size');
		$settings['logon_delay'] = Setting::get('logon_delay');

		$obj = new stdClass;
		$obj->id = sha1($botid . $chatBot->vars['name'] . $chatBot->vars['dimension']);
		$obj->version = "1.3";
		$obj->debug = ($debug == true ? '1' : '0');
		$obj->commands = $data;
		$obj->settings = $settings;

		return $obj;
	}
	
	public static function getGuildSizeClass($size) {
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
