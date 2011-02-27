<?php

//Create the var that contains all members of the org
unset($chatBot->guildmembers);
$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON o.charid = p.charid");
$data = $db->fObject('all');
forEach ($data as $row) {
	$chatBot->guildmembers[$row->charid] = $row;
}
?>