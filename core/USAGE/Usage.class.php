<?php

class Usage {
	public function record($type, $cmd, $sender) {
		$db = DB::get_instance();

		$sql = "INSERT INTO usage_<myname> (type, command, sender, dt) VALUES ('$type', '$cmd', '$sender', " . time() . ")";
		$db->exec($sql);
	}

	public function submitUsage() {
		global $chatBot;
		$db = DB::get_instance();

		$time = time();
		$settingName = 'last_submitted_stats';
		$lastSubmittedStats = Setting::get($settingName);
		
		$botid = Setting::get('botid');
		if ($botid == '') {
			$botid = Util::genRandomString(20);
			Setting::add("USAGE", 'botid', 'botid', 'noedit', 'text', $botid);
		}

		$sql = "SELECT type, command FROM usage_<myname> WHERE dt >= $lastSubmittedStats";
		$db->query($sql);
		$data = $db->fObject('all');

		if (count($data) > 0) {
			$obj = new stdClass;
			$obj->id = sha1($botid . $chatBot->vars['name'] . $chatBot->vars['dimension']);
			$obj->dimension = $chatBot->vars['dimension'];
			$obj->commands = $data;
			$obj->version = "1.0";

			$postArray['stats'] = json_encode($obj);

			$url = 'stats.jkbff.com/submitUsage.php';
			$mycurl = new MyCurl($url);
			$mycurl->setPost($postArray);
			$mycurl->createCurl();

			Setting::save($settingName, $time);
		}
	}
}

?>
