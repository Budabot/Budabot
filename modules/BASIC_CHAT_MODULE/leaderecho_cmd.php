<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Enable/Disable raidleader echo
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 02.02.2007
   ** Date(last modified): 02.02.2007
   ** 
   ** Copyright (C) 2007 Carsten Lohmann
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
   
if (preg_match("/^leaderecho on$/i", $message)) {
	bot::savesetting("leaderecho", "1");
	bot::send("Leader echo has been <green>enabled<end>");
} else if (preg_match("/^leaderecho off$/i", $message)) {
	bot::savesetting("leaderecho", "0");
	bot::send("Leader echo has been <green>disabled<end>");	
} else if (preg_match("/^leaderecho$/i", $message)) {
	if ($this->settings["leaderecho"] == 1) {
		$msg = "Leader echo is currently <green>enabled<end>";
	} else {
		$msg = "Leader echo is currently <red>disabled<end>";
	}
	bot::send($msg, 'priv');
} else {
	$syntax_error = true;
}

?>