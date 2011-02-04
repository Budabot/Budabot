<?php

class Alts {
	public static function get_main($player) {
		$db = DB::get_instance();
		
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
		$db = DB::get_instance();
		
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
		$db = DB::get_instance();
		
		$main = ucfirst(strtolower($main));
		$alt = ucfirst(strtolower($alt));
		
		$sql = "INSERT INTO `alts` (`alt`, `main`) VALUES ('$alt', '$main')";
		return $db->exec($sql);
	}
	
	public static function rem_alt($main, $alt) {
		$db = DB::get_instance();
		
		$sql = "DELETE FROM `alts` WHERE `alt` LIKE '$alt' AND `main` LIKE '$main'";
		return $db->exec($sql);
	}
	
	public static function get_alts_blob($char) {
		global $chatBot;
	
		$main = Alts::get_main($char);
		$alts = Alts::get_alts($main);

		if (count($alts) == 0) {
			return null;
		}

		$list = "<header>::::: Alternative Character List :::::<end> \n \n";
		$list .= ":::::: Main Character\n";
		$list .= "<tab><tab>{$main}";
		$character = Player::get_by_name($main);
		if ($character !== null) {
			$list .= " (Level <highlight>{$character->level}<end>/<green>{$character->ai_level}<end> <highlight>{$character->profession}<end>)";
		}
		$online = Buddylist::is_online($main);
		if ($online === null) {
			$list .= " - No status.\n";
		} else if ($online == 1) {
			$list .= " - <green>Online<end>\n";
		} else {
			$list .= " - <red>Offline<end>\n";
		}
		$list .= ":::::: Alt Character(s)\n";
		forEach ($alts as $alt) {
			$list .= "<tab><tab>{$alt}";
			$character = Player::get_by_name($alt);
			if ($character !== null) {
				$list .= " (Level <highlight>{$character->level}<end>/<green>{$character->ai_level}<end> <highlight>{$character->profession}<end>)";
			}
			$online = Buddylist::is_online($alt);
			if ($online === null) {
				$list .= " - No status.\n";
			} else if ($online == 1) {
				$list .= " - <green>Online<end>\n";
			} else {
				$list .= " - <red>Offline<end>\n";
			}
		}
		
		return $list;
	}
}

?>