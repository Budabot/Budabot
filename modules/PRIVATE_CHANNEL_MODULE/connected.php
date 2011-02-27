<?php

$sql = "SELECT name FROM members_<myname> m LEFT JOIN players p ON m.charid = p.charid WHERE autoinv = 1";
$db->query($sql);
$data = $db->fObject('all');
forEach ($data as $row) {
	Buddylist::add($row->name, 'member');
}

?>