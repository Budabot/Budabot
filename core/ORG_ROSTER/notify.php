<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Adding/Removing Guildmembers
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
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

if(eregi("^notify (on|add) (.+)$", $message, $arr)){
    // Get User id
    $uid = AoChat::get_uid($arr[2]);
	$name = ucfirst(strtolower($arr[2]));
    $db->query("SELECT * FROM org_members_<myname> WHERE `name` = '$name'");
	$numrows = $db->numrows();
	if($numrows != 0)
	    $row = $db->fObject();
    // Is the player already a member?
    if($numrows != 0 && $row->mode != "del")
        $msg = "<highlight>$name<end> is already on the Notify list.";
    // If the member was deleted set him as manual added again
    elseif($numrows != 0 && $row->mode == "del") {
        $db->query("UPDATE org_members_<myname> SET `mode` = 'man' WHERE `name` = '$name'");
	    if(!isset($this->buddyList[$name]))
	        bot::send("addbuddy", $name);
	    
	    $this->vars["IgnoreLog"][$name] = 2;
    	$msg = "<highlight>$name<end> has been added to the Notify list.";
    // Is the player name valid?
    } elseif($uid) {
        // Getting Player infos
        $whois = new whois($arr[2]);
        // Set global Ignore Logon/Logoff for this player(will not show the first logon/off msg)
        $this->vars["IgnoreLog"][$name] = 2;
        // Add him as a buddy and put his infos into the DB
        bot::send("addbuddy", $uid);
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
        $db->query("INSERT INTO org_members_<myname> (`mode`, `name`, `firstname`, `lastname`, `guild`, `rank_id`, `rank`, `level`, `profession`, `gender`, `breed`, `ai_level`, `ai_rank`)
                    VALUES ('man',
                    '".$name."', '".$whois -> firstname."',
                    '".$whois -> lastname."', '".$whois -> org."',
                    '".$whois -> rank_id."', '".$whois -> rank."',
                    '".$whois -> level."', '".$whois -> prof."',
                    '".$whois -> gender."', '".$whois -> breed."',
                    '".$whois -> ai_level."',
                    '".$whois -> ai_rank."')");
    	$msg = "<highlight>".$name."<end> has been added to the Notify list.";
    // Player name is not valid
    } else
        $msg = "Player <highlight>".$name."<end> does not exist.";

    // Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");
} else if(eregi("^notify (off|rem) (.+)$", $message, $arr)){
    $name = ucfirst(strtolower($arr[2]));
    $query = $db->query("SELECT * FROM org_members_<myname> WHERE `name` = '$name'");
	$numrows = $db->numrows();
	if($numrows != 0)
	    $row = $db->fObject();
	    
    // Is the player a member of this bot?
    if($numrows != 0 && $row->mode != "del") {
        $db->query("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = '$name'");
        $db->query("DELETE FROM guild_chatlist_<myname> WHERE `name` = '$name'");
        $msg = "Removed <highlight>$name<end> from the Notify list.";
    // Player is not a member of this bot
    } else
        $msg = "<highlight>$name<end> is not a member of this bot.";

    // Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");
} else
	$syntax_error = true;
?>
