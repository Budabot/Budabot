<?php

$sql = "SELECT name FROM tracked_users_<myname>";
$data = $db->query($sql);
forEach ($data as $row) {
	$buddylistManager->add($row->name, 'tracking');
}

?>