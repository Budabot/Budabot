<?php
   
Setting::add('SYSTEM', 'default_module_status', 'Default Status for new Modules', 'edit', "options", $chatBot->settings["default_module_status"], 'ON;OFF', '1;0', 'mod');

//Upload Settings from the db that are set by modules
$db->query("SELECT * FROM settings_<myname>");
$data = $db->fObject('all');
forEach ($data as $row) {
	$chatBot->settings[$row->name] = $row->value;
}

?>