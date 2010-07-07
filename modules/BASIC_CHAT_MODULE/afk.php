<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: AFK Handling
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

$db->query("SELECT afk FROM priv_chatlist_<myname> WHERE `name` = '$sender'");
$row = $db->fObject();
if(preg_match("/^afk$/i", $message, $arr)) {
    if($row->afk == '0') {
        $db->query("UPDATE priv_chatlist_<myname> SET `afk` = 1 WHERE `name` = '$sender'");
        $msg = "<highlight>$sender<end> is now AFK";
    } else if($row->afk != '0') {
        $db->query("UPDATE priv_chatlist_<myname> SET `afk` = 0 WHERE `name` = '$sender'");
        $msg = "<highlight>$sender<end> is back";
    }
} else if(preg_match("/^afk (.*)$/i", $message, $arr)) {
    if($row->afk == '0') {
        $db->query("UPDATE priv_chatlist_<myname> SET `afk` = '$arr[1]' WHERE `name` = '$sender'");
        $msg = "<highlight>$sender<end> is now AFK";
    } else if($row->afk != '0') {
        $db->query("UPDATE priv_chatlist_<myname> SET `afk` = 0 WHERE `name` = '$sender'");
        $msg = "<highlight>$sender<end> is back";
    }
}
bot::send($msg);
?>
