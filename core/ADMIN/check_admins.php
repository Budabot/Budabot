<?php

$db->query("SELECT * FROM admin_<myname>");
$data = $db->fObject('all');
forEach ($data as $row) {
	Buddylist::add($row->name, 'admin');
}

?>