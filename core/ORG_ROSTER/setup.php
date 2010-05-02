<?php
// Creating Tables
// Org Roster table
$db->query("CREATE TABLE IF NOT EXISTS org_members_<myname> (`name` VARCHAR(25) NOT NULL PRIMARY KEY, `mode` VARCHAR(7), `firstname` VARCHAR(25), `lastname` VARCHAR(25), `guild` VARCHAR(30), `rank_id` TINYINT, `rank` VARCHAR(20), `level` INT, `profession` VARCHAR(15), `gender` VARCHAR(10), `breed` VARCHAR(10), `ai_level` INT, `ai_rank` VARCHAR(15), `logged_off` INT Default '0', `logon_msg` VARCHAR(255) Default '0')");

$db->query("CREATE TABLE IF NOT EXISTS guests_<myname> (`name` VARCHAR(25) NOT NULL PRIMARY KEY)");

//Create the var that contains all members of the org
unset($this->guildmembers);
$db->query("SELECT * FROM org_members_<myname>");
if($db->numrows() != 0) {
	while($row = $db->fObject())
		$this->guildmembers[$row->name] = $row->rank_id;
}
?>