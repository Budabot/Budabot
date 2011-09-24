<?php

//Create the var that contains all members of the org
unset($chatBot->guildmembers);
$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE mode <> 'del'");
if ($db->numrows() != 0) {
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$chatBot->guildmembers[$row->name] = $row->guild_rank_id;
	}
}
//Add logoff table to org_members_<myname> if it dosent exist.
$db->query("SELECT `logoff_msg` FROM `org_members_<myname>`");

if ($db->errorInfo[0] != "00000") {
	//No column
	$db->exec("ALTER TABLE `org_members_<myname>` ADD COLUMN `logoff_msg` VARCHAR(400) DEFAULT ''"); //Add the logoff_msg column
}
?>