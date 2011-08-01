<?php

class Timer {
	public static function add_timer($name, $char, $type, $timer, $callback = null, $callback_param = null) {
		global $chatBot;

		$db = DB::get_instance();

		$chatBot->data["timers"][] = (object)array("name" => $name, "owner" => $char, "mode" => $type, "timer" => $timer, "settime" => time(), 'callback' => $callback, 'callback_param' => $callback_param);
		$sql = "INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`, `callback`, `callback_param`) " .
			"VALUES ('".str_replace("'", "''", $name)."', '$char', '$type', $timer, ".time().", '".str_replace("'", "''", $callback)."', '".str_replace("'", "''", $callback_param)."')";
		$db->exec($sql);
	}
	
	public static function remove_timer($key) {
		global $chatBot;

		$db = DB::get_instance();

		$db->exec("DELETE FROM timers_<myname> WHERE `name` LIKE '" . str_replace("'", "''", $chatBot->data["timers"][$key]->name) . "' AND `owner` = '{$chatBot->data["timers"][$key]->owner}'");
		unset($chatBot->data["timers"][$key]);
	}
}

?>
