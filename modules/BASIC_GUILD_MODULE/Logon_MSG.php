<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Set logon messages from Guildmembers
   ** Version: 1.0
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

$db->query("SELECT name, logon_msg FROM org_members_<myname> WHERE `name` = '$sender'");
$row = $db->fObject();
if(eregi("^logon clear$", $message)) {
    if($row->name == $sender) {
        $db->query("UPDATE org_members_<myname> SET `logon_msg` = 0 WHERE `name` = '$sender'");
        $msg = "Logon message cleared.";
    } else
        $msg = "You are not on the Notify list of this bot.";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
} else if(eregi("^logon (.+)$", $message, $arr)) {
    if($row->name == $sender) {
        $arr[1] = str_replace("'", "\'", $arr[1]);
        $arr[1] = str_replace('"', "\'", $arr[1]);        
        if(strlen($arr[1]) <= 200) {
            $db->query("UPDATE org_members_<myname> SET `logon_msg` = \"$arr[1]\" WHERE `name` = '$sender'");
            $msg = "Thankyou ".$sender.". Your logon message has been set.";
        } else
            $msg = "Your Logon Message is too long. Pls choose a shorter one.";
    } else
        $msg = "You are not on the Notify list of this bot.";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
}
?>
