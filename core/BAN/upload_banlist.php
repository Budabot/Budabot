<?php
   /*
   ** Author: Sebuda (RK2)
   ** Description: Uploads banned players to the local var
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 21.01.2006
   ** Date(last modified): 10.12.2006
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

$db->query("CREATE TABLE IF NOT EXISTS banlist_<myname> (name VARCHAR(25) NOT NULL PRIMARY KEY, admin VARCHAR(25), time VARCHAR(10), why TEXT, banend INT)");

$db->query("SELECT * FROM banlist_<myname>");
while ($row = $db->fObject()) {
	$this->banlist[$row->name]["name"] = $row->name;
	$this->banlist[$row->name]["admin"] = $row->admin;
	$this->banlist[$row->name]["when"] = $row->time;
	if ($row->banend != 0 || $row->banend != NULL) {
		$this->banlist[$row->name]["banend"] = $row->banend;
	}

	if ($row->why != "" || $row->why != NULL) {
		$this->banlist[$row->name]["reason"] = $row->why;
	}
}

?>