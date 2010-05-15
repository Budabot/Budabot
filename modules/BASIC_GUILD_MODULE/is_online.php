<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Checks if a player is online
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 21.11.2006
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

$msg = "";
if(preg_match("/^is (.+)$/i", $message, $arr)) {
    // Get User id
    $uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if(!$uid)
        $msg = "Player <highlight>$name<end> does not exist.";
    else {
        //if the player is a buddy then
        if(bot::send("isbuddy", $name)) {
            $db->query("SELECT * FROM org_members_<myname> WHERE `name` = '$name' AND `mode` != 'del'");
            if($db->numrows() == 1) {
                $row = $db->fObject();
                if($row->logged_off != "0")
                    $logged_off = "\nLogged off at ".gmdate("l F d, Y - H:i", $row->logged_off)."(GMT)";
            }
            if($this->buddyList[$name] == "0")
                $status = "<red>offline<end>".$logged_off;
            else
                $status = "<green>online<end>";
            $msg = "Player <highlight>$name<end> is $status";
        // else add him
        } else {
            $this->vars["IgnoreLog"][$name] = $type;
						if($type == "msg")
	            	$this->vars["IgnoreLogSender"][$name] = $sender;
            bot::send("addbuddy", $uid);
            bot::send("rembuddy", $uid);
        }
    }
    if($msg) {
        // Send info back
        if($type == "msg")
            bot::send($msg, $sender);
        elseif($type == "priv")
        	bot::send($msg);
        elseif($type == "guild")
        	bot::send($msg, "guild");
    }
} elseif (($type == "logOn") || ($type == "logOff")) {
    //If $sender is marked as player to check online status
    if($this->vars["IgnoreLog"][$sender]) {
        if($this->buddyList[$sender] == "0")
            $status = "<red>offline<end>";
        else
            $status = "<green>online<end>";
        $msg = "Player <highlight>$sender<end> is $status";

        if($this->vars["IgnoreLog"][$sender] == "priv")
        	bot::send($msg);
        elseif($this->vars["IgnoreLog"][$sender] == "guild")
        	bot::send($msg, "guild");
        elseif($this->vars["IgnoreLog"][$sender] == "msg") {
        	bot::send($msg, $this->vars["IgnoreLogSender"][$sender]);
            unset($this->vars["IgnoreLogSender"][$sender]);
        }
        	
        unset($this->vars["IgnoreLog"][$sender]);
    }
}
?>
