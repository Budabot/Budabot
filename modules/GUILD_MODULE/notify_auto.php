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
    $uid = AoChat::get_uid($arr[2]);
    $name = ucfirst(strtolower($arr[2]));
    $name2 = ucfirst(strtolower($arr[1]));
    $db->query("SELECT * FROM org_members_<myname> WHERE `name` = '$name'");
    $row = $db->fObject();
    if($row->name != "" && $row->mode == "del") {
        $db->exec("UPDATE org_members_<myname> SET `mode` = 'man' WHERE `name` = '".$name."'");
	    $this->guildmembers[$name] = 6;
    	$msg = "<highlight>".$name."<end> has been added to the Notify list.";
    // Is the player name valid?
    } else {
        // update character info
        Player::get_by_name($arr[2]);

        // Add him as a buddy and put his infos into the DB
        $db->exec("INSERT INTO org_members_<myname> (`mode`, `name`) VALUES ('man', '".$name."')");
		$this->add_buddy($name, 'org');
    	$msg = "<highlight>".$name."<end> has been added to the Notify list.";
    	$this->guildmembers[$name] = 6;
    }
    $db->exec("INSERT INTO guild_chatlist_<myname> (`name`) VALUES ('".$name."')");     
    bot::send($msg, "guild");
} else if (preg_match("/^(.+) kicked (.+) from your organization.$/", $message, $arr) || preg_match("/^(.+) removed inactive character (.+) from your organization.$/", $message, $arr)) {
    $uid = AoChat::get_uid($arr[2]);
    $name = ucfirst(strtolower($arr[2]));
    $db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = '$name'");
    $db->exec("DELETE FROM guild_chatlist_<myname> WHERE `name` = '$name'");
    $msg = "Removed <highlight>".$name."<end> from the Notify list.";
    unset($this->guildmembers[$name]);
	$this->remove_buddy($name, 'org');
    bot::send($msg, "guild");
} else if(preg_match("/^(.+) just left your organization.$/", $message, $arr) || preg_match("/^(.+) kicked from organization (alignment changed).$/", $message, $arr)) {
    $uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    $db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = '$name'");
    $db->exec("DELETE FROM guild_chatlist_<myname> WHERE `name` = '$name'");
    $msg = "Removed <highlight>".$name."<end> from the Notify list.";
    unset($this->guildmembers[$name]);
	$this->remove_buddy($name, 'org');
    bot::send($msg, "guild");
}

?>