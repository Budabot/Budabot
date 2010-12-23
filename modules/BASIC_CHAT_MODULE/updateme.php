<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Updates the character infos
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 05.06.2006
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

if (preg_match("/^updateme$/i", $message)) {
	$info = Player::get_by_name($sender, true);
	if ($info === null) {
		bot::send("An Error occurred while trying to update your info. Please try again later.", $sendto);
		return;
	} else {
		bot::send("Update successful.", $sendto);
	}
} else {
	$syntax_error = true;
}
?>