<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Handles autoupdates of apftimers
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 25.03.2006
   ** Date(last modified): 02.02.2007
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

if($sender == $this->settings["apftimerbot"]) {
  	if(eregi("^setpf nc$", $message)) {
		bot::savesetting("pftimer_status", "no_correction");
	} elseif(eregi("^setpf ([0-9]{9,12})$", $message, $arr)) {
	  	bot::savesetting("pftimer", $arr[1]);
	  	bot::savesetting("pftimer_status", "open");  	
	  	$msg = "Pftimer has been updated.";
	  	bot::send($msg, "guild");
		bot::send($msg);
	} elseif(eregi("^setship nc$", $message)) {
		bot::savesetting("shiptimer_status", "no_correction");
	} elseif(eregi("^setship ([0-9]{10,12})$", $message, $arr)) {
	  	bot::savesetting("shiptimer", $arr[1]);
	  	bot::savesetting("shiptimer_status", "open"); 	
	  	$msg = "Shiptimer has been updated.";
	  	bot::send($msg, "guild");
		bot::send($msg);
	}
	$restricted = true;
}
?>