<?php

class Timer {
	public static function add_timer($name, $owner, $mode, $timer, $callback = null, $callback_param = null) {
		global $chatBot;

		$db = DB::get_instance();

		$chatBot->data["timers"][] = (object)array("name" => $name, "owner" => $owner, "mode" => $mode, "timer" => $timer, "settime" => time(), 'callback' => $callback, 'callback_param' => $callback_param);
		$sql = "INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`, `callback`, `callback_param`) " .
			"VALUES ('".str_replace("'", "''", $name)."', '$owner', '$mode', $timer, ".time().", '".str_replace("'", "''", $callback)."', '".str_replace("'", "''", $callback_param)."')";
		$db->exec($sql);
	}
	
	public static function remove_timer($key) {
		global $chatBot;

		$db = DB::get_instance();

		$db->exec("DELETE FROM timers_<myname> WHERE `name` LIKE '" . str_replace("'", "''", $chatBot->data["timers"][$key]->name) . "' AND `owner` = '{$chatBot->data["timers"][$key]->owner}'");
		unset($chatBot->data["timers"][$key]);
	}

	public static function get($name) {
		global $chatBot;
	
		forEach ($chatBot->data["timers"] as $timer) {
			if (strcasecmp($name, $timer->name) == 0) {
				return $timer;
			}
		}
		return null;
	}	
}

?>
