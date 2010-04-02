<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Creates a command to check if all players of the raidlist are in the area
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 10.10.2006
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

if(eregi("^raidcheck$", $message)) {
	if($this->vars["raid_status"] != 13 && $this->vars["raid_status"] != 28 && $this->vars["raid_status"] != 35) {
		$msg = "No Raid started.";
		bot::send($msg);
		return;
	}
	
	global $raidlist;
	$list = "<header>::::: Check who is here from the raidlist :::::<end>\n\n";
	foreach($raidlist as $key => $value)
		$content .= " \\n /assist $key";

  	$list .= "<a href='chatcmd:///text Raidcheck: $content'>Click here to check who is here</a>";
  	$msg = bot::makeLink("Raidcheck", $list);
  	bot::send($msg);
} else 
	$syntax_error = true;
?>