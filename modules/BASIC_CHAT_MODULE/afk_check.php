<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: AFK Handling(checks afk status)
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 26.01.2007
   **
   ** Copyright (C) 2005, 2006, 2007 Carsten Lohmann
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

// to stop raising and lowering the cloak messages from triggering afk check
if ($sender == -1) {
	return;
}

if (!preg_match("/^.?afk(.*)$/i", $message)) {
	$db->query("SELECT afk FROM online WHERE `name` = '{$sender}' AND added_by = '<myname>' AND channel_type = '$type'");
	$row = $db->fObject();

	if ($row != null && $row->afk != '') {
		$db->exec("UPDATE online SET `afk` = '' WHERE `name` = '{$sender}' AND added_by = '<myname>' AND channel_type = '$type'");
		$msg = "<highlight>{$sender}<end> is back";
		$chatBot->send($msg, $type);
	} else {
		$name = split(" ", $message, 2);
		$name = $name[0];
		$name = ucfirst(strtolower($name));
		$uid = $chatBot->get_uid($name);
		if ($uid) {
			$db->query("SELECT afk FROM online WHERE `name` = '{$name}' AND added_by = '<myname>'");

			if ($db->numrows() != 0) {
				$row = $db->fObject();
				if ($row->afk == "1") {
					$msg = "<highlight>{$name}<end> is currently AFK.";
					$chatBot->send($msg, $type);
				} else if ($row->afk == "kiting") {
					$msg = "<highlight>{$name}<end> is currently Kiting.";
					$chatBot->send($msg, $type);
				} else if ($row->afk != "") {
					$msg = "<highlight>{$name}<end> is currently AFK: <highlight>{$row->afk}<end>";
					$chatBot->send($msg, $type);
				}
			}
		}
	}
}

?>
