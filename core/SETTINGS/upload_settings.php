<?php
   
//Upload Settings from the db that are set by modules
$db->query("SELECT * FROM settings_<myname>");
$data = $db->fObject('all');
forEach ($data as $row) {
	$chatBot->settings[$row->name] = $row->value;
}

?>