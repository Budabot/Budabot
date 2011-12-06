<?php

// disable error logging
$error_file = $vars['error_file'];
$error_console = $vars['error_console'];

//$vars['error_file'] = 0;
//$vars['error_console'] = 0;

$data = $db->query("SELECT name, logon_msg, logoff_msg FROM org_members_<myname>");
forEach ($data as $row) {
	if ($row->logon_msg != '') {
		Preferences::save($name, 'logon_msg', $row->logon_msg);
	}
	if ($row->logoff_msg != '') {
		Preferences::save($name, 'logoff_msg', $row->logoff_msg);
	}
}
$db->exec("UPDATE org_members_<myname> SET logon_msg = '', logoff_msg = ''");

// re-enable error logging
$vars['error_file'] = $error_file;
$vars['error_console'] = $error_console;

?>