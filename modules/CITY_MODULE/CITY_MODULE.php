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
   
	$MODULE_NAME = "CITY_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, 'org_city');

    Command::register($MODULE_NAME, "", "city_guild.php", "city", "guild", "Shows the status of the Citycloak");
	Command::register($MODULE_NAME, "", "city_guild.php", "cloak", "guild", "Shows the status of the Citycloak");

	Event::register($MODULE_NAME, "guild", "city_guild.php", "city", "Records when the cloak is raised or lowered");
    Event::register($MODULE_NAME, "1min", "city_guild_timer.php", "city", "Checks timer to see if cloak can be raised or lowered");
	Event::register($MODULE_NAME, "1min", "city_guild_raise_cloak.php", "city", "Reminds the player who lowered cloak to raise it when it can be raised.");
	Event::register($MODULE_NAME, "logOn", "city_guild_logon.php", "city", "Displays summary of city status.");
	
	Setting::add($MODULE_NAME, "showcloakstatus", "Show cloak status to players at logon", "edit", "1", "Never;When cloak is down;Always", "0;1;2");
	
	// Helpfiles
	Help::register($MODULE_NAME, "citycloak", "citycloak.txt", "guild", "Status of the citycloak");
	
	// Auto Wave
	Command::register($MODULE_NAME, "guild", "start.php", "startraid", "guild", "manually starts wave counter");
	Command::register($MODULE_NAME, "guild", "stopraid.php", "stopraid", "guild", "manually stops wave counter");
	Event::register($MODULE_NAME, "guild", "start.php", "none", "Starts a wave counter when cloak is lowered");
	Event::register($MODULE_NAME, "2sec", "counter.php", "none", "Checks timer to see when next wave should come");
	
	// OS/AS timer
	Event::register($MODULE_NAME, "orgmsg", "os_timer.php", "none", "Sets a timer when an OS/AS is launched");
?>