<?php

class Timer {
	public static function add($name, $owner, $mode, $timer, $callback = null, $callback_param = null) {
		$chatBot = Registry::getInstance('chatBot');
		$db = Registry::getInstance('db');

		$chatBot->data["timers"][strtolower($name)] = (object)array("name" => $name, "owner" => $owner, "mode" => $mode, "timer" => $timer, "settime" => time(), 'callback' => $callback, 'callback_param' => $callback_param);
		$sql = "INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`, `callback`, `callback_param`) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$db->exec($sql, $name, $owner, $mode, $timer, time(), $callback, $callback_param);
	}
	
	public static function remove($name) {
		$chatBot = Registry::getInstance('chatBot');
		$db = Registry::getInstance('db');

		$db->exec("DELETE FROM timers_<myname> WHERE `name` LIKE ?", $name);
		unset($chatBot->data["timers"][strtolower($name)]);
	}

	public static function get($name) {
		$chatBot = Registry::getInstance('chatBot');

		return $chatBot->data["timers"][strtolower($name)];
	}

	public static function getAllTimers() {
		$chatBot = Registry::getInstance('chatBot');

		return $chatBot->data["timers"];
	}
}

?>
