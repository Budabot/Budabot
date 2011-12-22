<?php

class Botconnect {
	public static function onConnectList($name) {
		global $chatBot;
		$db = DB::get_instance();
		
		$uid = $chatBot->get_uid($name);
		$data = $db->query("SELECT * FROM botconnect WHERE charid = ?", $uid);
		if (count($data) == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public static function add($name) {
		global $chatBot;
		$db = DB::get_instance();
		
		$uid = $chatBot->get_uid($name);
		$db->exec("INSERT INTO botconnect (charid, dt) VALUES (?, ?)", $uid, time());
	}
	
	public static function remove($name) {
		global $chatBot;
		$db = DB::get_instance();
		
		$uid = $chatBot->get_uid($name);
		$db->exec("DELETE FROM botconnect WHERE charid = ?", $uid);
	}
	
	public static function getAll() {
		$db = DB::get_instance();
		
		return $db->query("SELECT *, IFNULL((SELECT name FROM name_history WHERE charid = b.charid ORDER BY dt DESC LIMIT 1), charid) AS name FROM botconnect b ORDER BY name ASC");
	}
}

?>
