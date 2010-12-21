<?php

class Alts {
	public static function get_main($player) {
		$db = db::get_instance();
		
		$sql = "SELECT `alt`, `main` FROM `alts` WHERE `alt` LIKE '$player'";
		$db->query($sql);
		$main = $db->fObject();

		if ($main === null) {
			return $player;
		} else {
			return $main;
		}
	}
	
	public static function get_alts($main) {
		$db = db::get_instance();
		
		$sql = "SELECT `alt`, `main` FROM `alts` WHERE `main` LIKE '$main'";
		$db->query($sql);
		return $db->fObject('all');
	}
	
	public static function add_alt($main, $alt) {
		$db = db::get_instance();
		
		$main = ucfirst(strtolower($main));
		$alt = ucfirst(strtolower($alt));
		
		$sql = "INSERT INTO `alts` (`alt`, `main`) VALUES ('$alt', '$main')";
		return $db->exec($sql);
	}
	
	public static function rem_alt($main, $alt) {
		$db = db::get_instance();
		
		$sql = "DELETE FROM `alts` WHERE `alt` LIKE '$alt' AND `main` LIKE '$main'";
		return $db->exec($sql);
	}
}

?>