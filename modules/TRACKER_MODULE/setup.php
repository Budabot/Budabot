<?php

$sql = "SELECT name FROM tracked_users_<myname>";
$db->query($sql);
$data = $db->fObject('all');
forEach ($data as $row) {
	Buddylist::add($row->name, 'tracking');
}

?>