<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Kicks a player from the privatechannel
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 17.02.2006
   ** Date(last modified): 18.02.2006
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

if (preg_match("/^adduser (.+)$/i", $message, $arr)) {
	$uid = AoChat::get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	if (!$uid) {
		$msg = "Player <highlight>$name<end> does not exist.";
	} else {
		$db->query("SELECT * FROM members_<myname> WHERE `name` = '$name'");
		if ($db->numrows() != 0) {
			$msg = "<highlight>$name<end> is already a member of this bot.";
		} else {
			$db->exec("INSERT INTO members_<myname> (`name`, `autoinv`) VALUES ('$name', 1)");
			$msg = "<highlight>$name<end> has been added as a member of this bot.";
		}

		// always add in case 
		Buddylist::add($name, 'member');
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>