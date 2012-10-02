<?php

class Preferences {
	public static function save($sender, $name, $value) {
		$db = Registry::getInstance('db');

		$sender = ucfirst(strtolower($sender));
		$name = strtolower($name);

		if (Preferences::get($sender, $name) === false) {
			$db->exec("INSERT INTO preferences_<myname> (sender, name, value) VALUES (?, ?, ?)", $sender, $name, $value);
		} else {
			$db->exec("UPDATE preferences_<myname> SET value = ? WHERE sender = ? AND name = ?", $value, $sender, $name);
		}
	}

	public static function get($sender, $name) {
		$db = Registry::getInstance('db');

		$sender = ucfirst(strtolower($sender));
		$name = strtolower($name);

		$row = $db->queryRow("SELECT * FROM preferences_<myname> WHERE sender = ? AND name = ?", $sender, $name);
		if ($row === null) {
			return false;
		} else {
			return $row->value;
		}
	}
}

?>
