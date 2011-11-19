<?php

class Playfields {
	public static function get_playfield_by_name($playfield_name) {
		$db = DB::get_instance();
		
		$sql = "SELECT * FROM playfields WHERE `long_name` LIKE '{$playfield_name}' OR `short_name` LIKE '{$playfield_name}' LIMIT 1";
		
		$data = $db->query($sql);
		return $data[0];
	}
	
	public static function get_playfield_by_id($playfield_id) {
		$db = DB::get_instance();
		
		$sql = "SELECT * FROM playfields WHERE `id` = {$playfield_id}";

		$data = $db->query($sql);
		return $data[0];
	}
}

?>