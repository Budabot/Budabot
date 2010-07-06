<?php
/*
   ** Author: Marinerecon (RK2)
   ** Description: Removes an item from the roll
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 07.29.2009
   ** Date(last modified): 07.29.2009
   ** 
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

	if(preg_match("/^remloot ([0-9]+)$/i", $message, $arr)) {
		$key = $arr[1];
		// validate item existance on loot list
		if($key > 0 && $key <= count($loot)){
		
			// if removing this item empties the list, clear the loot list properly
			if(count($loot) <= 1){
				$loot = "";
				$this->send("Item in slot <highlight>#".$key."<end> was the last item in the list. The list has been cleared.");
				return;
			}
			// remove the item by shifting lower items up one slot and remove last slot
			else{
				$loop = $key;
				while($loop < count($loot)){
					$loot[$loop] = $loot[$loop+1];
					$loop++;
				}
			unset($loot[count($loot)]);
			$this->send("Deleting item in slot <highlight>#".$key."<end>");
			return;
			}
		}
		else{
			$this->send("There is no item at slot <highlight>#".$key."<end>");
			return;
		}
	}

?>