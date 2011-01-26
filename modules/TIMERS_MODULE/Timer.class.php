<?php

class Timer {
	public static function add_timer($name, $char, $type, $timer) {
		global $chatBot;
		
		$db = db::get_instance();
	
		$chatBot->vars["Timers"][] = (object)array("name" => $name, "owner" => $char, "mode" => $type, "timer" => $timer, "settime" => time());
		$db->exec("INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`) VALUES ('".str_replace("'", "''", $name)."', '$char', '$type', $timer, ".time().")");
	}
	
	public static function remove_timer($key, $name, $owner) {
		global $chatBot;
		
		$db = db::get_instance();
	
		unset($chatBot->vars["Timers"][$key]);
		$db->exec("DELETE FROM timers_<myname> WHERE `name` LIKE '" . str_replace("'", "''", $name) . "' AND `owner` = '$owner'");
	}
}

?>
