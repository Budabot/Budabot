<?
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
   
if(eregi("^leaderecho on$", $message)) {
	bot::savesetting("leaderecho", "1");
	bot::send("Raidleader echo has been <green>enabled<end>");
} elseif(eregi("^leaderecho off$", $message)) {
	bot::savesetting("leaderecho", "0");
	bot::send("Raidleader echo has been <green>disabled<end>");	
} elseif(eregi("^leaderecho$", $message)) {
	if($this->settings["leaderecho"] == 1)
		$msg = "Leaderecho is currently <green>enabled<end>";
	else
		$msg = "Leaderecho is currently <red>disabled<end>";
	bot::send($msg);
} else
	$syntax_error = true;
?>