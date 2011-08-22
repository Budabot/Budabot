<?php

class AltInfo {
	public $main; // The main for this character
	public $alts = array(); // The list of alts for this character
	public $accessCharacter; // The character name that should be used for determining access
	public $currentValidated; // Whether the current character is validated
}

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
		
		$sql = "SELECT `alt`, `main` FROM `alts` WHERE (`main` LIKE '$main') OR (`main` LIKE (SELECT `main` FROM `alts` WHERE `alt` LIKE '$main'))";
		$db->query($sql);
		
		$data = $db->fObject('all');
		$array = array();
		forEach ($data as $row) {
			$array[] = $row->alt;
		}
		return $array;
	}
	
	public static function get_alt_info($player) {
		$db = DB::get_instance();
		
		$ai = new AltInfo();
		
		$sql = "SELECT `alt`, `main`, `validated` FROM `alts` WHERE (`main` LIKE '$player') OR (`main` LIKE (SELECT `main` FROM `alts` WHERE `alt` LIKE '$player'))";
		$db->query($sql);
		
		$isValidated = 0;
		
		$data = $db->fObject('all');
		foreach ($data as $row) {
			$ai->main = $row->main;
			$ai->alts []= $row->alt;
			if ($player == $row->alt)
			{
				$isValidated = $row->validated;
			}
		}
		
		$ai->currentValidated = $isValidated || $ai->main == $player;
		$ai->accessCharacter = $player;
		if ($ai->currentValidated) {
			$ai->accessCharacter = $ai->main;
		}
		
		return $ai;
	}
	
	public static function add_alt($main, $alt, $validated = 1) {
		$db = DB::get_instance();
		
		$main = ucfirst(strtolower($main));
		$alt = ucfirst(strtolower($alt));
		
		$sql = "INSERT INTO `alts` (`alt`, `main`, `validated`) VALUES ('$alt', '$main', '$validated')";
		return $db->exec($sql);
	}
	
	public static function rem_alt($main, $alt) {
		$db = DB::get_instance();
		
		$sql = "DELETE FROM `alts` WHERE `alt` LIKE '$alt' AND `main` LIKE '$main'";
		return $db->exec($sql);
	}
	
	public static function get_alts_blob($char) {
		$db = DB::get_instance();
	
		$altInfo = Alts::get_alt_info(ucfirst(strtolower($char)));
		
		if (count($altInfo->alts) == 0 || (count($altInfo->alts) == 1 && $altInfo->alts[0] == $altInfo->main)) {
			return "No registered alts";
		}

		$list = "<header> :::::: Character List for {$altInfo->main} :::::: <end>\n\n";
		$list .= "<tab><tab>{$altInfo->main}";
		$character = Player::get_by_name($altInfo->main);
		if ($character !== null) {
			$list .= " (<highlight>{$character->level}<end>/<green>{$character->ai_level}<end> <highlight>{$character->profession}<end>)";
		}
		$online = Buddylist::is_online($altInfo->main);
		if ($online === null) {
			$list .= " - No status.\n";
		} else if ($online == 1) {
			$list .= " - <green>Online<end>\n";
		} else {
			$list .= " - <red>Offline<end>\n";
		}
		$list .= "\n:::::: Alt Character(s)\n";
		
		$sql = "SELECT `alt`, `main`, `validated`, p.* FROM `alts` a LEFT JOIN players p ON a.alt = p.name WHERE `main` LIKE '{$altInfo->main}' ORDER BY level DESC, ai_level DESC, profession ASC, name ASC";
		$db->query($sql);
		$data = $db->fObject('all');
		forEach ($data as $row) {
			$list .= "<tab><tab>{$row->alt}";
			if ($row->profession !== null) {
				$list .= " (<highlight>{$row->level}<end>/<green>{$row->ai_level}<end> <highlight>{$row->profession}<end>)";
			}
			$online = Buddylist::is_online($row->alt);
			if ($online === null) {
				$list .= " - No status.";
			} else if ($online == 1) {
				$list .= " - <green>Online<end>";
			} else {
				$list .= " - <red>Offline<end>";
			}
			
			if ($row->validated == 0) {
				$list .= " [Unvalidated] " . Text::make_link('Validate', "/tell <myname> <symbol>altvalidate {$row->alt}", 'chatcmd');
			}
			
			$list .= "\n";
		}
		
		$msg = Text::make_blob("Alts of {$altInfo->main}", $list);
		
		return $msg;
	}
}

?>