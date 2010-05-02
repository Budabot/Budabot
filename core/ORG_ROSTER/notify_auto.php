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
        $db->query("UPDATE org_members_<myname> SET `mode` = 'man' WHERE `name` = '".$name."'");
	    $this->guildmembers[$name] = 6;
    	$msg = "<highlight>".$name."<end> has been added to the Notify list.";
    // Is the player name valid?
    } else {
        // Getting Player infos
        $whois = new whois($arr[2]);
        if($whois->errorCode != 0) {
		  	$whois -> firstname = "";
		  	$whois -> lastname = "";
		  	$whois -> rank_id = 6;
		  	$whois -> rank = "Applicant";
		  	$whois -> level = "1";
		  	$whois -> prof = "Unknown";
		  	$whois -> gender = "Unknown";
		  	$whois -> breed = "Unknown";
		}
        // Set global Ignore Logon/Logoff for this player(will not show the first logon/off msg)
        $this->vars["IgnoreLog"][$name] = 2;
        // Add him as a buddy and put his infos into the DB
        $db->query("INSERT INTO org_members_<myname> (`mode`, `name`, `firstname`, `lastname`, `guild`, `rank_id`, `rank`, `level`, `profession`, `gender`, `breed`, `ai_level`, `ai_rank`)
                    VALUES ('man',
                    '".$name."', '".$whois -> firstname."',
                    '".$whois -> lastname."', '".$this->vars["my guild"]."',
                    '".$whois -> rank_id."', '".$whois -> rank."',
                    '".$whois -> level."', '".$whois -> prof."',
                    '".$whois -> gender."', '".$whois -> breed."',
                    '".$whois -> ai_level."',
                    '".$whois -> ai_rank."')");                            
		if(!isset($this->buddList[$name]))
	        bot::send("addbuddy", $uid);
    	$msg = "<highlight>".$name."<end> has been added to the Notify list.";
    	$this->guildmembers[$name] = 6;
    }
    $db->query("INSERT INTO guild_chatlist_<myname> (`name`, `profession`, `guild`, `breed`, `level`, `ai_level`)
                VALUES ('".$name."', '".$whois->prof."', '".$this->vars["my guild"]."',
                   '".$whois->breed."', '".$whois->level."', '".$whois->ai_level."')");     
    bot::send($msg, "guild");
	$msg = "Welcome to <highlight>".$this->vars["my guild"]."<end>.";
    bot::send($msg, $name);
	$msg = "I am the Org bot for <highlight>".$this->vars["my guild"]."<end>. You may wanna learn what i can do for you, so just do /tell <myname> help.";
    bot::send($msg, $name);
	$msg = "And you will see all my commands that are only there to make your life easier.";
    bot::send($msg, $name); 
} else if (preg_match("/^(.+) kicked (.+) from your organization.$/", $message, $arr) || preg_match("/^(.+) removed inactive character (.+) from your organization.$/", $message, $arr)) {
    $uid = AoChat::get_uid($arr[2]);
    $name = ucfirst(strtolower($arr[2]));
    $db -> query("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = '$name'");
    $db -> query("DELETE FROM guild_chatlist_<myname> WHERE `name` = '$name'");
    $msg = "Removed <highlight>".$name."<end> from the Notify list.";
    unset($this->guildmembers[$name]);
    bot::send($msg, "guild");
} else if(preg_match("/^(.+) just left your organization.$/", $message, $arr) || preg_match("/^(.+) kicked from organization (alignment changed).$/", $message, $arr)) {
    $uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    $db -> query("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = '$name'");
    $db -> query("DELETE FROM guild_chatlist_<myname> WHERE `name` = '$name'");
    $msg = "Removed <highlight>".$name."<end> from the Notify list.";
    unset($this->guildmembers[$name]);
    bot::send($msg, "guild");
} elseif(($type == "logOn" || $type == "logOff")  && ($this->vars["IgnoreLog"][$sender] == 2 || $this->vars["IgnoreLog"][$sender] == 1)) {
    //$this->vars["IgnoreLog"][$sender] if it is 2 then log modules didn�t executed yet
    if($this->vars["IgnoreLog"][$sender] == 2)
        $this->vars["IgnoreLog"][$sender] = 1;
    //log module is executed
    elseif($this->vars["IgnoreLog"][$sender] == 1)
        unset($this->vars["IgnoreLog"][$sender]);
}
?>