<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Lets a player remove from his choosen loot
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 11.10.2006
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

global $loot;
global $raidloot;

if (preg_match("/^rem$/i", $message)) {
	if ($this->vars["raid_status"] != "" && $this->vars["raid_pts"] == 0) {
	  	forEach ($raidloot as $key => $value) {
			forEach ($value as $key1 => $value1) {
				if ($raidloot[$key][$key1]["users"][$sender] == true) {
					unset($raidloot[$key][$key1]["users"][$sender]);
				}
			}
		}
	
		$msg = "You have been removed from all rolls";
	  	bot::send($msg, $sender);
	} else if (count($loot) > 0) {
	  	forEach ($loot as $key => $item) {
			if ($loot[$key]["users"][$sender] == true) {
				unset($loot[$key]["users"][$sender]);
			}
		}
	
		$msg = "You have been removed from all rolls";
	  	bot::send($msg, $sender);
	} else {
		bot::send("There is nothing where you could add in.", $sender);
	}
}

?>