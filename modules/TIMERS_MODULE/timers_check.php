<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Checks the running timers
   ** Version: 1.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 26.12.2005
   ** Date(last modified): 21.11.2006
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann
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

//Check if at least one timer is running
if(count($this->vars["Timers"]) == 0)
	return;

foreach($this->vars["Timers"] as $key => $value) {
   	$msg = "";

	$tleft = $this->vars["Timers"][$key]["timer"] - time();
	$set_time = $this->vars["Timers"][$key]["settime"];
	$name = $this->vars["Timers"][$key]["name"];
	$owner = $this->vars["Timers"][$key]["owner"];
	$mode = $this->vars["Timers"][$key]["mode"];
	
	if($tleft <= 0 && $tleft >= -600) {
		if($name == "PrimTimer")
			$msg = "<highlight>$owner<end> your timer has gone off";
		else
			$msg = "<highlight>$owner<end> your timer named <highlight>$name<end> has gone off";
	
		unset($this->vars["Timers"][$key]);
		$db->query("DELETE FROM timers_<myname> WHERE `name` = \"$name\" AND `owner` = '$owner'");
	} elseif ($tleft <= 0) {
		unset($this->vars["Timers"][$key]);
		$db->query("DELETE FROM timers_<myname> WHERE `name` = \"$name\" AND `owner` = '$owner'");
	} elseif ($tleft >= 3599 && $tleft < 3601 && ((time() - $set_time) >= 30)) {
		if($name == "PrimTimer")
			$msg = "Reminder: Timer has <highlight>1hour<end> left [set by <highlight>$owner<end>]";
		else
			$msg = "Reminder: Timer <highlight>$name<end> has <highlight>1hour<end> left [set by <highlight>$owner<end>]";
	} elseif ($tleft >= 899 && $tleft < 901 && ((time() - $set_time) >= 30)) {
		if($name == "PrimTimer")
			$msg = "Reminder: Timer has <highlight>15minutes<end> left [set by <highlight>$owner<end>]";
		else
			$msg = "Reminder: Timer <highlight>$name<end> has <highlight>15minutes<end> left [set by <highlight>$owner<end>]";
	} elseif ($tleft >= 59 && $tleft < 61 && ((time() - $set_time) >= 30)) {
		if($name == "PrimTimer")
			$msg = "Reminder: Timer has <highlight>1minute<end> left [set by <highlight>$owner<end>]";
		else
			$msg = "Reminder: Timer <highlight>$name<end> has <highlight>1minute<end> left [set by <highlight>$owner<end>]";
	}

	if($mode == "guild" && $msg != "")
	   	bot::send($msg, "guild");
	elseif($mode == "msg" && $msg != "")
		bot::send($msg, $owner);
	elseif($mode == "priv" && $msg != "")
		bot::send($msg);
		
}
?>