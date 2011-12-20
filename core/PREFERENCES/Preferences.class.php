<?php

class Preferences {
	public static function save($sender, $name, $value) {
		$db = DB::get_instance();
		
		$sender = ucfirst(strtolower($sender));
		$name = strtolower($name);
		$value = str_replace("'", "''", $value);
		
		if (Preferences::get($sender, $name) === false) {
			$db->exec("INSERT INTO preferences_<myname> (sender, name, value) VALUES ('{$sender}', '{$name}', '{$value}')");
		} else {
			$db->exec("UPDATE preferences_<myname> SET value = '{$value}' WHERE sender = '{$sender}' AND name = '{$name}'");
		}
	}
	
	public static function get($sender, $name) {
		$db = DB::get_instance();
		
		$sender = ucfirst(strtolower($sender));
		$name = strtolower($name);
		
		$row = $db->queryRow("SELECT * FROM preferences_<myname> WHERE sender = '$sender' AND name = '$name'");
		if ($row === null) {
			return false;
		} else {
			return $row->value;
		}
	}
}

?>