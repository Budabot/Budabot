<?php

//Upload Settings from the db that are set by modules
$data = $db->query("SELECT * FROM settings_<myname>");
forEach ($data as $row) {
	$setting->settings[$row->name] = $row->value;
}

?>