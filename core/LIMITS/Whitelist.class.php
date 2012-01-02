<?php

class Whitelist {
	/** @Inject */
	public $db;

	public function add($user, $sender) {
		$user = ucfirst(strtolower($user));
		$sender = ucfirst(strtolower($sender));

		if ($user == '' || $sender == '') {
			return "User or sender is blank";
		}
	
		$data = $this->db->query("SELECT * FROM whitelist WHERE name = ?", $user);
		if (count($data) != 0) {
			return "Error! $user already added to the whitelist";
		} else {
			$this->db->exec("INSERT INTO whitelist (name, added_by, added_dt) VALUES (?, ?, ?)", $user, $sender, time());
			return "$user has been added to the whitelist";
		}
	}
	
	public function remove($user) {
		$user = ucfirst(strtolower($user));

		if ($user == '') {
			return "User is blank";
		}
	
		$data = $this->db->query("SELECT * FROM whitelist WHERE name = ?", $user);
		if (count($data) == 0) {
			return "Error! $user is not on the whitelist";
		} else {
			$this->db->exec("DELETE FROM whitelist WHERE name = ?", $user);
			return "$user has been removed from the whitelist";
		}
	}
	
	public function check($user) {
		$user = ucfirst(strtolower($user));

		$data = $this->db->query("SELECT * FROM whitelist WHERE name = ?", $user);
		if (count($data) == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public function all() {
		return $this->db->query("SELECT * FROM whitelist ORDER BY name ASC");
	}
}

?>