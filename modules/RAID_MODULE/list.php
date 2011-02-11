<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Show a loot list
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 30.01.2007
   ** 
   ** Copyright (C) 2006, 2007 Carsten Lohmann
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

if (preg_match("/^list$/i", $message)) {
	if ($this->vars["raid_status"] == "") {
	  	if (is_array($loot)) {
		  	$list = "<header>::::: Loot List :::::<end>\n\nUse <symbol>flatroll or <symbol>rollloot to roll.\n\n";
			forEach ($loot as $key => $item) {
				$add = Text::make_link("Add", "/tell <myname> add $key", "chatcmd");
				$rem = Text::make_link("Remove", "/tell <myname> add 0", "chatcmd");
				$added_players = count($item["users"]);
	
				$list .= "<u>Slot #<font color='#FF00AA'>$key</font></u>\n";
			  	if ($item["icon"] != "") {
			  		$list .= "<img src=rdb://{$item["icon"]}>\n";
				}

				if ($item["multiloot"] > 1) {
					$ml = " <yellow>(x".$item["multiloot"].")<end>";
				} else {
					$ml = "";
				}
				
				if ($item["linky"]) {
					$itmnm = $item["linky"];
				} else {
					$itmnm = $item["name"];
				}
	
				$list .= "Item: <orange>$itmnm<end>".$ml."\n";
				if ($item["minlvl"] != "") {
					$list .= "MinLvl set to <highlight>{$item["minlvl"]}<end>\n";
				}
								
				$list .= "<highlight>$added_players<end> Total ($add/$rem)\n";
			  	$list .= "Players added:";
				if (count($item["users"]) > 0) {
					forEach ($item["users"] as $key => $value) {
					  	$list .= " [<yellow>$key<end>]";
					}
				} else {
					$list .= " None added yet.";
				}
				
				$list .= "\n\n";
			}
			$msg = Text::make_link("Loot List", $list);
		} else {
			$msg = "No List exists yet.";
		}
	} else if ($this->vars["raid_status"] != "" && $this->vars["raid_loot_pts"] == 0) {
	  	if (is_array($raidloot)) {
		  	$list = "<header>::::: Raidloot List :::::<end>\n\n";
			forEach ($raidloot as $key => $item) {
				$add = Text::make_link("Add", "/tell <myname> add $key", "chatcmd");
				$rem = Text::make_link("Remove", "/tell <myname> add 0", "chatcmd");
				$added_players = count($item["users"]);
	
				$list .= "<u>Slot #$key</u>\n";
			  	if ($item["icon"] != "") {
			  		$list .= "<img src=rdb://{$item["icon"]}>\n";
				}
	
				$list .= "Item: <highlight>{$item["name"]}<end>\n";
				if ($item["minlvl"] != "") {
					$list .= "MinLvl set to <highlight>{$item["minlvl"]}<end>\n";
				}
				$list .= "<highlight>$added_players<end> Total ($add/$rem)\n";
			  	$list .= "Players added:";
				if (count($item["users"]) > 0) {
					forEach ($item["users"] as $key => $value) {
					  	$list .= " [<highlight>$key<end>]";
					}
				} else {
					$list .= " None added yet.";
				}
				
				$list .= "\n\n";
			}
			$msg = Text::make_link("Raidloot List", $list);
		} else {
			$msg = "No List exists yet.";
		}
	} else {
		bot::send("No list available!");
		return;
	}

	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>