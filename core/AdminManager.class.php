<?php

/**
 * @Instance
 */
class AdminManager {

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $db;

	/** @Inject */
	public $buddylistManager;

	public $admins = array();

	public function uploadAdmins() {
		$this->db->exec("CREATE TABLE IF NOT EXISTS admin_<myname> (`name` VARCHAR(25) NOT NULL PRIMARY KEY, `adminlevel` INT)");

		$this->chatBot->vars["SuperAdmin"] = ucfirst(strtolower($this->chatBot->vars["SuperAdmin"]));

		$data = $this->db->query("SELECT * FROM admin_<myname> WHERE `name` = ?", $this->chatBot->vars["SuperAdmin"]);
		if (count($data) == 0) {
			$this->db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (?, ?)", '4', $this->chatBot->vars["SuperAdmin"]);
		} else {
			$this->db->exec("UPDATE admin_<myname> SET `adminlevel` = ? WHERE `name` = ?", '4', $this->chatBot->vars["SuperAdmin"]);
		}

		$data = $this->db->query("SELECT * FROM admin_<myname>");
		forEach ($data as $row) {
			$this->admins[$row->name]["level"] = $row->adminlevel;
		}
	}

	public function removeFromLists($who) {
		unset($this->admins[$who]);
		$this->db->exec("DELETE FROM admin_<myname> WHERE `name` = ?", $who);
		$this->buddylistManager->remove($who, 'admin');
	}

	public function addToLists($who, $intlevel) {
		$action = '';
		if (isset($this->admins[$who])) {
			$this->db->exec("UPDATE admin_<myname> SET `adminlevel` = ? WHERE `name` = ?", $intlevel, $who);
			if ($this->admins[$who]["level"] > $intlevel) {
				$action = "demoted";
			} else {
				$action = "promoted";
			}
		} else {
			$this->db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (?, ?)", $intlevel, $who);
			$action = "promoted";
		}

		$this->admins[$who]["level"] = $intlevel;
		$this->buddylistManager->add($who, 'admin');

		return $action;
	}
	
	public function checkExisting($who, $level) {
		if ($this->admins[$who]["level"] != $level) {
			return false;
		} else {
			return true;
		}
	}
}

?>
