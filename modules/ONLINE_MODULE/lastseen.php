<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the last time a player logged off
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 03.02.2007
   ** Date(last modified): 03.02.2007
   ** 
   ** Copyright (C) 2007 Carsten Lohmann
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

if (preg_match("/^lastseen (.+)$/i", $message, $arr)) {
	// Get User id
    $uid = $this->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if (!$uid) {
        $msg = "Player <highlight>$name<end> does not exist.";
    } else {
	    $db->query("SELECT * FROM org_members_<myname> WHERE `name` = '$name' AND `mode` != 'del'");
        if ($db->numrows() == 1) {
    	    $row = $db->fObject();
    	    if ($this->buddy_online($name)) {
    	    	$msg = "This player is currently <green>online<end>.";
            } else if ($row->logged_off != "0") {
        	    $msg = "Logged off at ".gmdate("l F d, Y - H:i", $row->logged_off)."(GMT)";
        	} else {
        		$msg = "No Record for this player.";
			}
        } else {
        	$msg = "This player is not a member of this Org.";
		}
	}

	$this->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>