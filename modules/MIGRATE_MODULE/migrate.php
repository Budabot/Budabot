<?php

if (preg_match("/^migrate alts$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT main, alt FROM alts");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->query("SELECT alt FROM alts WHERE main = '$row->main' AND alt = '$row->alt'");
		if ($db->numrows() == 0) {
			$count++;
			Alts::add_alt($row->main, $row->alt, 1);
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
	
    $chatBot->send("$count kos entries migrated successfully. It is recommended that you restart your bot now. Do not import your kos list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate events$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT time_submitted, submitter_name, event_name, event_date, event_desc, event_attendees FROM events_<myname>_<dim>");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO events (time_submitted, submitter_name, event_name, event_date, event_desc, event_attendees) VALUES
			('$row->time_submitted', '$row->submitter_name', '$row->event_name', '$row->event_date', '$row->event_desc', '$row->event_attendees')");
		$count++;
	}
	
    $chatBot->send("$count events migrated successfully. It is recommended that you restart your bot now. Do not import your events list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate news$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT time, name, news FROM news_<myname>");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO news (time, name, news) VALUES ('$row->time', '$row->name', '$row->news')");
		$count++;
	}
	
    $chatBot->send("$count news entries migrated successfully. It is recommended that you restart your bot now. Do not import your news list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate quotes$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT `Who`, `OfWho`, `When`, `What` FROM quote");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO quote (`Who`, `OfWho`, `When`, `What`) VALUES ('$row->Who', '$row->OfWho', '$row->When', '$row->What')");
		$count++;
	}
	
    $chatBot->send("$count quotes migrated successfully. It is recommended that you restart your bot now. Do not import your quotes list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate orghistory$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT actor, actee, action, organization, time FROM org_history");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO org_history (actor, actee, action, organization, time) VALUES ('$row->actor', '$row->actee', '$row->action', '$row->organization', '$row->time')");
		$count++;
	}
	
    $chatBot->send("$count org history entries migrated successfully. It is recommended that you restart your bot now. Do not import your org history list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate notes$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT name, note FROM notes_<myname>");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		$db->exec("INSERT INTO notes (name, note) VALUES ('$row->name', '$row->note')");
		$count++;
	}
	
    $chatBot->send("$count notes migrated successfully. It is recommended that you restart your bot now. Do not import your notes list again or you will have duplicates.", $sendto);
} else if (preg_match("/^migrate orgmembers$/i", $message, $arr)) {
	$db2 = new DB2(Setting::get('migrate_type'), Setting::get('migrate_name'), Setting::get('migrate_hostname'), Setting::get('migrate_username'), Setting::get('migrate_password'), Setting::get('migrate_botname'));

	$db2->query("SELECT name, mode, logged_off, logon_msg FROM org_members_<myname>");
	$data = $db2->fObject('all');
	$count = 0;
	forEach ($data as $row) {
		if ($chatBot->get_uid($row->name)) {
			// 1.0 modes: man, org, del
			// 2.0 modes: add, org, del
			if ($row->mode == 'man') {
				$row->mode = 'add';
			}
		
			$db->query("SELECT name FROM org_members_<myname> WHERE name = '$row->name'");
			$row2 = $db2->fObject();
			if ($db->numrows() == 0) {
				$db->exec("INSERT INTO org_members_<myname> (name, mode, logon_msg, logged_off) VALUES ('$row->name', '$row->mode', '$row->logon_msg', '$row->logged_off')");
			} else {
				// use whichever logon time is most recent from the old and new databases
				$row->logged_off = max($row->logged_off, $row2->logged_off);
				
				// use the current logon message if it's set, otherwise use the logon message from the old database
				if ($row2->logon_msg != '') {
					$row->logon_msg = $row2->logon_msg;
				}
				$db->exec("UPDATE org_members_<myname> set name = '$row->name', mode = '$row->mode', logon_msg = '$row->logon_msg', logged_off = '$row->logged_off'");
			}
			
			$count++;
		}
	}
	
    $chatBot->send("$count guild members migrated successfully. It is recommended that you restart your bot now.", $sendto);
} else {
	$syntax_error = true;
}

?>