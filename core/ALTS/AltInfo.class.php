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
		$db = Registry::getInstance('db');
		$settingManager = Registry::getInstance('settingManager');
		$playerManager = Registry::getInstance('playerManager');
		$buddylistManager = Registry::getInstance('buddylistManager');
		$text = Registry::getInstance('text');

		if (count($this->alts) == 0) {
			return "No registered alts.";
		}

		$blob .= "{$this->main}";
		$character = $playerManager->get_by_name($this->main);
		if ($character !== null) {
			$blob .= " ({$character->level}/<green>{$character->ai_level}<end> {$character->profession})";
		}
		$online = $buddylistManager->is_online($this->main);
		if ($online === null) {
			$blob .= " - No status\n";
		} else if ($online == 1) {
			$blob .= " - <green>Online<end> " . $text->make_chatcmd("Send tell", "/tell $this->main") . "\n";
		} else {
			$blob .= " - <red>Offline<end>\n";
		}

		$sql = "SELECT `alt`, `main`, `validated`, p.* FROM `alts` a LEFT JOIN players p ON (a.alt = p.name AND p.dimension = '<dim>') WHERE `main` LIKE ? ORDER BY level DESC, ai_level DESC, profession ASC, name ASC";
		$data = $db->query($sql, $this->main);
		$count = count($data) + 1;
		forEach ($data as $row) {
			$blob .= "{$row->alt}";
			if ($row->profession !== null) {
				$blob .= " ({$row->level}/<green>{$row->ai_level}<end> {$row->profession})";
			}
			$online = $buddylistManager->is_online($row->alt);
			if ($online === null) {
				$blob .= " - No status.";
			} else if ($online == 1) {
				$blob .= " - <green>Online<end> " . $text->make_chatcmd("Send tell", "/tell $row->alt");
			} else {
				$blob .= " - <red>Offline<end>";
			}

			if ($showValidateLinks && $settingManager->get('alts_inherit_admin') == 1 && $row->validated == 0) {
				$blob .= " [Unvalidated] " . $text->make_chatcmd('Validate', "/tell <myname> <symbol>altvalidate {$row->alt}");
			}

			$blob .= "\n";
		}

		$msg = $text->make_blob("Alts of {$this->main} ($count)", $blob);

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
