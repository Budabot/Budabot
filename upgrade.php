<?php

function checkIfColumnExists($table, $column) {
	$db = DB::get_instance();

	$data = $db->query("SELECT * FROM org_members_<myname>");
	return property_exists($data[0], 'logon_msg');
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

?>