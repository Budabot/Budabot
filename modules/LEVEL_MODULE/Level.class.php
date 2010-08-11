<?php

class Level {
	public static function get_level_info($level) {
		global $db;
		
		$sql = "SELECT * FROM levels WHERE level = $level");
		$db->query($sql);
		return $db->fObject();
	}
	
	public static function get_all_levels() {
		global $db;
		
		$sql = "SELECT * FROM levels ORDER BY level");
		$db->query($sql);
		return $db->fObject('all');
	}
}

?>