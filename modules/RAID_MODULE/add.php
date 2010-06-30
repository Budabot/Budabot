<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Adding to a Lootslot
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 22.11.2006
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
global $raidlist;
global $raidloot;

if(preg_match("/^add$/i", $message)) {
	//Check if a flat(multiroll) or pts roll is going on
	if($this->vars["raid_pts"] > 0) {
		$msg = "<red>This raid is pts rolled. Use instead bid.<end>";
		bot::send($msg, $sender);
		return;
	}
	
	if($this->vars["raid_pts"] == 0 && $this->vars["raid_flat_multiroll"] == 1) {
		$msg = "<red>You need to specify a slot where you want to add!<end>";
		bot::send($msg, $sender);
		return;
	}
	
	if(!isset($raidlist[$sender])) {
		$msg = "<red>You need to be on the raidlist to be able to add to an item!<end>";
		bot::send($msg, $sender);
		return;		
	}

	$index = $this->vars["raid_loot_index"];
	$cat = $this->vars["raid_loot_cat"];
	
	//Check if minlvl is set and if the player is higher then it
	if(isset($raidloot[$cat][$index]["minlvl"])) {
	  	$whois = new whois($sender);
	  	if($whois->level < $raidloot[$cat][$index]["minlvl"]) {
		    $msg = "You need to be at least lvl<highlight>{$raidloot[$cat][$index]["minlvl"]}<end> to join this roll.";
	  		bot::send($msg, $sender);
	  		return;
		}
	}
	  	
	//Check if the player is already added
	
	if($raidloot[$cat][$index]["users"][$sender]) {
		$msg = "<red>You are already assigned to this roll<end>";
		bot::send($msg, $sender);
		return;
	}
	
	//Add the player to the choosen slot
    $raidloot[$cat][$index]["users"][$sender] = true;
	
    $msg = "You have been assigned to the roll of <highlight>\"{$raidloot[$cat][$index]["name"]}\"<end>.";
	bot::send($msg, $sender);
	
	$msg = "<highlight>$sender<end> has been added for this roll.";
	bot::send($msg);
} elseif(preg_match("/^add 0$/i", $message)) {
 	//Raid with flatrolls
	if ($this->vars["raid_status"] != "" && $this->vars["raid_pts"] == 0) {
	  	foreach($raidloot as $key => $value) {
			foreach($value as $key1 => $value1) {
				if($raidloot[$key][$key1]["users"][$sender] == true) {
					unset($raidloot[$key][$key1]["users"][$sender]);
				}
			}
		}

		$msg = "You have been removed from all rolls";
	  	bot::send($msg, $sender);	  
	} elseif(count($loot) > 0) {
	  	foreach($loot as $key => $item)
			if($loot[$key]["users"][$sender] == true)
				unset($loot[$key]["users"][$sender]);		 		
	
		$msg = "You have been removed from all rolls";
	  	bot::send($msg, $sender);	   
	} else
		bot::send("There is nothing where you could add in.", $sender);
} elseif(preg_match("/^add ([0-9]+)$/i", $message, $arr)) {
  	$slot = $arr[1];
  	$found = false;
  	//Raid with flatrolls
	if($this->vars["raid_status"] != "" && $this->vars["raid_pts"] == 0) {
  	  	$slot = $arr[1];
		
		if($this->vars["raid_pts"] == 0 && $this->vars["raid_flat_multiroll"] == 0) {
			$msg = "<red>Use add alone only!<end>";
			bot::send($msg, $sender);
			return;
		}
		
		if(!isset($raidlist[$sender])) {
			$msg = "<red>You need to be on the raidlist to be able to add to an item!<end>";
			bot::send($msg, $sender);
			return;		
		}
	
		//Check if the slot exists
		$found = false;
		foreach($raidloot as $key => $value) {
			foreach($value as $key1 => $value1) {
				if($key1 == $slot) {
					$found = true;
					$cat = $key;
					$index = $key1;
					break;
				}
			}
			if($found)
				break;
		}
		
	  	if(!$found) {
	  		$msg = "The slot you trying to add in doesn't exists";
		  	bot::send($msg, $sender);
		  	return;
	  	}
	
		//Check if minlvl is set and if the player is higher then it
		if(isset($raidloot[$cat][$index]["minlvl"])) {
		  	$whois = new whois($sender);
		  	if($whois->level < $raidloot[$cat][$index]["minlvl"]) {
			    $msg = "You need to be at least lvl<highlight>{$raidloot[$cat][$index]["minlvl"]}<end> to join this roll.";
		  		bot::send($msg, $sender);
		  		return;
			}
		}
	  	
	  	//Remove the player from other slots if set
	  	$found = false;
	  	foreach($raidloot as $key => $value) {
	  		foreach($value as $key1 => $value1) {
				if($raidloot[$key][$key1]["users"][$sender] == true) {
					unset($raidloot[$key][$key1]["users"][$sender]);
					$found = true;
					break;
				}		 		
			}
			if($found)
				break;
		}
		
		//Add the player to the choosen slot
	    $raidloot[$cat][$index]["users"][$sender] = true;
	
	    if($found == false)
		    $msg = "You have been assigned to the roll of <highlight>\"{$raidloot[$cat][$index]["name"]}\"<end>.";
		else
			$msg = "You have moved to the roll of <highlight>\"{$raidloot[$cat][$index]["name"]}\"<end>.";
		
	  	bot::send($msg, $sender);
	} elseif(count($loot) > 0) {
  	  	$slot = $arr[1];

		//Check if the slot exists
	  	if (!isset($loot[$slot])) {
	  		$msg = "The slot you trying to add in doesn't exists";
		  	bot::send($msg, $sender);
		  	return;
	  	}
	
		//Check if minlvl is set and if the player is higher then it
		if (isset($loot[$slot]["minlvl"])) {
		  	$whois = new whois($sender);
		  	if ($whois->lvl < $loot[$slot]["minlvl"]) {
			    $msg = "You need to be at least lvl<highlight>{$loot[$slot]["minlvl"]}<end> to join this roll.";
		  		bot::send($msg, $sender);
		  		return;
			}
		}
	  	
	  	//Remove the player from other slots if set
	  	$found = false;
	  	forEach ($loot as $key => $item) {
			if ($loot[$key]["users"][$sender] == true) {
				unset($loot[$key]["users"][$sender]);
				$found = true;
			}
		}
	
		//Add the player to the choosen slot
	    $loot[$slot]["users"][$sender] = true;
	
	    if($found == false)
		    $msg = "You have been assigned to the roll of <highlight>\"{$loot[$slot]["name"]}\"<end>.";
		else
			$msg = "You have moved to the roll of <highlight>\"{$loot[$slot]["name"]}\"<end>.";
		
	  	bot::send($msg, $sender);
	} else
		bot::send("No list available where you can add in.", $sender);
} else
	$syntax_error = true;
?>