<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Creates a Bior Order
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 24.02.2006
   ** Date(last modified): 09.03.2006
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

if (count($chatBot->data['bior']) == 0) {
	$msg = "No Adventurer, Keeper, Enforcer or Engineer 201+ in chat.";
} else {
  	$chatBot->data['blist'] = array();
	$info  = "<header>::::: Info about Bio Regrowth macro :::::<end>\n\n";
	$info .= "The bot has it's own Bio Regrowth macro to use it just do ";
	$info .= "<symbol>b in the chat. \n\n";
	$info .= "<a href='chatcmd:///macro BR_Macro /g <myname> <symbol>b'>Click here to make an Bio Regrowth macro </a>";
	$info = Text::make_blob("Info", $info);

  	//Create Bio Regrowth Order
	forEach ($chatBot->data['bior'] as $key => $value) {
	  	if ($chatBot->data['bior_caller'] == $key) {
			$list[(sprintf("%03d", "300").$key)] = $key;
	  	} else if ($chatBot->data['bior'][$key]["b"] == "ready") {
			$list[(sprintf("%03d", (220 - $chatBot->data['bior'][$key]["lvl"])).$key)] = $key;
		} else {
			$list[(sprintf("%03d", "250").$key)] = $key;
		}
  	}

	$num = 0;
	ksort($list);
	reset($list);
  	$msg = "Bio Regrowth Order($info):";
	forEach ($list as $player) {
	  	if ($chatBot->data['bior'][$player]["b"] == "ready") {
	  		$status = "<green>*ready*<end>";
	  	} else if (($chatBot->data['bior'][$player]["b"] - time()) > 300) {
	  		$status = "<red>running<end>";
	  	} else {
		    $rem = $chatBot->data['bior'][$player]["b"] - time();
			$mins = floor($rem / 60);
			$secs = sprintf("%02d", $rem - ($mins * 60));
		    $status = "<orange>$mins:$secs<end>";
		}
		$num++;
		$msg .= " [$num. <highlight>$player<end> $status]";
        $chatBot->data['blist'][] = $player;
        if ($num >= $chatBot->settings["bior_max"]) {
        	break;
		}
	}

  	//Send Blist
  	forEach ($chatBot->data['blist'] as $player) {
		$chatBot->send($msg, $player);
	}
}
$chatBot->send($msg, $sendto);
?>