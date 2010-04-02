<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Sets a minlvl for a slot
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.03.2006
   ** Date(last modified): 03.03.2006
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
//Remove a minlvl from a slot
if(eregi("^setminlvl ([0-9]+) 0$", $message, $arr)) {
  	$slot = $arr[1];
	$minlvl = $arr[2];
	
  	if(!isset($loot[$slot])) {
  		$msg = "Sry but the slot <highlight>#$slot<end> doesn´t exist.";
		if($type == "msg")
		  	bot::send($msg, $sender);
		elseif($type == "priv")
	  		bot::send($msg);
	  	return;
  	}

    unset($loot[$slot]["minlvl"]);
    $msg = "You have removed the minlvl req. from Slot <highlight>#$slot<end>.";

    if($type == "msg")
 		bot::send($msg, $sender);
	elseif($type == "priv")
  		bot::send($msg);
//Sets a minlvl for a slot
} elseif(eregi("^setminlvl ([0-9]+) ([0-9]+)$", $message, $arr)) {
  	$slot = $arr[1];
	$minlvl = $arr[2];
	
	if($minlvl > 220) {
		$msg = "You need to choose a lvl between 1 and 220.";
		if($type == "msg")
		  	bot::send($msg, $sender);
		elseif($type == "priv")
	  		bot::send($msg);
	  	return;
	}
	
  	if(!isset($loot[$slot])) {
  		$msg = "Sry but the slot <highlight>#$slot<end> doesn´t exist.";
		if($type == "msg")
		  	bot::send($msg, $sender);
		elseif($type == "priv")
	  		bot::send($msg);
	  	return;
  	}

    $loot[$slot]["minlvl"] = $minlvl;
    $msg = "You have changed the minlvl for Slot <highlight>#$slot<end> to <highlight>$minlvl<end>.";

    if($type == "msg")
 		bot::send($msg, $sender);
	elseif($type == "priv")
  		bot::send($msg);
} else
	$syntax_error = true;
?>