<?php
   /*
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Sends a message to each player about the city status when they logon, and reminds them to raise cloak when it is time
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 20-NOV-2007
   ** Date(last modified): 25-FEB-2010
   ** 
   ** Copyright (C) 2010 Jason Wheeler
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
   */
   
	$MODULE_NAME = "CITY_CLOAK_MODULE";
	$PLUGIN_VERSION = 0.1;
	
	$this->loadSQLFile($MODULE_NAME, 'org_city');

    $this->command("guild", "$MODULE_NAME/city_guild.php", "city", ALL, "Shows the status of the Citycloak");

    $this->event("guild", "$MODULE_NAME/city_guild.php", "city");
    $this->event("1min", "$MODULE_NAME/city_guild_timer.php", "city");
	$this->event("1min", "$MODULE_NAME/city_guild_raise_cloak.php", "city", "Reminds the player who lowered cloak to raise it when it can be raised.");
	$this->event("logOn", "$MODULE_NAME/city_guild_logon.php", "city", "Displays summary of city status.");
	
	// Help files
	$this->help("citycloak", "$MODULE_NAME/citycloak.txt", GUILDMEMBER, "Status of the citycloak");
	
	// Auto Wave
	$this->command("guild","$MODULE_NAME/start.php", "startraid");
	$this->command("guild","$MODULE_NAME/stopraid.php", "stopraid");
	$this->event("setup", "$MODULE_NAME/setup.php");
	$this->event("guild", "$MODULE_NAME/start.php");
	$this->event("2sec", "$MODULE_NAME/counter.php");
?>