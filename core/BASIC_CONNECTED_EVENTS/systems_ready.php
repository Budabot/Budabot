<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Sends a message to guild and to the Admin on a successfull logon
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 27.04.2006
   ** Date(last modified): 30.10.2006
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

//Send Admin(s) a tell that the bot is online
forEach ($this->admins as $name => $info) {
	if ($name != "" && $info["level"] == 4 && $info["online"] == "online") {
		bot::send("<myname> is <green>online<end>. For updates or help use the Budabot Forums <highlight>http://budabot.com<end>", $name);
	}
}

//Send a message to guild channel
bot::send("Logon Complete :: All systems ready to use.", "guild");
?>