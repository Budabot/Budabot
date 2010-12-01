<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Rolling the loot
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.03.2006
   ** Date(last modified): 05.02.2007
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
global $loot_winners;
global $residual;

if (preg_match("/^flatroll$/i", $message) || preg_match("/^rollloot$/i", $message) || preg_match("/^result$(i", $message) || preg_match("/^win$/i", $message)) {
	//Check if a loot list exits
  	if (!is_array($loot)) {
	    $msg = "There is nothing to roll atm.";
	    bot::send($msg, $sendto);
	    return;
	}
  	
  	$list = "<header>::::: Win List :::::<end>\n\n";
  	//Roll the loot
	$resnum = 1;
	forEach ($loot as $key => $item) {
  	  	$list .= "Item: <orange>{$item["name"]}<end>\n";
  	  	$list .= "Winner(s): ";
	    $users = count($item["users"]);
	 	if ($users == 0) {
	 		$list .= "<highlight>None added.<end>\n\n";
			$residual[$resnum]["name"] = $item["name"];
			$residual[$resnum]["icon"] = $item["icon"];
			$residual[$resnum]["linky"] = $item["linky"];
			$residual[$resnum]["multiloot"] = $item["multiloot"];
			$resnum++;
	 	} else {
			if ($item["multiloot"]>1) {
				if ($item["multiloot"] > sizeof($item["users"])) {
					$arrolnum = sizeof($item["users"]);
				} else {
					$arrolnum = $item["multiloot"];
				}

				for ($i = 0; $i < $arrolnum; $i++) {
					$winner = array_rand($item["users"], 1);
					unset($item["users"][$winner]);
					$list .= "<red>$winner<end> ";
				}

				if ($arrolnum<$item["multiloot"]) {
					$newmultiloot = $item["multiloot"]-$arrolnum;
					$residual[$resnum]["name"] = $item["name"];
					$residual[$resnum]["icon"] = $item["icon"];
					$residual[$resnum]["linky"] = $item["linky"];
					$residual[$resnum]["multiloot"] = $newmultiloot;
					$resnum++;
				}
			} else {
            	$winner = array_rand($item["users"], 1);
				$list .= "<red>$winner<end>";
			}
			$list .= "\n\n";
		}
	}
	//Reset loot
	$winner = "";
	$arrolnum = "";
	$loot = "";
	//Show winner list
	$msg = bot::makeLink("Winner List", $list);
	if (is_array($residual)) {
		$rerollmsg = " (There are item(s) left to be rolled. To re-add, type <symbol>reroll)";
	} else {
		$rerollmsg = "";
	}

	bot::send($msg.$rerollmsg, 'priv');

	if ($type != 'priv') {
		bot::send($msg.$rerollmsg, $sendto);
	}
}

?>