<?php

class Playfields {
	public static function get_playfield_by_name($playfield_name) {
		$db = db::get_instance();
		
		$sql = "SELECT * FROM playfields WHERE `long_name` LIKE '{$playfield_name}' OR `short_name` LIKE '{$playfield_name}' LIMIT 1";
		
		$db->query($sql);
		return $db->fObject();
	}
	
	public static function get_playfield_by_id($playfield_id) {
		$db = db::get_instance();
		
		$sql = "SELECT * FROM playfields WHERE `id` = {$playfield_id}";

		$db->query($sql);
		return $db->fObject();
	}
}

?>