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
if (count($this->vars["Timers"]) == 0) {
	return;
}

forEach ($this->vars["Timers"] as $key => $timer) {
	$msg = "";

	$tleft = $timer->timer - time();
	$set_time = $timer->settime;
	$name = $timer->name;
	$owner = $timer->owner;
	$mode = $timer->mode;
	
	if ($timer->callback != '') {
		call_user_func($timer->callback, $timer->callback_param);
		return;
	}

	if ($tleft >= 3599 && $tleft < 3601 && ((time() - $set_time) >= 30)) {
		if ($name == "PrimTimer") {
			$msg = "Reminder: Timer has <highlight>1 hour<end> left [set by <highlight>$owner<end>]";
		} else {
			$msg = "Reminder: Timer <highlight>$name<end> has <highlight>1 hour<end> left [set by <highlight>$owner<end>]";
		}
	} else if ($tleft >= 899 && $tleft < 901 && ((time() - $set_time) >= 30)) {
		if ($name == "PrimTimer") {
			$msg = "Reminder: Timer has <highlight>15 minutes<end> left [set by <highlight>$owner<end>]";
		} else {
			$msg = "Reminder: Timer <highlight>$name<end> has <highlight>15 minutes<end> left [set by <highlight>$owner<end>]";
		}
	} else if ($tleft >= 59 && $tleft < 61 && ((time() - $set_time) >= 30)) {
		if ($name == "PrimTimer") {
			$msg = "Reminder: Timer has <highlight>1 minute<end> left [set by <highlight>$owner<end>]";
		} else {
			$msg = "Reminder: Timer <highlight>$name<end> has <highlight>1 minute<end> left [set by <highlight>$owner<end>]";
		}
	} else if ($tleft <= 0) {
		if ($tleft >= -600) {
			if ($name == "PrimTimer") {
				$msg = "<highlight>$owner<end> your timer has gone off";
			} else {
				$msg = "<highlight>$owner<end> your timer named <highlight>$name<end> has gone off";
			}
		}
	
		unset($this->vars["Timers"][$key]);
		$db->query("DELETE FROM timers_<myname> WHERE `name` = '" . str_replace("'", "''", $name) . "' AND `owner` = '$owner'");
	}

	if ('' != $msg) {
		if ('msg' == $mode) {
			bot::send($msg, $owner);
		} else {
			bot::send($msg, $mode);
		}
	}
}

?>