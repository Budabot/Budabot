<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Show a loot list
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 10.10.2006
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
global $vote;
if(eregi("^list$", $message)) {
	if($this->vars["raid_status"] == "") {
	  	if(is_array($loot)) {
		  	$list = "<header>::::: Loot List :::::<end>\n\n";
			foreach($loot as $key => $item) {
				$add = bot::makeLink("Add", "/tell <myname> add $key", "chatcmd");
				$rem = bot::makeLink("Remove", "/tell <myname> add 0", "chatcmd");
				$added_players = count($item["users"]);
	
				$list .= "<u>Slot #$key</u>\n";
			  	if($item["icon"] != "")
			  		$list .= "<img src=rdb://{$item["icon"]}>\n";
	
				$list .= "Item: <highlight>{$item["name"]}<end>\n";
				if($item["minlvl"] != "")
					$list .= "MinLvl set to <highlight>{$item["minlvl"]}<end>\n";
				$list .= "<highlight>$added_players<end> Total ($add/$rem)\n";
			  	$list .= "Players added:";
				if(count($item["users"]) > 0)
					foreach($item["users"] as $key => $value)
					  	$list .= " [<highlight>$key<end>]";
				else
					$list .= " None added yet.";
				
				$list .= "\n\n";
			}
			$msg = bot::makeLink("Loot List", $list);
		} else
			$msg = "No List exists yet.";
	} elseif($this->vars["raid_status"] != "" && $this->vars["raid_loot_pts"] == 0) {
	  	if(is_array($raidloot)) {
		  	$list = "<header>::::: Raidloot List :::::<end>\n\n";
			foreach($raidloot as $key => $item) {
				$add = bot::makeLink("Add", "/tell <myname> add $key", "chatcmd");
				$rem = bot::makeLink("Remove", "/tell <myname> add 0", "chatcmd");
				$added_players = count($item["users"]);
	
				$list .= "<u>Slot #$key</u>\n";
			  	if($item["icon"] != "")
			  		$list .= "<img src=rdb://{$item["icon"]}>\n";
	
				$list .= "Item: <highlight>{$item["name"]}<end>\n";
				if($item["minlvl"] != "")
					$list .= "MinLvl set to <highlight>{$item["minlvl"]}<end>\n";
				$list .= "<highlight>$added_players<end> Total ($add/$rem)\n";
			  	$list .= "Players added:";
				if(count($item["users"]) > 0)
					foreach($item["users"] as $key => $value)
					  	$list .= " [<highlight>$key<end>]";
				else
					$list .= " None added yet.";
				
				$list .= "\n\n";
			}
			$msg = bot::makeLink("Raidloot List", $list);
		} else
			$msg = "No List exists yet.";	
	} else {
		bot::send("No list available!");
		return;
	}

	bot::send($msg);
} else
	$syntax_error = true;
?>