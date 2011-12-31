<?php

class Character {
	private $name = null;
	private $charId = null;
	private $altInfo = null;
	private $whois = null;

	public function construct__($name) {
		$this->name = ucfirst(strtolower($name));
	}

	public function getName() {
		return $this->name;
	}

	public function getAltInfo() {
		if ($this->altInfo === null) {
			$this->altInfo = Alts::get_alt_info($this->name);
		}
		return $this->altInfo;
	}

	public function getWhois() {
		if ($this->whois === null) {
			$this->whois = Player::get_by_name($this->name);
		}
		return $this->whois;
	}

	public function checkAccess($accessLevel) {
		$accessLevel = $chatBot->getInstance('accessLevel');
		return $accessLevel->checkAccess($this->name, $accessLevel);
	}

	public function getCharId() {
		global $chatBot;
		if ($this->charId === null) {
			$this->charId = $chatBot->get_uid($name);
		}
		return $this->charId;
	}
}

?>
