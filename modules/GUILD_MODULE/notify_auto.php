<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Automatically Adding/Removing Guildmembers
   ** Version: 0.3
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.12.2005
   ** Date(last modified): 10.12.2006
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
   
if (preg_match("/^(.+) invited (.+) to your organization.$/", $message, $arr)) {
    $name = ucfirst(strtolower($arr[2]));
	$joined_charid = $chatBot->get_uid($name);
	
    $db->query("SELECT * FROM org_members_<myname> WHERE `charid` = '{$joined_charid}'");
    $row = $db->fObject();
    if ($row != null && $row->mode == "del") {
        $db->exec("UPDATE org_members_<myname> SET `mode` = 'man' WHERE `charid` = '{$joined_charid}'");
    	$msg = "<highlight>{$name}<end> has been added to the Notify list.";
		
		$chatBot->guildmembers[$joined_charid] = new stdClass;
		$chatBot->guildmembers[$joined_charid]->guild_rank_id = 6;
		$chatBot->guildmembers[$joined_charid]->name = $name;
    } else {
        $db->exec("INSERT INTO org_members_<myname> (`mode`, `charid`) VALUES ('man', '{$joined_charid}')");
		Buddylist::add($name, 'org');
    	$msg = "<highlight>{$name}<end> has been added to the Notify list.";

		$chatBot->guildmembers[$joined_charid] = new stdClass;
		$chatBot->guildmembers[$joined_charid]->guild_rank_id = 6;
		$chatBot->guildmembers[$joined_charid]->name = $name;
    }
    $db->exec("INSERT INTO online (`charid`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$joined_charid', '{$chatBot->vars['my guild']}', 'guild', '<myname>', " . time() . ")");
    $chatBot->send($msg, "guild");
	
	// update character info
    Player::get_by_name($name);
} else if (preg_match("/^(.+) kicked (.+) from your organization.$/", $message, $arr) || preg_match("/^(.+) removed inactive character (.+) from your organization.$/", $message, $arr)) {
    $name = ucfirst(strtolower($arr[2]));
	$left_charid = $chatBot->get_uid($name);

    $db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `charid` = '{$left_charid}'");
    $db->exec("DELETE FROM online WHERE `charid` = '$left_charid' AND `channel_type` = 'guild' AND added_by = '<myname>'");
    
	unset($chatBot->guildmembers[$left_charid]);
	Buddylist::remove($name, 'org');

	$msg = "Removed <highlight>{$name}<end> from the Notify list.";
    $chatBot->send($msg, "guild");
} else if(preg_match("/^(.+) just left your organization.$/", $message, $arr) || preg_match("/^(.+) kicked from organization \\(alignment changed\\).$/", $message, $arr)) {
    $name = ucfirst(strtolower($arr[2]));
	$left_charid = $chatBot->get_uid($name);

    $db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `charid` = '{$left_charid}'");
    $db->exec("DELETE FROM online WHERE `charid` = '$left_charid' AND `channel_type` = 'guild' AND added_by = '<myname>'");
    
	unset($chatBot->guildmembers[$left_charid]);
	Buddylist::remove($name, 'org');

	$msg = "Removed <highlight>{$name}<end> from the Notify list.";
    $chatBot->send($msg, "guild");
}

?>