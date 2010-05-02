<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Sets APF Ship timer
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 22.03.2006
   ** Date(last modified): 24.03.2006
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

if(eregi("^setship$", $message)) {
  	bot::savesetting("shiptimer", time());
	$msg = "Shiptimer has been updated.";
  	bot::send($msg, $sender);  	
} elseif(eregi("^setship 0$", $message)) {
  	if($this->settings["server_status"] == "down") {
	    bot::send("Server is down. No correction possible!", $sender);
	    return;
	}
	  
	if($this->settings["shiptimer_status"] == "open") {
	    bot::send("Shiptimer isn�t locked atm.", $sender);
	    return; 
	}
	bot::savesetting("shiptimer_status", "open");
	bot::send("Shiptimer is now unlocked.", $sender);
} elseif(eregi("^setship \+([0-9]+)$", $message, $arr) || eregi("^setship ([0-9]+)$", $message, $arr)) {
  	$ctime = $arr[1]*60;
	if($arr[1] > 10) {
	  	$msg = "You can�t change the timer for more then 10minutes!";
	  	bot::send($msg, $sender);
	  	return;
	}
	$this->settings["shiptimer"] += $ctime;
  	bot::savesetting("shiptimer", $this->settings["shiptimer"]);
	$msg = "Shiptimer has been updated.";
  	bot::send($msg, $sender);
} elseif(eregi("^setship -([0-9]+)$", $message, $arr)) {
  	$ctime = $arr[1] * 60;
	if($arr[1] > 10) {
	  	$msg = "You can�t change the timer for more then 10minutes!";
	  	bot::send($msg, $sender);
	  	return;
	}
	$this->settings["shiptimer"] -= $ctime;
	bot::savesetting("shiptimer", $this->settings["shiptimer"]);	
	$msg = "Shiptimer has been updated.";
  	bot::send($msg, $sender);
} else
	$syntax_error = true;

?>