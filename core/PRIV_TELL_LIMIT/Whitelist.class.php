<?php

class Whitelist {
	public static function add($user, $sender) {
		global $db;
		$user = ucfirst(strtolower($user));
		$sender = ucfirst(strtolower($sender));

		if ($user == '' || $sender == '') {
			return "User or sender is blank";
		}
	
		$db->query("SELECT * FROM whitelist WHERE name = '$user'");
		if ($db->numrows() != 0) {
			return "Error! $user already added to the whitelist";
		} else {
			$db->exec("INSERT INTO whitelist (name, added_by, added_dt) VALUES ('$user', '$sender', CURRENT_TIMESTAMP)");
			return "$user has been added to the whitelist";
		}
	}
	
	public static function remove($user) {
		global $db;
		$user = ucfirst(strtolower($user));

		if ($user == '') {
			return "User is blank";
		}
	
		$db->query("SELECT * FROM whitelist WHERE name = '$user'");
		if ($db->numrows() == 0) {
			return "Error! $user is not on the whitelist";
		} else {
			$db->exec("DELETE FROM whitelist WHERE name = '$user'");
			return "$user has been removed from the whitelist";
		}
	}
	
	public static function check($user) {
		global $db;
		$user = ucfirst(strtolower($user));

		$db->query("SELECT * FROM whitelist WHERE name = '$user'");
		if ($db->numrows() == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public static function all() {
		global $db;
	
		$db->query("SELECT * FROM whitelist ORDER BY name ASC");
		return $db->fObject('all');
	}
}

?>