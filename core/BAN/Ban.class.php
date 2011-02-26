<?php

class Ban {
	public static function add($charid, $sender, $length, $reason) {
		$db = DB::get_instance();
		
		if ($length == null) {
			$ban_end = "NULL";
		} else {
			$ban_end = time() + $length;
		}
		$reason = str_replace("'", "''", $reason);

		$sql = "INSERT INTO banlist_<myname> (`charid`, `admin`, `time`, `reason`, `banend`) VALUES ('{$charid}', '{$sender}', '".time()."', '{$reason}', {$ban_end})";
		$numrows = $db->exec($sql);
		
		Ban::upload_banlist();
		
		return $numrows;
	}
	
	public static function remove($charid) {
		$db = DB::get_instance();

		$sql = "DELETE FROM banlist_<myname> WHERE charid = '{$charid}'";
		$numrows = $db->exec($sql);
		
		Ban::upload_banlist();
		
		return $numrows;
	}
	
	public static function upload_banlist() {
		$db = DB::get_instance();
		global $chatBot;
		
		$chatBot->banlist = array();
		
		$sql = "SELECT b.*, p.name FROM banlist b LEFT JOIN players p ON b.charid = p.charid";
		$data = $db->fObject('all');
		forEach ($data as $row) {
			$chatBot->banlist[$row->charid] = $row;
		}
	}
	
	public static function is_banned($charid) {
		global $chatBot;
	
		return isset($chatBot->banlist[$charid]);
	}
}

?>