<?
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
if(eregi("^rem$", $message)) {
	//Check if a flat(multiroll) or pts roll is going on
	if($this->vars["raid_pts"] > 0) {
		$msg = "<red>This raid is pts rolled. Use instead unbid.<end>";
		bot::send($msg, $sender);
		return;
	}
	
	if($this->vars["raid_pts"] == 0 && $this->vars["raid_flat_multiroll"] == 1) {
		$msg = "<red>You need to specify a slot where you want to be removed!<end>";
		bot::send($msg, $sender);
		return;
	}
	
	$index = $this->vars["raid_loot_index"];
	$cat = $this->vars["raid_loot_cat"];
	
	//Check if the player is added
	if(!$raidloot[$cat][$index]["users"][$sender]) {
		$msg = "<red>You were never added to the roll<end>";
		bot::send($msg, $sender);
		return;
	}
	
	//Remove the player from the slot
    unset($raidloot[$cat][$index]["users"][$sender]);
	
    $msg = "You have been removed from the roll of <highlight>\"{$raidloot[$cat][$index]["name"]}\"<end>.";
	bot::send($msg, $sender);
	
	$msg = "<highlight>$sender<end> has been added for this roll.";
	bot::send($msg);
} elseif(eregi("^rem ([0-9]+)$", $message, $arr)) {
  	$slot = $arr[1];
  	$found = false;
  	if($this->vars["raid_status"] == "") {
  	  	if($slot != 1 && $slot != 2 && $slot != 3) {
			$msg = "The slot <highlight>$slot<end> doesn´t exist.";
			bot::send($msg,  $sender);
			return;
		}
		if(isset($vote[$slot]["users"][$sender])) {
		  	unset($vote[$slot]["users"][$sender]);
		  	$msg = "You removed yourself from the roll of <highlight>{$loot[$slot]["name"]}<end>.";
		} else
			$msg = "You was never in the roll of <highlight>{$loot[$slot]["name"]}<end>.";
	    bot::send($msg, $sender);
	} elseif($this->vars["raid_status"] != "" && $this->vars["raid_pts"] == 0) {
	  	foreach($raidloot as $key => $value) {
			foreach($value as $key1 => $value1) {
				if($key1 == $slot) {
					if($raidloot[$key][$key1]["users"][$sender]) {
		 			  	unset($raidloot[$key[$key1]]["users"][$sender]);
					  	$msg = "You removed yourself from the roll of <highlight>{$loot[$slot]["name"]}<end>.";
				 	} else 
						$msg = "You was never in the roll of <highlight>{$loot[$slot]["name"]}<end>.";
				    bot::send($msg, $sender);
					return;	 	
				}
			}
		}
				
		$msg = "The slot <highlight>$slot<end> doesn´t exist.";
		bot::send($msg,  $sender);
		return;
	} else
		bot::send("There is no use for this command currently", $sender);
} else
	$syntax_error = true;
?>