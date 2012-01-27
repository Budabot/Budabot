<?php

if (preg_match("/^(.+) invited (.+) to your organization.$/", $message, $arr)) {
    $name = ucfirst(strtolower($arr[2]));
	
    $row = $db->queryRow("SELECT * FROM org_members_<myname> WHERE `name` = ?", $name);
    if ($row != null) {
        $db->exec("UPDATE org_members_<myname> SET `mode` = 'add' WHERE `name` = ?", $name);
	    $buddyList->add($name, 'org');
		$chatBot->guildmembers[$name] = 6;
    	$msg = "<highlight>{$name}<end> has been added to the Notify list.";
    } else {
        $db->exec("INSERT INTO org_members_<myname> (`mode`, `name`) VALUES ('add', ?)", $name);
		$buddyList->add($name, 'org');
		$chatBot->guildmembers[$name] = 6;
    	$msg = "<highlight>{$name}<end> has been added to the Notify list.";
    }
    $db->exec("INSERT INTO online (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild>', 'guild', '<myname>', ?)", $name, time());
    $chatBot->sendGuild($msg);
	
	// update character info
    Player::get_by_name($name);
} else if (preg_match("/^(.+) kicked (.+) from your organization.$/", $message, $arr) || preg_match("/^(.+) removed inactive character (.+) from your organization.$/", $message, $arr)) {
    $name = ucfirst(strtolower($arr[2]));
    
	$db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = ?", $name);
    $db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $name);
	
	unset($chatBot->guildmembers[$name]);
	$buddyList->remove($name, 'org');
    
	$msg = "Removed <highlight>{$name}<end> from the Notify list.";
	$chatBot->sendGuild($msg);
} else if (preg_match("/^(.+) just left your organization.$/", $message, $arr) || preg_match("/^(.+) kicked from organization \\(alignment changed\\).$/", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
    
	$db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = ?", $name);
    $db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $name);

    unset($chatBot->guildmembers[$name]);
	$buddyList->remove($name, 'org');

	$msg = "Removed <highlight>{$name}<end> from the Notify list.";
    $chatBot->sendGuild($msg);
}

?>