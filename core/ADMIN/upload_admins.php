<?php
   /*
   ** Author: Sebuda (RK2)
   ** Description: Uploads admins to local var
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 21.01.2006
   ** Date(last modified): 21.11.2006
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

$db->query("CREATE TABLE IF NOT EXISTS admin_<myname> (`name` VARCHAR(25) NOT NULL PRIMARY KEY, `adminlevel` INT)");

$this->settings["Super Admin"] = ucfirst(strtolower($this->settings["Super Admin"]));

$db->query("SELECT * FROM admin_<myname> WHERE `name` = '{$this->settings["Super Admin"]}'");
if ($db->numrows() == 0) {
	$db->query("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (4, '{$this->settings["Super Admin"]}')");
} else {
	$db->query("UPDATE admin_<myname> SET `adminlevel` = 4 WHERE `name` = '{$this->settings["Super Admin"]}'");
}

$db->query("SELECT * FROM admin_<myname>");
while ($row = $db->fObject()) {
	$this->admins[$row->name]["level"] = $row->adminlevel;
}

?>