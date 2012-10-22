<?php
/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
 */
class Preferences {
	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'preferences');
	}
	
	public function save($sender, $name, $value) {
		$sender = ucfirst(strtolower($sender));
		$name = strtolower($name);

		if ($this->get($sender, $name) === false) {
			$this->db->exec("INSERT INTO preferences_<myname> (sender, name, value) VALUES (?, ?, ?)", $sender, $name, $value);
		} else {
			$this->db->exec("UPDATE preferences_<myname> SET value = ? WHERE sender = ? AND name = ?", $value, $sender, $name);
		}
	}

	public function get($sender, $name) {
		$sender = ucfirst(strtolower($sender));
		$name = strtolower($name);

		$row = $this->db->queryRow("SELECT * FROM preferences_<myname> WHERE sender = ? AND name = ?", $sender, $name);
		if ($row === null) {
			return false;
		} else {
			return $row->value;
		}
	}
}

?>
