<?php

class Whitelist {
	public static function add($user, $sender) {
		global $chatBot;
		$db = Registry::getInstance('db');
		$user = ucfirst(strtolower($user));
		$sender = ucfirst(strtolower($sender));

		if ($user == '' || $sender == '') {
			return "User or sender is blank";
		}
	
		$data = $db->query("SELECT * FROM whitelist WHERE name = ?", $user);
		if (count($data) != 0) {
			return "Error! $user already added to the whitelist";
		} else {
			$db->exec("INSERT INTO whitelist (name, added_by, added_dt) VALUES (?, ?, ?)", $user, $sender, time());
			return "$user has been added to the whitelist";
		}
	}
	
	public static function remove($user) {
		global $chatBot;
		$db = Registry::getInstance('db');
		$user = ucfirst(strtolower($user));

		if ($user == '') {
			return "User is blank";
		}
	
		$data = $db->query("SELECT * FROM whitelist WHERE name = ?", $user);
		if (count($data) == 0) {
			return "Error! $user is not on the whitelist";
		} else {
			$db->exec("DELETE FROM whitelist WHERE name = ?", $user);
			return "$user has been removed from the whitelist";
		}
	}
	
	public static function check($user) {
		global $chatBot;
		$db = Registry::getInstance('db');
		$user = ucfirst(strtolower($user));

		$data = $db->query("SELECT * FROM whitelist WHERE name = ?", $user);
		if (count($data) == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public static function all() {
		global $chatBot;
		$db = Registry::getInstance('db');
	
		return $db->query("SELECT * FROM whitelist ORDER BY name ASC");
	}
}

?>