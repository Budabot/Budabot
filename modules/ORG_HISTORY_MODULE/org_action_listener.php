<?php
  /*
   ** Module: ORG_HISTORY
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Shows the org history (invites and kicks) for a player
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 14-Feb-2010
   ** Date(last modified): 14-Feb-2010
   **
   ** Copyright (C) 2009 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** Licence Infos:
   ** This file is an addon to Budabot.
   **
   ** This module is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** This module is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with this module; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   **
   ** This module may be obtained at: http://www.box.net/shared/bgl3cx1c3z
   **
   */

if (preg_match("/^(.+) just left your organization.$/", $message, $arr)) {
	$actor = $arr[1];
	$actee = "";
	$action = "left";
	$time = time();
	
	$sql = "INSERT INTO org_history ( actor, actee, action, organization, time ) VALUES ( '$actor', '$actee', '$action', '<myguild>', $time ) ";
	$db->exec($sql);
} else if (preg_match("/^(.+) kicked (.+) from your organization.$/", $message, $arr)) {
	$actor = $arr[1];
	$actee = $arr[2];
	$action = "kicked";
	$time = time();
	
	$sql = "INSERT INTO org_history ( actor, actee, action, organization, time ) VALUES ( '$actor', '$actee', '$action', '<myguild>', $time ) ";
	$db->exec($sql);
} else if (preg_match("/^(.+) invited (.+) to your organization.$/", $message, $arr)) {
	$actor = $arr[1];
	$actee = $arr[2];
	$action = "invited";
	$time = time();
	
	$sql = "INSERT INTO org_history ( actor, actee, action, organization, time ) VALUES ( '$actor', '$actee', '$action', '<myguild>', $time ) ";
	$db->exec($sql);
} else if (preg_match("/^(.+) removed inactive character (.+) from your organization.$/", $message, $arr)) {
	$actor = $arr[1];
	$actee = $arr[2];
	$action = "removed";
	$time = time();
	
	$sql = "INSERT INTO org_history ( actor, actee, action, organization, time ) VALUES ( '$actor', '$actee', '$action', '<myguild>', $time ) ";
	$db->exec($sql);
}

?>
