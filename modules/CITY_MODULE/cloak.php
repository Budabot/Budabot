<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the status of city cloak
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.12.2005
   ** Date(last modified): 26.11.2006
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

if (preg_match("/^cloak$/i", $message)) {
    $db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 0, 20 ");
    if ($db->numrows() == 0) {
        $msg = "<highlight>Unknown status on city cloak!<end>";
    } else {
		$row = $db->fObject();
        if (((time() - $row->time) >= 60*60) && ($row->action == "off")) {
            $msg = "The cloaking device is disabled. It is possible to enable it.";
        } else if (((time() - $row->time) < 60*60) && ($row->action == "off")) {
            $msg = "The cloaking device is disabled. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to enable it.";
        } else if (((time() - $row->time) >= 60*60) && ($row->action == "on")) {
            $msg = "The cloaking device is enabled. It is possible to disable it.";
		} else if (((time() - $row->time) < 60*60) && ($row->action == "on")) {
            $msg = "The cloaking device is <green>enabled<end>. It is possible in ".round((($row->time + 60*60) - time())/60, 0)."min to disable it.";
		}

        $list = "<header>::::: City Cloak History :::::<end>\n\n";
        $list .= "Time: <highlight>".gmdate("M j, Y, G:i", $row->time)." (GMT)<end>\n";
        if ($row->action == "Attack") {
            $list .= "Action: <highlight>City was under attack.<end>\n\n";
        } else if ($row->action == "on" || $row->action == "off") {
            $list .= "Action: <highlight>Cloaking Device has been turned ".$row->action."<end>\n";
            $list .= "Player: <highlight>".$row->player."<end>\n\n";
        }
        
        while ($row = $db->fObject()) {
            $list .= "Time: <highlight>".gmdate("M j, Y, G:i", $row->time)." (GMT)<end>\n";
            if ($row->action == "Attack") {
                $list .= "Action: <highlight>City was under attack.<end>\n\n";
            } else if ($row->action == "on" || $row->action == "off") {
                $list .= "Action: <highlight>Cloaking Device has been turned ".$row->action."<end>\n";
                $list .= "Player: <highlight>".$row->player."<end>\n\n";
            }
        }
        $msg .= " ".Text::make_link("City History", $list);
    }
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^cloak (raise|on)$/i", $message)) {
    $db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 0, 20 ");
	$row = $db->fObject();

	if ($row->action == "on") {
		$msg = "The cloaking device is already <green>enabled<end>.";
	} else {
		$db->exec("INSERT INTO org_city_<myname> (`time`, `action`, `player`) VALUES ('".time()."', 'on', '{$sender}*')");
		$msg = "The cloaking device has been manually set to on in the bot.";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>