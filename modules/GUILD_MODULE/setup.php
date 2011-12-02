<?php

//Create the var that contains all members of the org
unset($chatBot->guildmembers);
$data = $db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE mode <> 'del'");
if (count($data) != 0) {
	forEach ($data as $row) {
		$chatBot->guildmembers[$row->name] = $row->guild_rank_id;
	}
}

?>