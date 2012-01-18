<?php

function checkIfColumnExists($db, $table, $column) {
	$data = $db->query("SELECT * FROM $table");
	return property_exists($data[0], $column);
}

require_once 'core/PREFERENCES/Preferences.class.php';
$data = $db->query("SELECT * FROM org_members_<myname>");
if (property_exists($data[0], 'logon_msg') || property_exists($data[0], 'logoff_msg')) {
	forEach ($data as $row) {
		if (isset($row->logon_msg) && $row->logon_msg != '') {
			Preferences::save($row->name, 'logon_msg', $row->logon_msg);
		}
		if (isset($row->logoff_msg) && $row->logoff_msg != '') {
			Preferences::save($row->name, 'logoff_msg', $row->logoff_msg);
		}
	}
	$db->exec("UPDATE org_members_<myname> SET logon_msg = '', logoff_msg = ''");
}

if (!checkIfColumnExists($db, "news", "sticky")) {
	$db->exec("ALTER TABLE news ADD `sticky` TINYINT NOT NULL DEFAULT 0");
}


$db->exec("UPDATE eventcfg_<myname> SET type = LOWER(type)")

$db->exec("UPDATE cmdcfg_<myname> SET admin = 'rl' WHERE admin = 'leader'");
$db->exec("UPDATE hlpcfg_<myname> SET admin = 'rl' WHERE admin = 'leader'");
$db->exec("UPDATE settings_<myname> SET admin = 'rl' WHERE admin = 'leader'");

?>