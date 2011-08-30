<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Checks the guild Onlinelist
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 26.02.2006
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

if ($chatBot->is_ready()) {
	$db->begin_transaction();
	   
	$sql = "SELECT name FROM `online`";
	$db->query($sql);
	$data = $db->fObject('all');
	$array = array();
	forEach ($data as $row) {
		$array []= $row->name;
	}

	forEach ($chatBot->guildmembers as $name => $rank) {
		if (Buddylist::is_online($name)) {
			if (in_array($name, $array)) {
				$db->exec("UPDATE `online` SET `dt` = " . time() . " WHERE `name` = '$name' AND added_by = '<myname>' AND channel_type = 'guild'");
			} else {
				$db->exec("INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$name', '<myguild>', 'guild', '<myname>', " . time() . ")");
			}
		}
	}

	forEach ($chatBot->chatlist as $name => $value) {
		if (in_array($name, $array)) {
			$db->exec("UPDATE `online` SET `dt` = " . time() . " WHERE `name` = '$name' AND added_by = '<myname>' AND channel_type = 'priv'");
		} else {
			$db->exec("INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$name', '<myguild> Guest', 'priv', '<myname>', " . time() . ")");
		}
	}

	$time_to_expire = (time() - (Setting::get('online_expire') * 60));
	$sql = "DELETE FROM `online` WHERE `dt` < {$time_to_expire}";
	$db->exec($sql);

	$db->commit();
}

?>