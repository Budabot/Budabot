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
   
$pageSize = 20;

if (preg_match("/^orghistory$/i", $message, $arr) || preg_match("/^orghistory (\\d+)$/i", $message, $arr)) {
	
	$page = 1;
	if ($arr[1] != '') {
		$page = $arr[1];
	}
	
	$startingRecord = ($page - 1) * $pageSize;

	$window = Text::make_header("Org History", "none");
	
	$sql = "SELECT actor, actee, action, organization, time FROM org_history ORDER BY time DESC LIMIT $startingRecord, $pageSize";
	$db->query($sql);
	while($row = $db->fObject()) {

		$window .= "$row->actor $row->action $row->actee in $row->organization at " . gmdate("M j, Y, G:i", $row->time)." (GMT)\n";
	}

	$msg = Text::make_link('Org History', $window, 'blob');

	bot::send($msg, $sendto);
} else if (preg_match("/^orghistory (.+)$/i", $message, $arr)) {

	$character = $arr[1];

	$window = Text::make_header("Org History", "none");
	
	$window .= "\n  Actions on $character\n";
	$sql = "SELECT actor, actee, action, organization, time FROM org_history WHERE actee LIKE '$character' ORDER BY time DESC";
	$db->query($sql);
	while($row = $db->fObject()) {

		$window .= "$row->actor $row->action $row->actee in $row->organization at " . gmdate("M j, Y, G:i", $row->time)." (GMT)\n";
	}

	$window .= "\n  Actions by $character\n";
	$sql = "SELECT actor, actee, action, organization, time FROM org_history WHERE actor LIKE '$character' ORDER BY time DESC";
	$db->query($sql);
	while($row = $db->fObject()) {

		$window .= "$row->actor $row->action $row->actee in $row->organization at " . gmdate("M j, Y, G:i", $row->time)." (GMT)\n";
	}

	$msg = Text::make_link('Org History', $window, 'blob');

	bot::send($msg, $sendto);
}

?>
