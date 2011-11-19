<?php

$data = $db->query("SELECT * FROM admin_<myname>");
forEach ($data as $row) {
	Buddylist::add($row->name, 'admin');
}

?>