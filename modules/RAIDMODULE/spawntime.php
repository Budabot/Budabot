<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the spawntime of a Mob
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 22.11.2006
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

if(eregi("^spawntime$", $message)) {
 	$db->query("SELECT * FROM raids_settings_<myname> WHERE `status` = 1 AND `spawntime` != 0");
	if($db->numrows() == 0) {
		$msg = "<red>No spawntimes registered!<end>";
		if($type == "msg")
			bot::send($msg, $sender);
		elseif($type == "priv")
			bot::send($msg);
		return;
	}

	$list .= "<header>::::: Spawntimes :::::<end>\n\n";
	while($row = $db->fObject()) {
		$list .= "Name: $row->raid_name\n";
		$list .= "Status: ";
		if($row->next_spawn <= time())
			$list .= "<green>Ready<end>\n";
		else {
			$timeleft = $row->next_spawn - time();
			$mins = floor($timeleft / 60);
			$hours = floor($mins / 60);
			$mins = floor($mins - ($hours * 60));
		
			if($mins < 10)
				$mins = "0".$mins;
			
			if($hours == 0)
				$hours = "00";
			else
				$hours = "0".$hours;
			
			$list .= "<red>Spawn in {$hours}hrs and {$mins}mins<end>\n";
		}		
		$list .= bot::makeLink("Start this Raid", "/g <myname> <symbol>raidstart $row->shortform", "chatcmd");
		$list .= "\n\n";
	}
	
	$msg = bot::makeLink("Spawntimes", $list);
	if($type == "msg")
		bot::send($msg, $sender);
	elseif($type == "priv")
		bot::send($msg);
} else
	$syntax_error = true;
?>