<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Gives out points for the players on the raidlist
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

global $raidlist;
global $raid_points;
if(eregi("^raidpoints$", $message)) {
	if($this->vars["raid_status"] == "") {
		$msg = "<red>No Raid started.<end>";
		bot::send($msg);
		return;
	}
	
	if(count($raidlist) == 0) {
	  	bot::send("<red>No one is on the raidlist atm.<end>");
	  	return;
	}
	
	if($this->vars["raid_pts"] == 0) {
	  	$msg = "<red>This raid is specified as flatrolled only.<end>";
	  	bot::send($msg);
	  	return;		
	}

	if($raid_points) {
	  	$msg = "<red>Points were already given!<end>";
	  	bot::send($msg);
	  	return;
	}

	$db->query("SELECT * FROM points_db_<myname>");
	while($row = $db->fObject())
		$exists[$row->name] = true;
		
	$db->beginTransaction();
	$list = "<header>::::: Raidpoints :::::<end>\n\n";
	$list .= "The following players got {$this->vars["raid_pts"]}pts added.\n\n";
  	foreach($raidlist as $key => $value) {
  		$list .= "<tab>- <highlight>$key<end>\n";
  		if(isset($exists[$key]))
	  		$db->query("UPDATE `points_db_<myname>` SET `points` = `points` + {$this->vars["raid_pts"]} WHERE `name` = '$key'");
		else
			$db->query("INSERT INTO `points_db_<myname>` (`name`, `points`) VALUES ('$key', {$this->vars["raid_pts"]})");	  		
  	}
	$db->Commit();

  	$msg = bot::makeLink("Raidpoints", $list);
  	bot::send($msg);
  	$raid_points = true;
  	$db->query("UPDATE raids_settings_<myname> SET `next_spawn` = ".time()." + `spawntime` WHERE `raid_name` = '{$this->vars["raid_status"]}'");
} else
	$syntax_error = true;  	
?>