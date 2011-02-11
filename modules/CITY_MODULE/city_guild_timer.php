<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Reminder for city cloak
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.12.2005
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

$db->query("SELECT * FROM org_city_<myname> ORDER BY `time` DESC LIMIT 0, 2");
if ($db->numrows() != 0) {
	$row = $db->fObject();
    if ($row->action == "off") {
        if (((time() - $row->time) >= 60*60) && ((time() - $row->time) < 61*60)) {
            $chatBot->send("Shields have been disabled one hour ago. It is now possible to enable it again.", "guild");
		}
    } else if ($row->action == "on") {
        if (((time() - $row->time) >= 60*60) && ((time() - $row->time) < 61*60)) {
            $chatBot->send("Shields have been enabled one hour ago. Alien attacks can be again initiated now.", "guild");
		}
    }
}
?>
