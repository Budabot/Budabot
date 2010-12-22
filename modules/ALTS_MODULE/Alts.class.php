<?php

class Alts {
	public static function get_main($player) {
		$db = db::get_instance();
		
		$sql = "SELECT `alt`, `main` FROM `alts` WHERE `alt` LIKE '$player'";
		$db->query($sql);
		$row = $db->fObject();

		if ($row === null) {
			return $player;
		} else {
			return $row->main;
		}
	}
	
	public static function get_alts($main) {
		$db = db::get_instance();
		
		$sql = "SELECT `alt`, `main` FROM `alts` WHERE `main` LIKE '$main'";
		$db->query($sql);
		
		$data = $db->fObject('all');
		$array = array();
		forEach ($data as $row) {
			$array[] = $row->alt;
		}
		return $array;
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