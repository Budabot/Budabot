<?php

namespace Budabot\Core;

/**
 * @Instance
 */
class BanManager {

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $playerManager;

	private $banlist = array();

	public function add($char, $sender, $length, $reason) {

		if ($length == null) {
			$ban_end = "0";
		} else {
			$ban_end = time() + $length;
		}

		$sql = "INSERT INTO banlist_<myname> (`name`, `admin`, `time`, `reason`, `banend`) VALUES (?, ?, ?, ?, ?)";
		$numrows = $this->db->exec($sql, $char, $sender, time(), $reason, $ban_end);

		$this->uploadBanlist();

		return $numrows;
	}

	public function remove($char) {
		$sql = "DELETE FROM banlist_<myname> WHERE name = ?";
		$numrows = $this->db->exec($sql, $char);

		$this->uploadBanlist();

		return $numrows;
	}

	public function uploadBanlist() {
		$this->banlist = array();

		$data = $this->db->query("SELECT * FROM banlist_<myname>");
		forEach ($data as $row) {
			$this->banlist[$row->name] = $row;
		}
	}

	public function isBanned($name) {
		if (isset($this->banlist[$name])) {
			return true;
		} else {
			$whois = $this->playerManager->get_by_name($name);
			return isset($this->banlist[$whois->guild]);
		}
	}

	public function getBanlist() {
		return $this->banlist;
	}
}

?>
