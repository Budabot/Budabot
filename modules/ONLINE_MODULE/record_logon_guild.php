<?php

$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON o.name = p.name WHERE o.`name` = '$sender'");
$org_member = $db->fObject();
if ($org_member !== null && $org_member->mode != "del") {
  	$db->query("SELECT name FROM guild_chatlist_<myname> WHERE `name` = '$sender'");
	if ($db->numrows() == 0) {
	    $db->exec("INSERT INTO guild_chatlist_<myname> (`name`) VALUES ('$org_member->name')");
	}

	// update info for player
	Player::get_by_name($sender);
}

?>
