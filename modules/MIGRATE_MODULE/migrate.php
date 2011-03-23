<?php

if (!class_exists('DB2')) {
	class DB2 extends DB {
		function __construct($type, $dbName, $host, $user, $pass, $botname) {
			parent::__construct($type, $dbName, $host, $user, $pass);
			$this->botname = strtolower($botname);
		}
		
		function formatSql($sql) {
			$sql = str_replace("<myname>", $this->botname, $sql);
			$sql = parent::formatSql($sql);

			return $sql;
		}
	}
}

if (preg_match("/^migrate alts$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT main, alt FROM alts");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->query("SELECT alt FROM alts WHERE main = '$row->main' AND alt = '$row->alt'");
		if ($db->numrows() == 0) {
			$count++;
			Alts::add_alt($row->main, $row->alt);
		}
	}
	
    $chatBot->send("$count alts migrated successfully. It is recommended that you restart your bot now.", $sendto);
} else if (preg_match("/^migrate members$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT name, autoinv FROM members_<myname>");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->query("SELECT name FROM members_<myname> WHERE name = '$row->name'");
		if ($db->numrows() == 0) {
			$count++;
			$db->exec("INSERT INTO members_<myname> (`name`, `autoinv`) VALUES ('$row->name', 1)");
		}
	}
	
    $chatBot->send("$count members migrated successfully. It is recommended that you restart your bot now.", $sendto);
} else {
	$syntax_error = true;
}

?>