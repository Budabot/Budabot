<?php

class Usage {
	public static function record($type, $cmd, $sender) {
		$db = DB::get_instance();

		$sql = "INSERT INTO usage_<myname> (type, command, sender, dt) VALUES ('$type', '$cmd', '$sender', " . time() . ")";
		$db->exec($sql);
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
		$db = DB::get_instance();
		global $version;

		$botid = Setting::get('botid');
		if ($botid == '') {
			$botid = Util::genRandomString(20);
			Setting::add("USAGE", 'botid', 'botid', 'noedit', 'text', $botid);
		}

		$sql = "SELECT type, command FROM usage_<myname> WHERE dt >= $lastSubmittedStats";
		$db->query($sql);
		$data = $db->fObject('all');

		$settings = array();
		$settings['dimension'] = $chatBot->vars['dimension'];
		$settings['is_guild_bot'] = ($chatBot->vars['my_guild'] == '' ? '0' : '1');
		$settings['guildsize'] = count($chatBot->guildmembers);
		$settings['using_chat_proxy'] = $chatBot->vars['use_proxy'];
		$settings['symbol'] = Setting::get('symbol');
		$settings['spam_protection'] = Setting::get('spam_protection');
		$settings['db_type'] = $db->get_type();
		$settings['bot_version'] = $version;
		$settings['using_svn'] = (file_exists("./modules/SVN_MODULE/svn.php") === true ? '1' : '0');
		$settings['os'] = (isWindows() === true ? 'Windows' : 'Other');
		$settings['relay_enabled'] = (Setting::get('relaybot') == 'Off' ? '0' : '1');
		$settings['relay_type'] = Setting::get('relaytype');

		$obj = new stdClass;
		$obj->id = sha1($botid . $chatBot->vars['name'] . $chatBot->vars['dimension']);
		$obj->version = "1.1";
		$obj->debug = ($debug == true ? '1' : '0');
		$obj->commands = $data;
		$obj->settings = $settings;

		return $obj;
	}
}

?>
