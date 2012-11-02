<?php

/**
 * @Instance
 */
class Ban {

	/** @Inject */
	public $db;

	private $banlist = array();

	public function add($char, $sender, $length, $reason) {

		if ($length == null) {
			$ban_end = "0";
		} else {
			$ban_end = time() + $length;
		}

		$sql = "INSERT INTO banlist_<myname> (`name`, `admin`, `time`, `reason`, `banend`) VALUES (?, ?, ?, ?, ?)";
		$numrows = $this->db->exec($sql, $char, $sender, time(), $reason, $ban_end);

		$this->upload_banlist();

		return $numrows;
	}

	public function remove($char) {
		$sql = "DELETE FROM banlist_<myname> WHERE name = ?";
		$numrows = $this->db->exec($sql, $char);

		$this->upload_banlist();

		return $numrows;
	}

	public function upload_banlist() {
		$this->banlist = array();

		$data = $this->db->query("SELECT * FROM banlist_<myname>");
		forEach ($data as $row) {
			$this->banlist[$row->name] = $row;
		}
	}

	public function is_banned($char) {
		return isset($this->banlist[$char]);
	}

	public function getBanlist() {
		return $this->banlist;
	}
}

?>
