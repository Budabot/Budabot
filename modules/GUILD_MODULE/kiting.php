<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Set a player as afk kiting
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.01.2007
   ** Date(last modified): 25.01.2007
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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

if ($type == "guild") {
	$db->query("SELECT afk FROM guild_chatlist_<myname> WHERE `name` = '$sender'");
	$numrows = $db->numrows();
	$row = $db->fObject();
	if (preg_match("/^kiting$/i", $message, $arr) && $numrows != 0) {
	    if ($row->afk != 'kiting') {
	        $db->exec("UPDATE guild_chatlist_<myname> SET `afk` = 'kiting' WHERE `name` = '$sender'");
	        $msg = "<highlight>$sender<end> is now kiting";
	    } else if ($row->afk != '0') {
	        $db->exec("UPDATE guild_chatlist_<myname> SET `afk` = '0' WHERE `name` = '$sender'");
	        $msg = "<highlight>$sender<end> is back";
	    }
	    bot::send($msg, "guild");
	} else {
		$syntax_error = true;
	}
} else {
	$db->query("SELECT afk FROM priv_chatlist_<myname> WHERE `name` = '$sender'");
	$numrows = $db->numrows();
	$row = $db->fObject();
	if (preg_match("/^kiting$/i", $message, $arr) && $numrows != 0) {
	    if ($row->afk != 'kiting') {
	        $db->exec("UPDATE priv_chatlist_<myname> SET `afk` = 'kiting' WHERE `name` = '$sender'");
	        $msg = "<highlight>$sender<end> is now kiting";
	    } else if ($row->afk != '0') {
	        $db->exec("UPDATE priv_chatlist_<myname> SET `afk` = '0' WHERE `name` = '$sender'");
	        $msg = "<highlight>$sender<end> is back";
	    }
	    bot::send($msg);
	} else {
		$syntax_error = true;
	}
}
?>