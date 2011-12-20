<?php

if ($chatBot->is_ready()) {
	$uid = $chatBot->get_uid($sender);
	$data = $db->query("SELECT * FROM tracked_users_<myname> WHERE uid = ?", $uid);
	if (count($data) > 0) {
		$db->exec("INSERT INTO tracking_<myname> (uid, dt, event) VALUES (?, ?, ?)", $uid, time(), 'logoff');
	}
}

?>