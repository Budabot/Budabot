<?php

class AltInfo {
	public $main; // The main for this character
	public $alts = array(); // The list of alts for this character
	
	public function is_validated($sender) {
		if ($sender == $this->main) {
			return true;
		}

		forEach ($this->alts as $alt => $validated) {
			if ($sender == $alt) {
				return ($validated == 1);
			}
		}

		// $sender is not an alt at all, return false
		return false;
	}

	public function get_alts_blob($showValidateLinks = false, $firstPageOnly = false) {
		$chatBot = Registry::getInstance('chatBot');
		$db = Registry::getInstance('db');
		$setting = Registry::getInstance('setting');
		$player = Registry::getInstance('player');
		$buddylistManager = Registry::getInstance('buddylistManager');

		if (count($this->alts) == 0) {
			return "No registered alts.";
		}

		$blob .= "<tab><tab>{$this->main}";
		$character = $player->get_by_name($this->main);
		if ($character !== null) {
			$blob .= " (<highlight>{$character->level}<end>/<green>{$character->ai_level}<end> <highlight>{$character->profession}<end>)";
		}
		$online = $buddylistManager->is_online($this->main);
		if ($online === null) {
			$blob .= " - No status\n";
		} else if ($online == 1) {
			$blob .= " - <green>Online<end>\n " . Text::make_chatcmd("Send tell", "/tell $this->main");
		} else {
			$blob .= " - <red>Offline<end>\n";
		}

		$sql = "SELECT `alt`, `main`, `validated`, p.* FROM `alts` a LEFT JOIN players p ON (a.alt = p.name AND p.dimension = '<dim>') WHERE `main` LIKE ? ORDER BY level DESC, ai_level DESC, profession ASC, name ASC";
		$data = $db->query($sql, $this->main);
		$count = count($data);

		$blob .= "\n:::::: Alt Characters ({$count})\n";
		forEach ($data as $row) {
			$blob .= "<tab><tab>{$row->alt}";
			if ($row->profession !== null) {
				$blob .= " (<highlight>{$row->level}<end>/<green>{$row->ai_level}<end> <highlight>{$row->profession}<end>)";
			}
			$online = $buddylistManager->is_online($row->alt);
			if ($online === null) {
				$blob .= " - No status.";
			} else if ($online == 1) {
				$blob .= " - <green>Online<end> " . Text::make_chatcmd("Send tell", "/tell $row->alt");
			} else {
				$blob .= " - <red>Offline<end>";
			}
			
			if ($showValidateLinks && $setting->get('alts_inherit_admin') == 1 && $row->validated == 0) {
				$blob .= " [Unvalidated] " . Text::make_chatcmd('Validate', "/tell <myname> <symbol>altvalidate {$row->alt}");
			}
			
			$blob .= "\n";
		}
		
		$msg = Text::make_blob("Alts of {$this->main}", $blob);
		
		if ($firstPageOnly && is_array($msg)) {
			return $msg[0];
		} else {
			return $msg;
		}
	}

	public function get_online_alts() {
		$online_list = array();
		$buddylistManager = Registry::getInstance('buddylistManager');

		if ($buddylistManager->is_online($this->main)) {
			$online_list []= $this->main;
		}
		
		forEach ($this->alts as $name => $validated) {
			if ($buddylistManager->is_online($name)) {
				$online_list []= $name;
			}
		}
		
		return $online_list;
	}
	
	public function get_all_alts() {
		$online_list = array();

		$online_list []= $this->main;
		
		forEach ($this->alts as $name => $validated) {
			$online_list []= $name;
		}
		
		return $online_list;
	}
	
	public function hasUnvalidatedAlts() {
		forEach ($this->get_all_alts() as $alt) {
			if (!$this->is_validated($alt)) {
				return true;
			}
		}
		return false;
	}
}

class Alts {
	public static function get_alt_info($player) {
		$chatBot = Registry::getInstance('chatBot');
		$db = Registry::getInstance('db');
		
		$player = ucfirst(strtolower($player));
		
		$ai = new AltInfo();
		
		$sql = "SELECT `alt`, `main`, `validated` FROM `alts` WHERE (`main` LIKE ?) OR (`main` LIKE (SELECT `main` FROM `alts` WHERE `alt` LIKE ?))";
		$data = $db->query($sql, $player, $player);
		
		$isValidated = 0;
		
		if (count($data) > 0) {
			forEach ($data as $row) {
				$ai->main = $row->main;
				$ai->alts[$row->alt] = $row->validated;
			}
		} else {
			$ai->main = $player;
		}
		
		return $ai;
	}
	
	public static function add_alt($main, $alt, $validated) {
		$chatBot = Registry::getInstance('chatBot');
		$db = Registry::getInstance('db');
		
		$main = ucfirst(strtolower($main));
		$alt = ucfirst(strtolower($alt));
		
		$sql = "INSERT INTO `alts` (`alt`, `main`, `validated`) VALUES (?, ?, ?)";
		return $db->exec($sql, $alt, $main, $validated);
	}
	
	public static function rem_alt($main, $alt) {
		$chatBot = Registry::getInstance('chatBot');
		$db = Registry::getInstance('db');
		
		$sql = "DELETE FROM `alts` WHERE `alt` LIKE ? AND `main` LIKE ?";
		return $db->exec($sql, $alt, $main);
	}
}

?>