<?php
// Creating Tables
// Org Roster table
$db->query("CREATE TABLE IF NOT EXISTS org_members_<myname> (`name` VARCHAR(25) NOT NULL PRIMARY KEY, `mode` VARCHAR(7), `logged_off` INT Default '0', `logon_msg` VARCHAR(255) Default '0')");

//Create the var that contains all members of the org
unset($this->guildmembers);
$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON o.name = p.name");
if ($db->numrows() != 0) {
	while ($row = $db->fObject()) {
		$this->guildmembers[$row->name] = $row->rank_id;
	}
}
?>