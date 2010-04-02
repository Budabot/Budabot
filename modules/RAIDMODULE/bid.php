<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Bid on an Item
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

global $raidloot;
global $raidlist;
if(eregi("^bid ([0-9]+)$", $message, $arr)) {
  	$points = $arr[1];
	if($this->vars["raid_status"] == "") {
		$msg = "No Raid started.";
		bot::send($msg);
		return;
	}
	
	if($this->vars["raid_pts"] == 0) {
		$msg = "All items are flatrolled at the moment. To add use /tell <myname> add (slot number)";
	  	bot::send($msg, $sender);
	  	return;
	}

	if(!isset($raidlist[$sender])) {
	  	$msg = "You need to be on the raidlist to be able to bid on an item.";
	  	bot::send($msg, $sender);
	  	return;
	}
	
	if($this->vars["raid_loot_index"] == 0 && $this->vars["raid_loot_cat"] == 0) {
	  	bot::send("No item on that you can bid is available!", $sender);
	  	return;
	}
	
	$index = $this->vars["raid_loot_index"];
	$cat = $this->vars["raid_loot_cat"];

	if($points == 0 && isset($raidloot[$cat][$index]["users"][$sender])) {
		bot::send("Your bid for the item <highlight>{$raidloot[$cat][$index]["name"]}<end> has been removed.", $sender);
	  	bot::send("<highlight>$sender<end> has removed his bid.");
		unset($raidloot[$cat][$index]["users"][$sender]);
		return;
	}
	
	//Check if minlvl is set and if the player is higher then it
	if(isset($raidloot[$cat][$index]["minlvl"])) {
	  	$whois = new whois($sender);
	  	if($whois->level < $raidloot[$cat][$index]["minlvl"]) {
		    $msg = "You need to be at least lvl<highlight>{$raidloot[$cat][$index]["minlvl"]}<end> to bid on this item.";
	  		bot::send($msg, $sender);
	  		return;
		}
	}
		
	$db->query("SELECT * FROM `points_db_<myname>` WHERE `name` = '$sender'");
	$result = $db->fObject();
	if($this->vars["raid_bid_cost"] != 0 && $result->points <= $this->vars["raid_bid_cost"]) {
	 	$pts = $this->vars["raid_bid_cost"] + 1;
	  	bot::send("You need at least {$pts}points to be able to bid on an item.", $sender);
	  	return;
	} elseif($this->vars["raid_bid_cost"] != 0 && ($points + $this->vars["raid_bid_cost"]) > $result->points) {
	 	$maxpts = $result->points - $this->vars["raid_bid_cost"];
	  	bot::send("You can bid only <highlight>$maxpts<end>point(s).", $sender);
	  	return;
	} elseif($points < $this->vars["raid_bid_cost"]) {
	 	$pts = $this->vars["raid_bid_cost"] + 1;
	  	bot::send("You need to bid at least {$pts}point(s).", $sender);
	  	return;	  	 	
	} elseif(isset($raidloot[$cat][$index]["users"][$sender])) {
	  	bot::send("Your bid for the item <highlight>{$raidloot[$cat][$index]["name"]}<end> was accepted.", $sender);
	  	bot::send("<highlight>$sender<end> has updated his bid.");
		$raidloot[$cat][$index]["users"][$sender] = $points;	 	
	} else {
	  	bot::send("Your bid for the item <highlight>{$raidloot[$cat][$index]["name"]}<end> was accepted.", $sender);
	  	bot::send("<highlight>$sender<end> is bidding.");
		$raidloot[$cat][$index]["users"][$sender] = $points;
	}
} else
	$syntax_error = true;
?>