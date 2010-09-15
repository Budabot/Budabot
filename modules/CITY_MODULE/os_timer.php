<?php
   /*
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Automatically sets a Orbital Strike timer after an OS has gone off
   ** Modified by Legendadv/Etherealcrat/Zephyrforce for use with recent budabot releases/FC changes
   ** Version: 1.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 20-DEC-2008
   ** Date(last modified): 9-Mar-2010
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


// create a timer for 15m when an OS/AS is launched (so org knows when they can launch again)
// [Org Msg] Blammo! Player has launched an orbital attack!

if(preg_match("/^Blammo! (.+) has launched an orbital attack!$/i", $message, $array)) {
	bot::send("OS !timer was set for 15 minutes", "guild");
	$orgName = $this->vars["my guild"];

	$launcher = $array[1];

	$newTimerName = "";
	for ($i = 1; $i <= 10; $i++) {
		$unique = true;

		$newTimerName = "$orgName OS/AS $i";
		foreach($this->vars["Timers"] as $key => $timer) {
		  	if($timer["name"] == $newTimerName) {
			  	$unique = false;
			    break;
			}
		}

		if ($unique) {
			break;
		}
	}

	$timer = time() + (15*60); // set timer for 15 minutes
	$this->vars["Timers"][] = array("name" => $newTimerName, "owner" => $launcher, "mode" => 'guild', "timer" => $timer, "settime" => time());
	$db->query("INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`) VALUES ('$newTimerName', '$launcher', 'guild', $timer, ".time().")");
}

?>
