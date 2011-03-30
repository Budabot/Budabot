<?php

//Create the var that contains all members of the org
unset($chatBot->guildmembers);
$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON o.name = p.name WHERE mode <> 'del'");
if ($db->numrows() != 0) {
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$chatBot->guildmembers[$row->name] = $row->guild_rank_id;
	}
}

?>