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

$chatBot->vars["SuperAdmin"] = ucfirst(strtolower($chatBot->vars["SuperAdmin"]));
$charid = $chatBot->get_uid($chatBot->vars["SuperAdmin"]);

$db->query("SELECT * FROM admin_<myname> WHERE `charid` = '{$charid}'");
if ($db->numrows() == 0) {
	$db->exec("INSERT INTO admin_<myname> (`access_level`, `charid`) VALUES (4, '{$charid}')");
} else {
	$db->exec("UPDATE admin_<myname> SET `access_level` = 4 WHERE `charid` = '{$charid}'");
}

$db->query("SELECT a.*, p.name FROM admin_<myname> a LEFT JOIN players p ON a.charid = p.charid");
$data = $db->fObject('all');
forEach ($data as $row) {
	$chatBot->admins[$row->charid] = $row;
	if ($row->access_level == 4 && Buddylist::is_online($row->name)) {
		$chatBot->send("<myname> is <green>online<end>. For updates or help use the Budabot Forums <highlight>http://budabot.com<end>", $row->name);
	}
}

?>