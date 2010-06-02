<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows which lvl is needed for a specific mission ql
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 30.01.2006
   ** Date(last modified): 30.01.2006
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

if (preg_match("/^(mission|missions) ([0-9]+)$/i", $message, $arr)) {
	$missionQl = $arr[2];

	if ($missionQl > 0 && $missionQl <= 250) {
		$msg = "QL{$missionQl} missions can be rolled from these level players:";
	
		$db->query("SELECT * FROM levels WHERE level = $missionQl");
		while (($row == $db->fObject()) !== FALSE) {
			$array = explode(",", $row->missions);
			if (in_array($missionQl, $array)) {
				$msg .= " " . $row->level;
			}
		}
	} else {
		$msg = "Missions are only available between QL1 and QL250";
	}

    // Send info back
    bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>