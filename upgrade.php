<?php

function checkIfColumnExists($db, $table, $column) {
	$data = $db->query("SELECT * FROM $table");
	return property_exists($data[0], $column);
}

function upgrade($db, $sql, $params = null) {
	try {
		$db->exec($sql);
	} catch (SQLException $e) {
		LegacyLogger::log("ERROR", 'Upgrade', $e->getMessage());
	}
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
	upgrade($db, "UPDATE org_members_<myname> SET logon_msg = '', logoff_msg = ''");
}

if (!checkIfColumnExists($db, "news", "sticky")) {
	upgrade($db, "ALTER TABLE news ADD `sticky` TINYINT NOT NULL DEFAULT 0");
}

upgrade($db, "UPDATE eventcfg_<myname> SET type = LOWER(type)");

upgrade($db, "UPDATE cmdcfg_<myname> SET admin = 'rl' WHERE admin = 'leader'");
upgrade($db, "UPDATE hlpcfg_<myname> SET admin = 'rl' WHERE admin = 'leader'");
upgrade($db, "UPDATE settings_<myname> SET admin = 'rl' WHERE admin = 'leader'");

upgrade($db, "DELETE FROM cmd_alias_<myname> WHERE alias = 'kickuser'");
upgrade($db, "DELETE FROM cmd_alias_<myname> WHERE alias = 'inviteuser'");

?>