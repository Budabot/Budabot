<?php

class Playfields {
	public static function get_playfield_by_name($playfield_name) {
		global $chatBot;
		$db = Registry::getInstance('db');
		
		$sql = "SELECT * FROM playfields WHERE `long_name` LIKE ? OR `short_name` LIKE ? LIMIT 1";
		
		return $db->queryRow($sql, $playfield_name, $playfield_name);
	}
	
	public static function get_playfield_by_id($playfield_id) {
		global $chatBot;
		$db = Registry::getInstance('db');
		
		$sql = "SELECT * FROM playfields WHERE `id` = ?";

		return $db->queryRow($sql, $playfield_id);
	}
}

?>