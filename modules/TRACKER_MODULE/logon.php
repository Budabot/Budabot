<?php

if ($chatBot->is_ready()) {
	$db->query("SELECT * FROM tracked_users_<myname> WHERE charid = $charid");
	if ($db->numrows() != 0) {
		$db->exec("INSERT INTO tracking_<myname> (charid, dt, event) VALUES ($charid, " . time() . ", 'logon')");
	}
}

?>