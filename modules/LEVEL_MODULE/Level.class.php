<?php

class Level {
	public static function get_level_info($level) {
		$db = DB::get_instance();
		
		$sql = "SELECT * FROM levels WHERE level = ?";
		return $db->queryRow($sql, $level);
	}
	
	public static function find_all_levels() {
		$db = DB::get_instance();
		
		$sql = "SELECT * FROM levels ORDER BY level";
		return $db->query($sql);
	}
}

?>