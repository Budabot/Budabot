<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Force an update of the org roster
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 16.01.2007
   ** Date(last modified): 31.01.2007
   ** 
   ** Copyright (C) 2007 Carsten Lohmann
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
   
if (preg_match("/^updateorg$/i", $message)) {
 	$force_update = true;
 	$chatBot->send("Starting updating Organisation roster. Please Standby.", $sendto);
	include './modules/GUILD_MODULE/roster_guild.php';
	$chatBot->send("Done", $sendto);
} else {
	$syntax_error = true;
}

?>