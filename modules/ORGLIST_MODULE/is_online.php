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
if (preg_match("/^is (.+)$/i", $message, $arr)) {
    // Get User id
    $uid = $this->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if (!$uid) {
        $msg = "Player <highlight>$name<end> does not exist.";
		$this->send($msg, $sendto);
    } else {
        //if the player is a buddy then
        if ($this->is_buddy($name, NULL)) {
            $db->query("SELECT * FROM org_members_<myname> WHERE `name` = '$name' AND `mode` != 'del'");
            if ($db->numrows() == 1) {
                $row = $db->fObject();
                if($row->logged_off != "0") {
                    $logged_off = "\nLogged off at ".gmdate("l F d, Y - H:i", $row->logged_off)."(GMT)";
				}
            }
            if ($this->buddy_online($name)) {
                $status = "<green>online<end>";
            } else {
                $status = "<red>offline<end>".$logged_off;
			}
            $msg = "Player <highlight>$name<end> is $status";
			$this->send($msg, $sendto);
        // else add him
        } else {
			$this->data["ONLINE_MODULE"]['playername'] = $name;
			$this->data["ONLINE_MODULE"]['sendto'] = $sendto;
			$this->add_buddy($name, 'is_online');
        }
    }
} elseif (($type == "logOn" || $type == "logOff") && $sender == $this->data["ONLINE_MODULE"]['playername']) {
    if ($type == "logOn") {
		$status = "<green>online<end>";
	} else if ($type == "logOff") {
		$status = "<red>offline<end>";
	}
	$msg = "Player <highlight>$sender<end> is $status";
	$this->send($msg, $this->data["ONLINE_MODULE"]['sendto']);
	$this->remove_buddy($sender, 'is_online');
	unset($this->data["ONLINE_MODULE"]);
}
?>
