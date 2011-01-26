<?php

if (isset($this->guildmembers[$sender])) {
  	$db->query("SELECT name FROM guild_chatlist_<myname> WHERE `name` = '$sender'");
	if ($db->numrows() == 0) {
	    $db->exec("INSERT INTO guild_chatlist_<myname> (`name`) VALUES ('$org_member->name')");
	}

	// update info for player
	Player::get_by_name($sender);
}

?>
