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
} else if (preg_match("/^migrate admins$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT name, adminlevel FROM admin_<myname>");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->query("SELECT name FROM admin_<myname> WHERE name = '$row->name'");
		if ($db->numrows() == 0) {
			$count++;
			$db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES ('$row->adminlevel', '$row->name')");
			$chatBot->admins[$row->name]["level"] = $row->adminlevel;
		}
	}
	
    $chatBot->send("$count admins migrated successfully. It is recommended that you restart your bot now.", $sendto);
} else if (preg_match("/^migrate settings$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT name, setting FROM settings_<myname> WHERE mode <> 'noedit'");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		if (Setting::save($row->name, $row->setting) !== false) {
			$count++;
		}
	}
	
    $chatBot->send("$count settings migrated successfully. It is recommended that you restart your bot now.", $sendto);
} else if (preg_match("/^migrate kos$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT time, name, sender, reason FROM koslist_<myname>");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO koslist (time, name, sender, reason) VALUES ('$row->time', '$row->name', '$row->sender', '$row->reason')");
		$count++;
	}
	
    $chatBot->send("$count kos entries migrated successfully. It is recommended that you restart your bot now. Do not try to import your kos list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate events$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT time_submitted, submitter_name, event_name, event_date, event_desc, event_attendees FROM events_<myname>_<dim>");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO events_<myname>_<dim> (time_submitted, submitter_name, event_name, event_date, event_desc, event_attendees) VALUES
			('$row->time_submitted', '$row->submitter_name', '$row->event_name', '$row->event_date', '$row->event_desc', '$row->event_attendees')");
		$count++;
	}
	
    $chatBot->send("$count events migrated successfully. It is recommended that you restart your bot now. Do not try to import your events list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate news$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT time, name, news FROM news_<myname>");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO news (time, name, news) VALUES ('$row->time', '$row->name', '$row->news')");
		$count++;
	}
	
    $chatBot->send("$count news entries migrated successfully. It is recommended that you restart your bot now. Do not try to import your news list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate quotes$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT Who, OfWho, When, What FROM quote");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO quote (Who, OfWho, When, What) VALUES ('$row->Who', '$row->OfWho', '$row->When', '$row->What')");
		$count++;
	}
	
    $chatBot->send("$count quotes migrated successfully. It is recommended that you restart your bot now. Do not try to import your quotes list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate orghistory$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT actor, actee, action, organization, time FROM org_history");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO org_history (actor, actee, action, organization, time) VALUES ('$row->actor', '$row->actee', '$row->action', '$row->organization', '$row->time')");
		$count++;
	}
	
    $chatBot->send("$count org history entries migrated successfully. It is recommended that you restart your bot now. Do not try to import your org history list again or you will have duplicates.", $sendto);
} else {
	$syntax_error = true;
}

?>