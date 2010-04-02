<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the raidloot
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
global $raid_points;
if(eregi("^raidloot$", $message)) {
 	//If no raid is started return
	if($this->vars["raid_status"] == "") {
		$msg = "No Raid started.";
		bot::send($msg);
		return;
	}
	
	//If the items are ptsrolled and no item is beeing rolled atm do
	if($this->vars["raid_loot_cat"] == 0 && $this->vars["raid_loot_index"] == 0 && $this->vars["raid_pts"] != 0) {
		foreach($raidloot as $key => $value) {
			$list  = "<header>::::: Lootlist for Raid {$this->vars["raid_status"]} :::::<end>\n\n";
			foreach($value as $key1 => $value1) {
			    $list .= "<img src=rdb://{$raidloot[$key][$key1]["icon"]}>\n";
				if($raidloot[$key][$key1]["aoid"] != 0)
				    $list .= bot::makeItem($raidloot[$key][$key1]["aoid"],$raidloot[$key][$key1]["aoid"],$raidloot[$key][$key1]["ql"],$raidloot[$key][$key1]["name"])."\n";
				else
					$list .= "<highlight>{$raidloot[$key][$key1]["name"]}<end>\n";
				
				if(isset($raidloot[$key][$key1]["minlvl"]))
					$list .= "<u>Details:</u> [<highlight>Min Level: {$raidloot[$key][$key1]["minlvl"]}<end>] [<highlight>Amount: {$raidloot[$key][$key1]["amount"]}<end>]";
	
				$db->query("SELECT points FROM raids_history_<myname> WHERE `item` = \"{$raidloot[$key][$key1]["name"]}\" AND `points` != 'flat'");
				if($db->numrows() != 0) {
				 	$avgpts = 0;
				 	$i = 0;
					while($row = $db->fObject()) {
						$avgpts += $row->points;
						$i++;
					}
					$avgpts /= $i;
					$avgpts = round($avgpts, 2);
					$list .= "[<highlight>Average pts.: $avgpts<end>]";
				}
				
				$item_name = str_replace("'", "\'", $raidloot[$key][$key1]["name"]);
				$list .= "\n<u>Action/Info:</u> ";
				if($raidloot[$key][$key1]["mode"] == "pts") {
				    $list .= bot::makeLink("Start Roll", "/tell <myname> raidloot $item_name", "chatcmd");
				} elseif($raidloot[$key][$key1]["amount"] == 0) {
					$list .= "<highlight>All Items of this type are rolled<end> ";
				    $list .= bot::makeLink("Start Roll", "/tell <myname> raidloot $item_name", "chatcmd");
				}
			
				$list .= "\n\n";	
			}
			if($key == "general")
				$msg = bot::makeLink("Loot list for {$this->vars["raid_status"]}", $list);
			else
				$msg = bot::makeLink("Loot list for {$this->vars["raid_status"]}($key)", $list);
				
			bot::send($msg);
		}
	//If the items are ptsrolled and an item is rolled atm do
	} elseif($this->vars["raid_loot_cat"] != "0" && $this->vars["raid_loot_index"] != 0 && $this->vars["raid_pts"] > 0) {
		$cat = $this->vars["raid_loot_cat"];
		$index = $this->vars["raid_loot_index"];
	 	if($raidloot[$cat][$index]["aoid"] != 0)
		    $msg = bot::makeItem($raidloot[$cat][$index]["aoid"],$raidloot[$cat][$index]["aoid"],$raidloot[$cat][$index]["ql"],$raidloot[$cat][$index]["name"]);
		else
			$msg = "<highlight>".$raidloot[$cat][$index]["name"]."<end>";

		if(count($raidloot[$cat][$index]["users"]) == 0) {
			$msg .= " (<highlight>0 added<end>)";
		} else {
			$users = count($raidloot[$cat][$index]["users"]);
			$list = "<header>::::: Players added to the roll of {$raidloot[$cat][$index]["name"]} :::::<end>\n\n";
			foreach($raidloot[$cat][$index]["users"] as $key => $value) {
				$list .= "<tab>- <highlight>$key<end>\n";
			}
			$msg .= " (".bot::makeLink("$users added", $list).")";
		}
		bot::send($msg);
		bot::send("To add to this item use <highlight>/tell <myname> bid (amount of points)<end>, to remove use <highlight>/tell <myname> unbid<end>");
	//If the items are flatrolled(mode is set to multiroll) do
	} elseif($this->vars["raid_pts"] == 0 && $this->vars["raid_flat_multiroll"] == 1) {
	 	$itemsflat = 0;
		$list  = "<header>::::: Flatroll list for {$this->vars["raid_status"]} :::::<end>\n\n";
		foreach($raidloot as $key => $value) {
		 	foreach($value as $key1 => $value1) {
		 	 	$itemsflat++;
			    $list .= "<img src=rdb://{$raidloot[$key][$key1]["icon"]}>\n";
				if(isset($raidloot[$key][$key1]["aoid"]))
				    $list .= bot::makeItem($raidloot[$key][$key1]["aoid"], $raidloot[$key][$key1]["aoid"], $raidloot[$key][$key1]["ql"], $raidloot[$key][$key1]["name"])."\n";
				else
					$list .= "<highlight>{$raidloot[$key][$key1]["name"]}<end>\n";
					
				$added_players = count($raidloot[$key][$key1]["users"]);
					
				$list .= "<u>Details:</u> [<highlight>Players added: $added_players<end>] [<highlight>Amount: {$raidloot[$key][$key1]["amount"]}<end>]";
				if(isset($raidloot[$key][$key1]["minlvl"]))
					$list .= "[<highlight>Min Level: {$raidloot[$key][$key1]["minlvl"]}<end>] ";
	
				$add = bot::makeLink("Add", "/tell <myname> add $key1", "chatcmd");
				$rem = bot::makeLink("Remove", "/tell <myname> add 0", "chatcmd");			    
	
				$list .= "\n<u>Action/Info:</u> $add/$rem\n";
	
			  	$list .= "<u>Players added:</u>";
				if(count($raidloot[$key][$key1]["users"]) > 0)
					foreach($raidloot[$key][$key1]["users"] as $key => $value)
					  	$list .= " [<highlight>$key<end>]";
				else
					$list .= " <highlight>None added yet.<end>";
				$list .= "\n\n";
			}
		}
		if($itemsflat == 0) {	 	
			bot::send("<red>Nothing to roll<end>");
		} else {
			$msg = bot::makeLink("Flatroll list for {$this->vars["raid_status"]}", $list);
			bot::send($msg);
		}
	//If the items are flatrolled(mode is set to singleroll) and no item is beeing rolled atm do
	} elseif($this->vars["raid_pts"] == 0 && $this->vars["raid_flat_multiroll"] == 0 && $this->vars["raid_loot_cat"] == 0 && $this->vars["raid_loot_index"] == 0) {
		foreach($raidloot as $key => $value) {
			$list  = "<header>::::: Lootlist for Raid {$this->vars["raid_status"]} :::::<end>\n\n";
			foreach($value as $key1 => $value1) {
			    $list .= "<img src=rdb://{$raidloot[$key][$key1]["icon"]}>\n";
				if($raidloot[$key][$key1]["aoid"] != 0)
				    $list .= bot::makeItem($raidloot[$key][$key1]["aoid"],$raidloot[$key][$key1]["aoid"],$raidloot[$key][$key1]["ql"],$raidloot[$key][$key1]["name"])."\n";
				else
					$list .= "<highlight>{$raidloot[$key][$key1]["name"]}<end>\n";
				
				$list .= "<u>Details:</u>";
				if(isset($raidloot[$key][$key1]["minlvl"]))
					$list .= "[<highlight>Min Level: {$raidloot[$key][$key1]["minlvl"]}<end>]";
		
				$list .= " [<highlight>Amount: {$raidloot[$key][$key1]["amount"]}<end>]";

				$item_name = str_replace("'", "\'", $raidloot[$key][$key1]["name"]);
				$list .= "\n<u>Action/Info:</u> ";
				if($raidloot[$key][$key1]["mode"] == "flat") {
				    $list .= bot::makeLink("Start Roll", "/tell <myname> raidloot $item_name", "chatcmd");
				} elseif($raidloot[$key][$key1]["amount"] == 0) {
					$list .= "<highlight>All Items of this type are rolled<end> ";
					$list .= bot::makeLink("Start Roll", "/tell <myname> raidloot $item_name", "chatcmd");
				}
				
				$list .= "\n\n";	
			}
			if($key == "general")
				$msg = bot::makeLink("Loot list for {$this->vars["raid_status"]}", $list);
			else
				$msg = bot::makeLink("Loot list for {$this->vars["raid_status"]}($key)", $list);
					
			bot::send($msg);
		}
	//If the items are flatrolled(mode is set to singleroll) and an item is beeing rolled atm do		
	} elseif($this->vars["raid_loot_cat"] != "0" && $this->vars["raid_loot_index"] != 0 && $this->vars["raid_pts"] == 0 && $this->vars["raid_flat_multiroll"] == 0) {
		$cat = $this->vars["raid_loot_cat"];
		$index = $this->vars["raid_loot_index"];
	 	if($raidloot[$cat][$index]["aoid"] != 0)
		    $msg = bot::makeItem($raidloot[$cat][$index]["aoid"],$raidloot[$cat][$index]["aoid"],$raidloot[$cat][$index]["ql"],$raidloot[$cat][$index]["name"]);
		else
			$msg = "<highlight>".$raidloot[$cat][$index]["name"]."<end>";

		if(count($raidloot[$cat][$index]["users"]) == 0) {
			$msg .= " (<highlight>0 added<end>)";
		} else {
			$users = count($raidloot[$cat][$index]["users"]);
			$list = "<header>::::: Players added to the roll of {$raidloot[$cat][$index]["name"]} :::::<end>\n\n";
			foreach($raidloot[$cat][$index]["users"] as $key => $value) {
				$list .= "<tab>- <highlight>$key<end>\n";
			}
			$msg .= " (".bot::makeLink("$users added", $list).")";
		}
		bot::send($msg);
		bot::send("To add to this item use <highlight>/tell <myname> add<end>, to remove use <highlight>/tell <myname> rem<end>");
	}
} elseif(eregi("^raidloot (.+)$", $message, $arr)) {
  	$item = strtolower($arr[1]);
	
	if($this->vars["raid_loot_index"] != 0) {
		$msg = "Roll for <highlight>{$raidloot[$this->vars["raid_loot_cat"]][$this->vars["raid_loot_index"]]["name"]}<end> needs to be finished before you can start another roll.";
		if($type == "msg")
			bot::send($msg, $sender);
		elseif($type == "priv")
			bot::send($msg);
		return;	
	}
	
	$item = str_replace("\'", "'", $item);
	$found = false;
	foreach($raidloot as $key => $value) {
		foreach($value as $key1 => $value1) {
			if(strtolower($raidloot[$key][$key1]["name"]) == $item) {
				$this->vars["raid_loot_index"] = $key1;
				$this->vars["raid_loot_cat"] = $key;
				$found = true;
				break;
			}
		}
		if($found)
			break;
	}
	
	if(!$found) {
		$msg = "Item <highlight>$item<end> wasn´t found on the loottable of this raid.";
		if($type == "msg")
			bot::send($msg, $sender);
		elseif($type == "priv")
			bot::send($msg);
		return;
	}

	$cat = $this->vars["raid_loot_cat"];
	$index = $this->vars["raid_loot_index"];

	if($raidloot[$cat][$index]["mode"] == "flat" && $this->vars["raid_pts"] > 0) {
		$msg = "<red>This item is set as rolled flat!<end>";
		if($type == "msg")
			bot::send($msg, $sender);
		elseif($type == "priv")
			bot::send($msg);
		return;
	}
			
 	if($raidloot[$cat][$index]["aoid"] != 0)
	    bot::send("Roll started for item: ".bot::makeItem($raidloot[$cat][$index]["aoid"],$raidloot[$cat][$index]["aoid"],$raidloot[$cat][$index]["ql"],$raidloot[$cat][$index]["name"]));
	else
		bot::send("Roll started for item: <highlight>{$raidloot[$cat][$index]["name"]}<end>");
	
	if($this->vars["raid_pts"] > 0)
		bot::send("To add to this item use <highlight>/tell <myname> bid (amount of points)<end>, to remove use <highlight>/tell <myname> unbid<end>");
	else
		bot::send("To add to this item use <highlight>/tell <myname> add<end>, to remove use <highlight>/tell <myname> rem<end>");
} else
	$syntax_error = true;
?>