<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows reminders on APF opening times
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 22.03.2006
   ** Date(last modified): 10.12.2006
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

if($this->settings["pftimer"] != 0 && $this->settings["pftimer_status"] != "no_correction") {
	$msg = "";
	$time = $this->settings["pftimer"];
	$timeraid = 7*60*60+12*60;
	$raids = floor((time() - $time) / $timeraid);
	$timeleft = ($time + $timeraid + $timeraid * $raids) - mktime();

	if($timeleft <= 3601 && $timeleft >= 3599) {
	  	$msg = "<red>APF Gates are opening in <highlight>1<end> hour!";
	} elseif($timeleft <= 1801 && $timeleft >= 1799) {
	  	$msg = "<red>APF Gates are opening in <highlight>30<end> minutes!";	  
	} elseif($timeleft <= 901 && $timeleft >= 899) {
	  	$msg = "<red>APF Gates are opening in <highlight>15<end> minutes!";	  
	} elseif($timeleft <= 301 && $timeleft >= 299) {
	  	$msg = "<red>APF Gates are opening in <highlight>5<end> minutes!";	  
	} elseif($timeleft <= 2) {
	  	$msg = "<red>APF Gates are opening <highlight>NOW<end>!";	  
	}

	if($msg) {
	    bot::send($msg, NULL, true);
	    bot::send($msg, "guild", true);
	}
}

if($this->settings["server_status"] == "down" && $this->settings["pftimer_status"] == "open")
	bot::savesetting("pftimer_status", "no_correction");
?>