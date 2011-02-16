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

if (-1 == $sender) {
    if (preg_match("/^(.+) turned the cloaking device in your city (on|off).$/i", $message, $arr)) {
        $db->exec("INSERT INTO org_city_<myname> (`time`, `action`, `player`) VALUES ('".time()."', '".$arr[2]."', '".$arr[1]."')");
    } else if (preg_match("/^Your city in (.+) has been targeted by hostile forces.$/i", $message, $arr)) {
        $db->exec("INSERT INTO org_city_<myname> (`time`, `action`) VALUES ('".time()."', 'Attack')");
    }
}

?>
