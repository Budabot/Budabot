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

	public function add($charId, $sender, $length, $reason) {

		if ($length == null) {
			$ban_end = "0";
		} else {
			$ban_end = time() + $length;
		}

		$sql = "INSERT INTO banlist_<myname> (`charid`, `admin`, `time`, `reason`, `banend`) VALUES (?, ?, ?, ?, ?)";
		$numrows = $this->db->exec($sql, $charId, $sender, time(), $reason, $ban_end);

		$this->uploadBanlist();

		return $numrows;
	}

	public function remove($charId) {
		$sql = "DELETE FROM banlist_<myname> WHERE charid = ?";
		$numrows = $this->db->exec($sql, $charId);

		$this->uploadBanlist();

		return $numrows;
	}

	public function uploadBanlist() {
		$this->banlist = array();

		$sql = "
			SELECT b.*, IFNULL(p.name, b.charid) AS name
			FROM banlist_<myname> b LEFT JOIN players p ON b.charid = p.charid";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$this->banlist[$row->charid] = $row;
		}
	}

	public function isBanned($charId) {
		return isset($this->banlist[$charId]);
	}

	public function getBanlist() {
		return $this->banlist;
	}
}
