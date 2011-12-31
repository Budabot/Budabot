<?php

class Level {
	public static function get_level_info($level) {
		global $chatBot;
		$db = Registry::getInstance('db');
		
		$sql = "SELECT * FROM levels WHERE level = ?";
		return $db->queryRow($sql, $level);
	}
	
	public static function find_all_levels() {
		global $chatBot;
		$db = Registry::getInstance('db');
		
		$sql = "SELECT * FROM levels ORDER BY level";
		return $db->query($sql);
	}
}

?>