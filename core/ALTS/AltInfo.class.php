<?php

namespace Budabot\Core\Modules;

use Budabot\Core\Registry;

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
		$db = Registry::getInstance('db');
		$settingManager = Registry::getInstance('settingManager');
		$playerManager = Registry::getInstance('playerManager');
		$buddylistManager = Registry::getInstance('buddylistManager');
		$text = Registry::getInstance('text');

		if (count($this->alts) == 0) {
			return "No registered alts.";
		}
		
		$online = $buddylistManager->isOnline($this->main);
		$blob .= $this->formatCharName($this->main, $online);

		$character = $playerManager->get_by_name($this->main);
		if ($character !== null) {
			$blob .= " ({$character->level}/<green>{$character->ai_level}<end> {$character->profession})";
		}
		$blob .= $this->formatOnlineStatus($online);
		$blob .= "\n";

		$sql = "SELECT `alt`, `main`, `validated`, p.* FROM `alts` a LEFT JOIN players p ON (a.alt = p.name AND p.dimension = '<dim>') WHERE `main` LIKE ? ORDER BY level DESC, ai_level DESC, profession ASC, name ASC";
		$data = $db->query($sql, $this->main);
		$count = count($data) + 1;
		forEach ($data as $row) {
			$online = $buddylistManager->isOnline($row->alt);
			$blob .= $this->formatCharName($row->alt, $online);
			if ($row->profession !== null) {
				$blob .= " ({$row->level}/<green>{$row->ai_level}<end> {$row->profession})";
			}
			$blob .= $this->formatOnlineStatus($online);

			if ($showValidateLinks && $settingManager->get('alts_inherit_admin') == 1 && $row->validated == 0) {
				$blob .= " [Unvalidated] " . $text->make_chatcmd('Validate', "/tell <myname> <symbol>altvalidate {$row->alt}");
			}

			$blob .= "\n";
		}

		$msg = $text->makeBlob("Alts of {$this->main} ($count)", $blob);

		if ($firstPageOnly && is_array($msg)) {
			return $msg[0];
		} else {
			return $msg;
		}
	}

	public function get_online_alts() {
		$online_list = array();
		$buddylistManager = Registry::getInstance('buddylistManager');

		if ($buddylistManager->isOnline($this->main)) {
			$online_list []= $this->main;
		}

		forEach ($this->alts as $name => $validated) {
			if ($buddylistManager->isOnline($name)) {
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
	
	public function getValidatedMain($sender) {
		if ($this->is_validated($sender)) {
			return $this->main;
		} else {
			return $sender;
		}
	}
	
	public function formatCharName($name, $online) {
		if ($online == 1) {
			$text = Registry::getInstance('text');
			return $text->make_chatcmd($name, "/tell $name");
		} else {
			return $name;
		}
	}
	
	public function formatOnlineStatus($online) {
		if ($online === null) {
			return " - No status";
		} else if ($online == 1) {
			return " - <green>Online<end>";
		} else {
			return " - <red>Offline<end>";
		}
	}
}
