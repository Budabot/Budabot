<?php

if (preg_match("/^(.+) invited (.+) to your organization.$/", $message, $arr)) {
    $name = ucfirst(strtolower($arr[2]));
	
    $data = $db->query("SELECT * FROM org_members_<myname> WHERE `name` = '{$name}'");
    $row = $data[0];
    if ($row != null) {
        $db->exec("UPDATE org_members_<myname> SET `mode` = 'add' WHERE `name` = '{$name}'");
	    Buddylist::add($name, 'org');
		$chatBot->guildmembers[$name] = 6;
    	$msg = "<highlight>{$name}<end> has been added to the Notify list.";
    } else {
        $db->exec("INSERT INTO org_members_<myname> (`mode`, `name`) VALUES ('add', '{$name}')");
		Buddylist::add($name, 'org');
		$chatBot->guildmembers[$name] = 6;
    	$msg = "<highlight>{$name}<end> has been added to the Notify list.";
    }
    $db->exec("INSERT INTO online (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('{$name}', '<myguild>', 'guild', '<myname>', " . time() . ")");
    $chatBot->send($msg, "guild");
	
	// update character info
    Player::get_by_name($name);
} else if (preg_match("/^(.+) kicked (.+) from your organization.$/", $message, $arr) || preg_match("/^(.+) removed inactive character (.+) from your organization.$/", $message, $arr)) {
    $name = ucfirst(strtolower($arr[2]));
    
	$db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = '{$name}'");
    $db->exec("DELETE FROM online WHERE `name` = '{$name}' AND `channel_type` = 'guild' AND added_by = '<myname>'");
	
	unset($chatBot->guildmembers[$name]);
	Buddylist::remove($name, 'org');
    
	$msg = "Removed <highlight>{$name}<end> from the Notify list.";
	$chatBot->send($msg, "guild");
} else if (preg_match("/^(.+) just left your organization.$/", $message, $arr) || preg_match("/^(.+) kicked from organization \\(alignment changed\\).$/", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
    
	$db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = '{$name}'");
    $db->exec("DELETE FROM online WHERE `name` = '{$name}' AND `channel_type` = 'guild' AND added_by = '<myname>'");

    unset($chatBot->guildmembers[$name]);
	Buddylist::remove($name, 'org');

	$msg = "Removed <highlight>{$name}<end> from the Notify list.";
    $chatBot->send($msg, "guild");
}

?>