<?php

class Playfields {

	/** @Inject */
	public $db;

	public function get_playfield_by_name($playfield_name) {
		$sql = "SELECT * FROM playfields WHERE `long_name` LIKE ? OR `short_name` LIKE ? LIMIT 1";
		
		return $this->db->queryRow($sql, $playfield_name, $playfield_name);
	}
	
	public function get_playfield_by_id($playfield_id) {
		$sql = "SELECT * FROM playfields WHERE `id` = ?";

		return $this->db->queryRow($sql, $playfield_id);
	}
}

?>